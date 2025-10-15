<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\admin\models\User;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\controllers\traits\ResponseTrait;
use app\module\api\module\viber\exceptions\NotFoundException;
use app\module\api\module\viber\exceptions\ValidationException;
use app\module\api\module\viber\interfaces\SenderInterface;
use app\module\api\module\viber\models\ViberChat;
use app\module\api\module\viber\models\ViberChatViberUser;
use app\module\api\module\viber\models\ViberMessage;
use app\module\api\module\viber\models\ViberMessageViberUser;
use app\module\api\module\viber\models\ViberUser;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Throwable;
use yii\base\InvalidConfigException;
use yii\BaseYii as Yii;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;

class ViberSender
{
    use ResponseTrait;

    public static $broadcast_url = 'https://chatapi.viber.com/pa/broadcast_message';
    public static $send_message_url = 'https://chatapi.viber.com/pa/send_message';

    /**
     * @param array $data
     * @param int $chat_id
     * @param array $additional
     * @return array|null
     */
    public static function sendMessage(array $data, $chat_id = 1, array $additional = [])
    {
        try {
            $chat = $chat_id instanceof ViberChat ? $chat_id : ViberChat::getChat($chat_id);
            if (!$chat) throw new ValidationException(['chat_id' => [T::t('validation', 'Chat is not found.')]]);

            if (empty($data['to']) || !is_string($data['to'])) throw new ValidationException(['to' => ["This field must be string."]]);
            /** @var ViberUser $user */
            $user = self::getUser($chat, $data['to']);

            self::setLanguage($user);

            self::runSender($user->viber_id, $chat, $data, $additional);

            return self::letterSent();
        } catch (Throwable $e) {
            Helper::log([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'viber');
            return self::handleException($e);
        }
    }

    /**
     * @param $users
     * @param $chat
     * @param array $data
     * @param $additional
     * @return array|null
     */
    public static function runSender($users, $chat, array $data, $additional)
    {
        $senders = Helper::config('senders');
        foreach ($data as $type => $message) {
            if (array_key_exists($type, $senders) && ($sender = new $senders[$type]($chat)) instanceof SenderInterface) {
                /** @var SenderInterface $sender */
                $sender->send($users, $message, $additional);
            }
        }
    }

    /**
     * @param ViberChat $chat
     * @param $viberId
     * @return ViberUser
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws ValidationException
     */
    public static function getUser(ViberChat $chat, $viberId)
    {
        /** @var ViberUser $user */
        $user = $chat->getViberUsers()->where(['viber_id' => $viberId])->one();
        if (!$user) throw new NotFoundException('User is not found');
        $pivot = ViberChatViberUser::findOne(['viber_chat_id' => $chat->viber_chat_id, 'viber_user_id' => $user->viber_user_id]);
        if (empty($pivot->subscribed_at) || !empty($pivot->unsubscribed_at))
            throw new ValidationException(['to' => ["User is not subscribed to this chat."]]);
        return $user;
    }


    /**
     * This method broadcasts messages to viber users.
     * Param $data can contain such keys:
     *  - to - an array of viber ids, messages to be sent to;
     *  - message - string: a simple text message to be sent;
     *  - contact - array [
     *          'name' => 'John Smith',
     *          'phone_number' => "+972511123123"
     *       ]: a contact to be shared
     *
     * @param array $data
     * @param int $chat_id
     * @param array $additional
     * @return array
     */
    public static function broadcastMessage(array $data, $chat_id = 1, array $additional = [])
    {
        try {
            $chat = $chat_id instanceof ViberChat ? $chat_id : ViberChat::getChat($chat_id);
            if (!$chat) throw new ValidationException(['chat_id' => [T::t('validation', 'Chat is not found.')]]);

            $batches = (self::getUsers($data['to'] ?? null, $chat))->batch();

            foreach ($batches as $viberUsers) {
                $users = [];

                foreach ($viberUsers as $user) {
                    $users['viber_ids'][] = $user->viber_id;
                    $users['ids'][] = $user->viber_user_id;
                }

                if (empty($users)) return self::apiNotFoundResponse();

                self::runSender($users, $chat, $data, $additional);
            }

            return self::letterSent();
        } catch (Throwable $e) {

            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'viber');
            return self::apiErrorResponse($e);
        }
    }

    /**
     * @param ViberUser $v_u
     * @param ViberMessage|null $m
     * @param ViberChat $chat
     * @param array $additional
     * @throws Throwable
     */
    public static function send(ViberUser $v_u, ?ViberMessage $m, ViberChat $chat, array $additional = [])
    {
        try {
            $body = array_merge([
                'receiver' => $v_u->viber_id,
                'sender' => Helper::config('main.sender'),
            ], json_decode($m->message, true), $additional);

            if (!array_key_exists('tracking_data', $body)) $body['tracking_data'] = 'message';
            $body = json_encode($body);

            $r = new GuzzleRequest('post', self::$send_message_url, ['X-Viber-Auth-Token' => $chat->token], $body);
            $client = new Client();
            $response = json_decode($client->send($r)->getBody()->getContents(), true);

            if (!empty($response['message_token'])) {
                $m->update([
                    'message_token' => $response['message_token'],
                    'sent_at' => Helper::now()->getTimestamp(),
                    'chat_id' => $chat->viber_chat_id
                ]);
                $data = [
                    'viber_message_id' => $m->viber_message_id,
                    'viber_user_id' => $v_u->viber_user_id,
                ];
                $pivot = ViberMessageViberUser::findOne($data);
                if (!$pivot) ViberMessageViberUser::create($data);
            }
        } catch (Throwable $e) {
            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'viber');
            throw new Exception('', 0, $e);
        }

    }


    /**
     * @param $users
     * @param ViberMessage $m
     * @param $chat
     * @param array $additional
     * @throws GuzzleException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public static function broadcast($users, $m, $chat, array $additional = [])
    {
        $body = array_merge([
            'broadcast_list' => $users['viber_ids'],
            'sender' => Helper::config('main.sender'),
        ], json_decode($m->message, true), $additional);

        if (!array_key_exists('tracking_data', $body)) $body['tracking_data'] = 'message';

        $r = new GuzzleRequest('post', self::$broadcast_url, ['X-Viber-Auth-Token' => $chat->token], json_encode($body));
        $client = new Client();
        $response = json_decode($client->send($r)->getBody()->getContents(), true);
        if (!empty($response['message_token'])) {
            $m->update([
                'message_token' => $response['message_token'],
                'sent_at' => Helper::now()->getTimestamp(),
                'chat_id' => $chat->viber_chat_id
            ]);
            foreach ($users['ids'] as $id) {
                $data[] = [$m->viber_message_id, $id];
            }
            if (isset($data))
                ViberMessageViberUser::find()->createCommand()->batchInsert(
                    ViberMessageViberUser::tableName(),
                    ['viber_message_id', 'viber_user_id'],
                    $data
                );
        }
    }

    /**
     * @param ViberUser $user
     */
    public static function setLanguage(ViberUser $user)
    {
        if (!empty($user->language)) Yii::$app->language = $user->language;
    }

    /**
     * @param ViberUser $v_u
     * @param $chat_id
     * @param array $additional
     * @return array|null
     */
    public static function sendNotFoundMessage(ViberUser $v_u, $chat_id, array $additional = [])
    {
        $data = [
            'to' => $v_u->viber_id,
            'text' => T::t('viber', 'Nothing was found for your request.')
        ];
        return self::sendMessage($data, $chat_id, $additional);
    }

    /**
     * @param ViberChat $chat
     * @return ActiveQuery
     */
    public static function getAdminsOnly(ViberChat $chat)
    {
        return ViberUser::find()->joinWith('viberChatViberUser')
            ->joinWith('user')->where([User::tableName() . '.role' => User::ROLE_ADMIN])
            ->andWhere([ViberChatViberUser::tableName() . '.viber_chat_id' => $chat->viber_chat_id])
            ->andWhere(['NOT', [ViberChatViberUser::tableName() . '.subscribed_at' => null]])
            ->andWhere(['IS', ViberChatViberUser::tableName() . '.unsubscribed_at', null]);

    }

    /**
     * @param $to
     * @param $chat
     * @return ActiveQuery
     */
    private static function getUsers($to, $chat)
    {
        return empty($to) ? self::getAdminsOnly($chat) :
            ViberUser::find()->joinWith('viberChatViberUser')
                ->joinWith('user')->where([User::tableName() . '.role' => User::ROLE_ADMIN])
                ->andWhere([ViberChatViberUser::tableName() . '.viber_chat_id' => $chat->viber_chat_id])
                ->andWhere(['NOT', [ViberChatViberUser::tableName() . '.subscribed_at' => null]])
                ->andWhere(['IS', ViberChatViberUser::tableName() . '.unsubscribed_at', null]);
    }
}