<?php


namespace app\module\api\module\viber\components;


use app\module\api\module\viber\controllers\helpers\T;

class NoButton extends Button
{
    public $Columns = 3;
    public $Rows = 1;
    public $BgColor = "#cd0000";

    public function __construct($config = [])
    {
        $this->Text = T::t('viber', 'No');
        parent::__construct($config);
    }
}