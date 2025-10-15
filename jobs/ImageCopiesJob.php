<?php


namespace app\jobs;


use app\components\ImageBehavior;
use Yii;
use yii\base\BaseObject;
use yii\imagine\Image;
use yii\queue\JobInterface;

class ImageCopiesJob extends BaseObject implements JobInterface
{

    public $file;
    public $message = '';
    private $xs = 480;
    private $s = 768;
    private $m = 1365;
    private $l = 2165;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            if (file_exists($this->file) && exif_imagetype($this->file) !== false) {
                $extension = ImageBehavior::getExtension($this->file);

                $sizes = ['xs', 's', 'm', 'l'];
                foreach ($sizes as $size) {
                    Image::resize($this->file,  $this->{$size}, null)->save($this->file . '_' . $size . '.' . $extension, [
                        'quality' => 50
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Yii::error([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'images');
        }
    }
}