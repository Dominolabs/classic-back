<?php


namespace app\module\api\module\viber\models;

/**
 * Class ViberCommand
 * @package app\module\api\module\viber\models
 * @property int $command_id
 * @property string $name
 */
class ViberCommand extends ActiveModel
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['name', 'string']
        ];
    }
}