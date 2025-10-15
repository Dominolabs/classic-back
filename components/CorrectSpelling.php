<?php


namespace app\components;


class CorrectSpelling
{
    private $vocabulary;
    private $lang;


    public function __construct()
    {
        $this->vocabulary = include(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'nouns.php');
        $this->lang = \Yii::$app->language;
    }


    /**
     * @param $noun
     * @param int $quantity
     * @return mixed
     */
    public function get($noun, $quantity = 0)
    {
        if(array_key_exists($noun, $this->vocabulary)){
            $key = $this->getKey($quantity);
            try {
                if(isset($this->vocabulary[$noun])){
                    return $this->vocabulary[$noun][$this->lang][$key];
                }
            } catch (\Throwable $exception) {
                return $noun;
            }
        }
        return $noun;
    }


    /**
     * @param $noun
     * @return array|mixed
     */
    public function getAllVariants($noun)
    {
        if(array_key_exists($noun, $this->vocabulary)){
            try {
                if(isset($this->vocabulary[$noun])){
                    return json_encode($this->vocabulary[$noun][$this->lang]);
                }
            } catch (\Throwable $exception) {
                return json_encode([]);
            }
        }
        return json_encode([]);
    }

    /**
     * @param $quantity
     * @return string
     */
    private function getKey($quantity)
    {
        switch (true){
            case ($quantity === 0):
                return 'zero';
                break;
            case ($quantity % 10 === 1 or $quantity === 1):
                return 'one';
                break;
            case ($quantity > 1 and $quantity < 5):
                return 'few';
                break;
            default:
                return 'many';
        }
    }
}