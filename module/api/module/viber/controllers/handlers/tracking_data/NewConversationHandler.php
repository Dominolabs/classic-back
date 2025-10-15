<?php


namespace app\module\api\module\viber\controllers\handlers\tracking_data;


use app\module\admin\models\User;
use app\module\api\module\viber\components\Additional;
use app\module\api\module\viber\components\Button;
use app\module\api\module\viber\components\ConfirmationKeyboard;
use app\module\api\module\viber\components\Keyboard;
use app\module\api\module\viber\controllers\handlers\events\ConversationStartedHandler;
use app\module\api\module\viber\controllers\handlers\tracking_data\traits\IdentificationTrait;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\controllers\senders\ViberSender;
use app\module\api\module\viber\interfaces\HandlerInterface;
use app\module\api\module\viber\models\ViberMessage;
use ReflectionException;
use Yii;
use yii\validators\EmailValidator;

class NewConversationHandler extends TrackingDataHandler implements HandlerInterface
{
    use IdentificationTrait;

//    public $next_t_d = 'identification';

    /**
     * @param ViberMessage $v_m
     * @return bool|void
     * @throws ReflectionException
     */
    public function run($v_m)
    {
        $message = json_decode($v_m->message, true);
        $this->v_m = $v_m;
        if (empty($message)) return;
        if ($v_m->type === 'contact') {
            $phone = preg_replace('/[^0-9]/', '', $message['contact']['phone_number'] ?? '');
            $users = User::where(['phone' => $phone])->all();
            if ($this->userFound($users)) return;
        }
        if ($message['type'] === 'text' && $message['text'] === ConversationStartedHandler::$action_body) return $this->usePhoneMessage();

        if ((new EmailValidator())->validate($message['text'])) {
            $users = User::where(['email' => $message['text']])->all();
        } else {
            $users = User::where(['phone' => $message['text']])->all();
        }
        if ($this->userFound($users)) return;
        return $this->cantIdentifyUserMessage();
    }

    /**
     * @param $users
     * @return bool
     * @throws ReflectionException
     */
    private function userFound($users)
    {
        if (empty($users) || !is_array($users)) return false;
        if (count($users) === 1) return $this->confirmUser($users[0]);
        return $this->sendUserListMessage($users);
    }

    /**
     * @param $users
     * @return bool
     * @throws ReflectionException
     */
    private function sendUserListMessage($users): bool
    {
        $additional = new Additional(['tracking_data' => $this->next_t_d]);
        $keyboard = new Keyboard();
        if ($this->v_u->api_version >= 4) {
            $additional->setOption('min_api_version', 4);
            $keyboard->setOption('InputFieldState', 'hidden');
        }
        /** @var User $user */
        foreach ($users as $user) {
            $text = $user->getFullName() . "<br>";
            if (!empty($user->email)) {
                $email = "<b>Email:</b> <i>$user->email</i>";
                if ($this->v_u->api_version < 4) $text .= $email;
                else $text .= '<font size=”12”>' . $email . '</font>';
            }
            $keyboard->attach(new Button([
                "ActionBody" => (string)$user->id,
                "Text" => $text,
            ]));
        }
        $keyboard->attach(new Button([
            "ActionBody" => "-2",
            "Text" => T::t('viber', 'Not in the list'),
        ]));
        $mes = T::t('viber', 'Please, choose your name from the list below or press "Not in the list" if you don\'t see your name.');
        $additional->attach('keyboard', $keyboard);

        $data = [
            'to' => $this->v_u->viber_id,
            'text' => $mes
        ];
        ViberSender::sendMessage(
            $data,
            $this->v_m->viber_chat_id,
            $additional->makeArray());
        return true;
    }

    /**
     * This method is used to send message to user, who use desktop version of viber to share phone.
     */
    private function usePhoneMessage()
    {
        $data = [
            'to' => $this->v_u->viber_id,
            'text' => T::t('viber', 'Use phone for identification by phone number, please.')
        ];
        ViberSender::sendMessage(
            $data,
            $this->v_m->viber_chat_id,
            array_merge(ConversationStartedHandler::getKeyboard(), ['tracking_data' => 'new_conversation']));
    }

    /**
     * @param User $user
     * @return bool
     * @throws ReflectionException
     */
    private function confirmUser($user)
    {
        $additional = new Additional(['tracking_data' => $this->next_t_d]);
        $keyboard = new ConfirmationKeyboard([
            'yesActionBody' => (string)$user->user_id,
            'noActionBody' => "0",
        ]);

        if ($this->v_u->api_version >= 4) {
            $additional->setOption('min_api_version', 4);
            $keyboard->setOption('InputFieldState', 'hidden');
        }
        $message = $this->constructConfirmationMessage($user);
        $additional->attach('keyboard', $keyboard);

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

    /**
     * @param User $user
     * @return string
     */
    private function constructConfirmationMessage($user)
    {
        $m = T::t('viber', "Is this your data?") . "\n\n"
            . T::t('viber', 'Name:') . (empty($user->name) ? $user->username : $user->name) . "\n";
        if (!empty($user->phone))
            $m .= T::t('viber', 'Phone:') . $user->phone . "\n";
        if (!empty($user->email))
            $m .= T::t('viber', 'Email:') . $user->email . "\n";
        return $m;
    }
}