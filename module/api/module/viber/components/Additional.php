<?php


namespace app\module\api\module\viber\components;


class Additional extends Component
{
    /**
     * Additional constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config))
            foreach ($config as $property => $value) {
                $this->$property = $value;
            }
    }

    /**
     * @param string $name
     * @param $value
     */
    public function setOption(string $name, $value)
    {
        $this->$name = $value;
    }

    /**
     * @param string $name
     * @param $value
     * @return Additional
     */
    public function attach(string $name, $value)
    {
        if (is_object($value) && method_exists($value, 'makeArray')) {
            $value = $value->makeArray();
        }

        $this->setOption($name, $value);
        return $this;
    }
}