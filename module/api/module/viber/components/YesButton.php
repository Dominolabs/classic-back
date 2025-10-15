<?php


namespace app\module\api\module\viber\components;


use app\module\api\module\viber\controllers\helpers\T;

class YesButton extends Button
{
    public $Columns = 3;
    public $Rows = 1;
    public $BgColor = "#32CD32";

    public function __construct($config = [])
    {
        $this->Text = T::t('viber', 'Yes');
        parent::__construct($config);
    }
}