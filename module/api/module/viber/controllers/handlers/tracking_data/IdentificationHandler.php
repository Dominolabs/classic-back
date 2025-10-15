<?php


namespace app\module\api\module\viber\controllers\handlers\tracking_data;


use app\module\admin\models\User;
use app\module\api\module\viber\components\Additional;
use app\module\api\module\viber\components\Button;
use app\module\api\module\viber\components\Keyboard;
use app\module\api\module\viber\controllers\handlers\events\ConversationStartedHandler;
use app\module\api\module\viber\controllers\handlers\tracking_data\traits\IdentificationTrait;
use app\module\api\module\viber\controllers\handlers\traits\NewConversationTrait;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\controllers\senders\ViberSender;
use app\module\api\module\viber\interfaces\HandlerInterface;
use ReflectionException;
use Throwable;
use yii\db\StaleObjectException;

class IdentificationHandler extends TrackingDataHandler implements HandlerInterface
{
    use IdentificationTrait, NewConversationTrait;

    /**
     * @param $v_m
     * @return bool|void
     * @throws ReflectionException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function run($v_m)
    {
        $message = json_decode($v_m->message, true);
        $this->v_m = $v_m;
        $messages = Helper::config('main.messages.new_conversation');
        if (empty($message)) return;
        if ($message['type'] === 'text') {
            if ($message['text'] === '-1') {
                $text = $messages["-1"] ?? T::t('viber', 'Try again');
                $this->setMessageText($text);
                $data = [
                    'to' => $this->v_u->viber_id,
                    'text' => $this->getNewConversationMessage()
                ];
                ViberSender::sendMessage(
                    $data,
                    $this->v_m->viber_chat_id,
                    array_merge(ConversationStartedHandler::getKeyboard(), ['tracking_data' => 'new_conversation']));
                return;
            } elseif (in_array($message['text'], array_keys($messages))) {
                $this->setMessageText($messages[$message['text']]);
                return $this->cantIdentifyUserMessage();
            }
            return $this->updateViberUser($message['text']);
        }
    }

    /**
     * @param $text
     * @throws StaleObjectException
     * @throws Throwable
     */
    private function setMessageText($text)
    {
        $message =  $message = json_decode($this->v_m->message, true);
        if (!empty($message)) {
            $message['text'] = $text;
            $this->v_m->update([
                'message' => json_encode($message)
            ]);
        }
    }

    /**
     * @param $user_id
     * @return bool|void
     * @throws Throwable
     * @throws StaleObjectException
     */
    private function updateViberUser($user_id)
    {
        $user = User::findOne($user_id);
        if (!$user) return $this->noUserFoundMessage();
        $this->setMessageText($user->getFullName() . ' Email: ' . $user->email);
        $this->v_u->update([
            'user_id' => $user->user_id
        ]);
        return $this->successfullyIdentifiedMessage($user);
    }

    /**
     * @return bool
     * @throws ReflectionException
     */
    private function noUserFoundMessage()
    {
        $message = T:: t('viber', "It seems, user you choose was removed. Please, register on our website and try again.")
            . "\n\n" . Helper::SITE_URL;
        return $this->sendTryAgainMessage($message, $this->next_t_d);
    }

    /**
     * @param User|null $user
     * @return bool
     * @throws ReflectionException
     */
    private function successfullyIdentifiedMessage(User $user)
    {
        $additional = new Additional(['tracking_data' => 'message']);
        $name = $user->getFullName();
        $name = empty($name) ? '.' : ", $name. ";
        $message = T::t('viber', 'Thank you for identifying') . $name . T::t('viber', 'Now you can chat with us.');

        $data = [
            'to' => $this->v_u->viber_id,
            'text' => $message
        ];
        ViberSender::sendMessage(
            $data,
            $this->v_m->viber_chat_id,
            $additional->makeArray());

        return true;
    }
}