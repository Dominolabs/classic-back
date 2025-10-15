<?php
namespace app\module\api\presenters;

use Yii;

abstract class AbstractPresenter
{
    protected $model_class;
    protected $model;
    protected $model_type;
    protected $lang;

    /**
     * AbstractPresenter constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model      = $model;
        $this->model_type = is_array($model) ? 'array' : 'object';
        $this->lang       = Yii::$app->language;
    }


    /**
     * @param $name
     * @return |null
     */
    public function __get($name)
    {
        if($this->model_type === 'array'){
            return $this->model[$name] ?? null;
        } else {
            return $this->model->{$name} ?? null;
        }
    }


    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        if($this->model_type === 'array'){
            return $this->model[$name] = $value;
        } else {
            return $this->model->{$name} = $value;
        }
    }


    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        if($this->model_type === 'array'){
            return isset($this->model[$name]);
        } else {
            return isset($this->model->{$name});
        }
    }
}