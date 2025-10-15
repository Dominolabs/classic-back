<?php

namespace app\module\api\module\viber\components;

use ReflectionClass;
use ReflectionException;
use yii\base\Model;

class Component extends Model
{
    /**
     * Button constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config)) $this->setAttributes($config, false);
    }

    /**
     * @param string $name
     * @param $value
     * @return Component
     */
    public function setOption(string $name, $value)
    {
        if (property_exists($this, $name)) $this->$name = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getOption(string $name)
    {
        if (property_exists($this, $name)) return $this->$name;

        return null;
    }

    /**
     * @param bool $with_static
     * @return array|void
     * @throws ReflectionException
     */
    public function makeArray($with_static = false)
    {
        $result = [];
        $propertiesNames = array_keys(get_object_vars($this));
        $required = $this->required ?? [];
        if ($with_static) {
            $class = new ReflectionClass($this);
            $propertiesNames = array_merge($propertiesNames, $class->getStaticProperties());
        }

        foreach ($propertiesNames as $name) {
            if ($name === 'required') continue;
            if (in_array($name, $required) || !is_null($this->$name)) $result[$name] = $this->$name;
        }
        return $result;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}