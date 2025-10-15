<?php


namespace app\module\api\module\viber\controllers\handlers\tracking_data;


use app\module\admin\models\User;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\interfaces\HandlerInterface;
use app\module\api\module\viber\models\ViberCommandTranslation;
use app\module\api\module\viber\models\ViberMessage;

class TDMessageHandler extends TrackingDataHandler implements HandlerInterface
{
    public $commands = [];
    public $handlers = [];

    /**
     * TDMessageHandler constructor.
     * @param $v_u
     */
    public function __construct($v_u)
    {
        parent::__construct($v_u);
        $this->commands = $this->getAvailableCommands();
        $user = $this->v_u->user;
        if ($user) {
            $this->handlers = Helper::config('handlers.commands.' . $this->v_u->user->role) ?? [];
        }
    }

    /**
     * @param ViberMessage $v_m
     */
    public function run($v_m)
    {
        if ($v_m->type === 'text') {
            $message = json_decode($v_m->message, true);
            if (!empty($message['text']) && in_array($text = trim(mb_strtolower($message['text'])), $this->commands)) {
                /** @var ViberCommandTranslation $translation */
                $translation = ViberCommandTranslation::find()->with('command')->where(['translation_id' => array_search($text, $this->commands)])->one();
                if ($translation  && in_array($command = $translation->command->name, array_keys($this->handlers))) {
                    $name = $this->handlers[$command];
                    if (($handler = new $name($this->v_u)) instanceof HandlerInterface) {
                        /** @var HandlerInterface $handler */
                        $handler->run($v_m);
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getAvailableCommands()
    {
        $all = ViberCommandTranslation::find()->all();
        if (empty($all)) return [];
        return array_column($all, 'translation', 'translation_id');
    }
}