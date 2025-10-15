<?php


namespace app\module\api\module\viber\components;



use ReflectionException;

class ConfirmationKeyboard extends Keyboard
{
    /**
     * ConfirmationKeyboard constructor.
     * @param $config
     * @throws ReflectionException
     */
    public function __construct($config)
    {
        $this->attach(new YesButton([
            "ActionBody" => (string) $config['yesActionBody'] ?? '',
        ]));

        $this->attach(new NoButton([
            "ActionBody" => (string) $config['noActionBody'] ?? '',
        ]));
    }
}