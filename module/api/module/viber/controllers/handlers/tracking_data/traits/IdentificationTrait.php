<?php

namespace app\module\api\module\viber\controllers\handlers\tracking_data\traits;

use app\module\api\module\viber\components\Additional;
use app\module\api\module\viber\components\Button;
use app\module\api\module\viber\components\Keyboard;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\controllers\senders\ViberSender;
use ReflectionException;
use Yii;

trait IdentificationTrait
{
    public $next_t_d = 'identification';

    /**
     * @return bool
     * @throws ReflectionException
     */
    public function cantIdentifyUserMessage(): bool
    {
        $message = T::t('viber', "Sorry, we cannot find you in our database. Please, register on our website and try again.")
            . "\n\n" . Helper::SITE_URL;
        return $this->sendTryAgainMessage($message, $this->next_t_d);
    }

    /**
     * @param $message
     * @param string $t_data
     * @return bool
     * @throws ReflectionException
     */
    public function sendTryAgainMessage($message, $t_data = '')
    {
        $additional = new Additional(['tracking_data' => $this->next_t_d]);
        $keyboard = new Keyboard();
        if ($this->v_u->api_version >= 4) {
            $additional->setOption('min_api_version', 4);
            $keyboard->setOption('InputFieldState', 'hidden');
        }
        $text = Helper::config('main.messages.new_conversation')["-1"] ?? T::t('viber', 'Try again');

        $keyboard->attach(new Button([
            "ActionBody" => "-1",
            "Text" => $text,
        ]));
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
}