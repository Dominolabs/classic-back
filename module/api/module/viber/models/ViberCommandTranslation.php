<?php


namespace app\module\api\module\viber\models;


use yii\db\ActiveQuery;

/**
 * Class ViberCommandTranslation
 * @package app\module\api\module\viber\models
 * @property ViberCommand $command
 */
class ViberCommandTranslation extends ActiveModel
{
    /**
     * @return ActiveQuery
     */
    public function getCommand()
    {
        return $this->hasOne(ViberCommand::class, ['command_id' => 'command_id']);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['command_id', 'language_id'], 'integer'],
            ['translation', 'string']
        ];
    }
}