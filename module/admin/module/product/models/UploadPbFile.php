<?php

namespace app\module\admin\module\product\models;

use yii\db\ActiveRecord;

/**
 * @property $id' => $this->primaryKey(),
 * @property $status' => $this->tinyInteger(),
 * @property $file' => $this->string(1024),
 * @property $message' => $this->text(),
 */
class UploadPbFile extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    public static function tableName()
    {
        return '{{%upload_pb_file}}';
    }

    public function rules()
    {
        return [
            ['file', 'string', 'max' => 1024],
            ['message', 'string', 'max' => 65535],
            ['status', 'number', 'integerOnly' => true],
        ];
    }

    public function afterDelete()
    {
        parent::afterDelete();
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }
}