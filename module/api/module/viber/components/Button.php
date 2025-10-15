<?php


namespace app\module\api\module\viber\components;


class Button extends Component
{
    public $Columns = 6;
    public $Rows = 1;
    public $ActionType = 'reply';
    public $ActionBody = '';
    public $BgColor = null;
    public $BgMediaType = null;
    public $BgMedia = null;
    public $BgLoop = null;
    public $Image = null;
    public $Text = null;
    public $TextHAlign = null;
    public $TextOpacity = null;
    public $TextSize = null;
    public $Silent = null;
    public $BgMediaScaleType = null;
    public $ImageScaleType = null;
    public $TextPaddings = null;
    public $OpenURLType = null;
    public $OpenURLMediaType = null;
    public $TextBgGradientColor = null;
    public $TextShouldFit = null;

    protected $required = ['ActionBody'];

    /**
     * @param Keyboard $keyboard
     * @throws \ReflectionException
     */
    public function attachTo(Keyboard $keyboard)
    {
        $keyboard->attach($this);
    }
}