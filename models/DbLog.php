<?php

namespace app\models;

use app\jobs\ClearDbLogsJob;
use Yii;
use yii\db\ActiveRecord;

class DbLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%db_log}}';
    }

    public static function add(array $attributes)
    {
        $model = new static;
        $model->attributes = $attributes;
        $model->created_at = date('Y-m-d H:i:s');
        if ($model->msg) {
            $model->msg = mb_substr($model->msg, 0, 65353);
        }
        if ($model->trace) {
            $model->trace = mb_substr($model->trace, 0, 65353);
        }
        if ($model->category) {
            $model->category = mb_substr($model->category, 0, 255);
        }
        $model->save();

        Yii::$app->queue->push(new ClearDbLogsJob([]));
    }

    public function rules()
    {
        return [
            [['msg', 'trace'], 'string', 'max' => 65535],
            ['category', 'string', 'max' => 255],
            ['created_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }
}