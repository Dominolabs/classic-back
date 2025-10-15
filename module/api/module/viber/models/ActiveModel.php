<?php


namespace app\module\api\module\viber\models;

use Throwable;
use app\module\api\module\viber\exceptions\ValidationException;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

class ActiveModel extends ActiveRecord
{
    /**
     * @param $condition
     * @param array $params
     * @return mixed
     */
    public static function where($condition, $params = [])
    {
        return static::find()->where($condition, $params);
    }

    /**
     * @param array $config
     * @return static
     * @throws ValidationException
     */
    public static function create($config = [])
    {
        $model = new static();
        if (!empty($config)) {
            $model->setAttributes($config);
            $model->save();
        }
        if ($model->hasErrors()) throw new ValidationException($model->getErrors());
        return $model;
    }

    /**
     * @param $runValidation
     * @param null $attributeNames
     * @return bool|false|int
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        if (is_array($runValidation)) {
            $this->setAttributes($runValidation);
            $this->save(false);
            if ($this->hasErrors()) throw new ValidationException($this->getErrors());
            return true;
        }
        return parent::update($runValidation, $attributeNames);
    }
}