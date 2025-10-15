<?php

namespace app\module\admin\models;

use app\components\ImageBehavior;
use Imagine\Image\ManipulatorInterface;
use yii\base\Model;

class DesignForm extends Model
{
    public $favicon;
    public $faviconFile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'favicon' => 'Иконка сайта',
            'faviconFile' => 'Иконка сайта',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'image' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'design',
            ]
        ];
    }

    /**
     * Returns image URL.
     *
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param string $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string image URL
     */
    public static function getImageUrl($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_INSET, $quality = 100)
    {
        return (new self())->resizeImage($filename, $width, $height, $mode, $quality);
    }
}
