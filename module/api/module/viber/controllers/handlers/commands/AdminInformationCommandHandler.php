<?php

namespace app\module\api\module\viber\controllers\handlers\commands;

use app\module\api\module\viber\components\Additional;
use app\module\api\module\viber\components\Button;
use app\module\api\module\viber\components\Keyboard;
use app\module\api\module\viber\controllers\handlers\tracking_data\TrackingDataHandler;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\interfaces\HandlerInterface;
use ReflectionException;

class AdminInformationCommandHandler extends TrackingDataHandler implements HandlerInterface
{
    public $next_t_d = 'admin_info';

    /**
     * @param $v_m
     * @return bool
     * @throws ReflectionException
     */
    public function run($v_m)
    {
        //Init
        $this->v_m = $v_m;
        $messages = Helper::config('main.messages.admin_info');
        $additional = new Additional(['tracking_data' => $this->next_t_d]);
        $keyboard = new Keyboard();
        if ($this->v_u->api_version >= 4) {
            $additional->setOption('min_api_version', 4);
            $keyboard->setOption('InputFieldState', 'hidden');
        }

        //Creating buttons
        $names = array_keys($messages);
        foreach ($names as $name) {
            $keyboard->attach(new Button([
                'ActionBody' => $name,
                'Text' => $messages[$name] ?? ''
            ]));
        }

        // Sending message
        $mes = T::t('viber', 'Please, choose information you want to receive.');

        $this->send(['text' => $mes], $additional->attach('keyboard', $keyboard)->makeArray());
        return true;
    }
}