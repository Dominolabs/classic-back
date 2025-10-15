<?php


namespace app\module\api\module\viber\components;


use ReflectionException;

class Keyboard extends Component
{
    public $Buttons = [];
    public $BgColor = null;
    public $DefaultHeight = null;
    public $CustomDefaultHeight = null;
    public $HeightScale = null;
    public $ButtonsGroupColumns = null;
    public $ButtonsGroupRows = null;
    public $InputFieldState = 'regular';
    public $FavoritesMetadata = null;
    public $Type = 'keyboard';

    protected $required = ['Buttons', 'Type'];

    /**
     * @param Button $button
     * @return Keyboard
     * @throws ReflectionException
     */
    public function attach(Button $button)
    {
        $this->Buttons[] = $button->makeArray();
        return $this;
    }
}