<?php

namespace app\components;

use app\jobs\ImageCopiesJob;
use app\module\admin\models\BannerImage;
use Throwable;
use Yii;
use yii\base\Behavior;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use Imagine\Image\ManipulatorInterface;

class ImageBehavior extends Behavior
{
    /**
     * @var string base image directory path alias
     */
    public $imageDirectory = '';
    /**
     * @var string image file attribute
     */
    public $imageAttribute = 'imageFile';

    public static $sizes = ['xs', 's', 'm', 'l'];


    /**
     * Returns image path.
     *
     * @return bool|string image base path.
     */
    public function getImagePath()
    {
        return Yii::getAlias(static::getImageBaseAlias() . DIRECTORY_SEPARATOR . $this->imageDirectory);
    }

    /**
     * Returns image cache path.
     *
     * @return bool|string image base path.
     */
    public function getImageCachePath()
    {
        return Yii::getAlias(static::getImageBaseAlias() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $this->imageDirectory);
    }

    /**
     * Resize image.
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param string $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string resized image path
     */
    public function resizeImage($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND, $quality = 100)
    {
        try {

            if (empty($filename)) {
                $filename = 'placeholder.png';
                $this->imageDirectory = '';
            }
            $imageBaseUrl = Yii::$app->request->baseUrl . '/image/cache/' . (!empty($this->imageDirectory) ? $this->imageDirectory . '/' : '');
            $imageOld = $filename;
            $imageOldPath = $this->getImagePath() . DIRECTORY_SEPARATOR . $filename;
            if (!is_file($imageOldPath)) {
                Yii::info('No file ' . $imageOldPath, 'images');
                return null;
            }
            $extension = pathinfo($imageOld, PATHINFO_EXTENSION);
            $imageNew = substr($filename, 0, strrpos($filename, '.')) . '-' . $width . (($height) ? ('x' . $height) : '') . '.' . $extension;
            $imageNewPath = $this->getImageCachePath() . DIRECTORY_SEPARATOR . $imageNew;
            $cache_dir = substr($imageNewPath, 0, strripos($imageNewPath, '/'));
            if (!is_file($imageNewPath) || (filemtime($imageOldPath) > filemtime($imageNewPath))) {
                list($widthOrig, $heightOrig, $imageType) = getimagesize($imageOldPath);
//                Yii::info(compact('imageBaseUrl', 'imageOldPath', 'cache_dir', 'imageNewPath', 'imageNew'), 'images');
                if (!in_array($imageType, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
                    return Yii::$app->request->baseUrl . '/image/' . (!empty($this->imageDirectory) ? $this->imageDirectory . '/' : '') . $imageOld;
                }
                if (!is_dir($cache_dir)) {
                    @mkdir($cache_dir, 0777, true);
                }
                if (self::checkImageType($filename)) {
                    if ($widthOrig != $width || $heightOrig != $height) {
                        Image::thumbnail($imageOldPath, $width, $height, $mode)->save($imageNewPath, ['quality' => $quality]);
                    } else {
                        copy($imageOldPath, $imageNewPath);
                    }
                } else {
                    copy($imageOldPath, $imageNewPath);
                }
            }

            return $imageBaseUrl . $imageNew;
        } catch (Throwable $exception) {
            return null;
        }
    }

    /**
     * Returns image URL.
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @return null|string resized image path
     */
    public function getUrl($filename, $width = null, $height = null)
    {
        if (empty($filename)) {
            return Yii::$app->request->baseUrl . '/image/placeholder.png';
        }
        $imageOldPath = $this->getImagePath() . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($imageOldPath)) {
            return Yii::$app->request->baseUrl . '/image/placeholder.png';
        }
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($width === null) {
            $imageNew = $filename;
        } else {
            $imageNew = substr($filename, 0, strrpos($filename, '.')) . '-' . $width . ($height ? ('x' . $height) : '') . '.' . $extension;
        }
        $imageNewPath = $this->getImageCachePath() . DIRECTORY_SEPARATOR . $imageNew;
        if (!is_file($imageNewPath)) {
            return Yii::$app->request->baseUrl . '/image/placeholder.png';
        }

        return Yii::$app->request->baseUrl . '/image/cache/' . $this->imageDirectory . '/' . $imageNew;
    }

    /**
     * Uploads image.
     * @param bool|string $attribute image attribute. Defaults false, meaning behavior attribute will be used
     * @param bool|string $index index used for array attribute value
     * @return bool|string uploaded image file name, false otherwise
     */
    public function uploadImage($attribute = false, $index = false)
    {
        try {
            $imageAttribute = $attribute ?: $this->imageAttribute;
            $attr = $index ? $this->owner->{$imageAttribute}[$index] : $this->owner->{$imageAttribute};
            if ($this->owner->validate($imageAttribute)) {
                if (!empty($attr)) {
                    $prefix = date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR;
                    $imageName = $prefix . static::generateImageName($attr->extension);
                    $filename = $this->getImagePath() . DIRECTORY_SEPARATOR . $imageName;
                    if (!is_dir($this->getImagePath() . DIRECTORY_SEPARATOR . $prefix)) {
                        @mkdir($this->getImagePath() . DIRECTORY_SEPARATOR . $prefix, 0777, true);
                    }
                    $attr->saveAs($filename);

                    $this->makeCopies($filename);

                    return $imageName;
                }
            }

            return false;
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * Uploads image by URL.
     * @param string $url image URL
     * @return bool|string uploaded image file name, false otherwise
     */
    public function uploadImageByUrl($url)
    {
        try {
            if (!empty($url)) {
                $options = self::getOptionsFromUrl($url);
                $extension = self::getExtensionByMimeType(!empty($options['type']) ? $options['type'] : null);
                if (!empty($extension)) {
                    $imageName = static::generateImageName($extension);
                    $filename = $this->getImagePath() . DIRECTORY_SEPARATOR . $imageName;
                    if (!is_dir($this->getImagePath())) {
                        @mkdir($this->getImagePath(), 0777, true);
                    }
                    @copy($url, $filename);

                    return $imageName;
                }
            }

            return false;
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * Removes image and all related thumbnails.
     * @param string $filename file name to remove
     */
    public function removeImage($filename)
    {
        try {
            if (!empty($filename)) {
                $image = $this->getImagePath() . DIRECTORY_SEPARATOR . $filename;
                if (file_exists($image)) {
                    unlink($image);
                }
                $this->removeThumbnailImages($filename);
                $this->removeCopies($filename);
            }
        } catch (Throwable $exception) {
            return;
        }
    }

    /**
     * @param $filename
     */
    public function removeCopies($filename)
    {
        try {
            $ext = self::getExtension($filename);
            foreach (self::$sizes as $size) {
                $image = $this->getImagePath() . DIRECTORY_SEPARATOR . $filename . '_' . $size . '.' . $ext;
                if (file_exists($image)) {
                    unlink($image);
                }
            }
        } catch (Throwable $exception) {
            return;
        }
    }

    /**
     * Removes thumbnail images.
     * @param string $filename original image file name
     */
    public function removeThumbnailImages($filename)
    {
        try {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $file = pathinfo($filename, PATHINFO_FILENAME);
            $images = glob($this->getImageCachePath() . DIRECTORY_SEPARATOR . $file . '*' . $extension);
            foreach ($images as $image) {
                if (file_exists($image)) {
                    unlink($image);
                }
            }
        } catch (Throwable $exception) {
            return;
        }
    }

    /**
     * Generates unique image file name.
     * @param string $extension image file extension
     * @return string name
     */
    public static function generateImageName($extension)
    {
        return md5(uniqid(time(), true)) . '.' . $extension;
    }

    /**
     * Returns images base alias.
     * @return string images base alias
     */
    public static function getImageBaseAlias()
    {
        return '@app/web/image';
    }

    /**
     * Returns image placeholder.
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param string $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string resized image path
     */
    public static function placeholder($width, $height, $mode = ManipulatorInterface::THUMBNAIL_INSET, $quality = 100)
    {
        return (new self())->resizeImage('placeholder.png', $width, $height, $mode, $quality);
    }

    /**
     * Checks whether image have supported format.
     *
     * @param string $filename file name with extension
     * @return bool whether image have supported format
     */
    public static function checkImageType($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array($ext, ['gif', 'jpeg', 'jpg', 'png'], false)) {
            return true;
        }

        return false;
    }

    /**
     * Returns image file options by URL.
     * @param string $url URL to image
     * @return mixed image options
     */
    public static function getOptionsFromUrl($url)
    {
        $parsed_url = parse_url($url);
        $headers = get_headers($url, 1);
        if (!$parsed_url || !$headers || !preg_match('/^(HTTP)(.*)(200)(.*)/i', $headers[0])) {
            $options['error'] = UPLOAD_ERR_NO_FILE;
        }
        $options['name'] = isset($parsed_url['path']) ? pathinfo($parsed_url['path'], PATHINFO_BASENAME) : '';
        $options['baseName'] = isset($parsed_url['path']) ? pathinfo($parsed_url['path'], PATHINFO_FILENAME) : '';
        $options['extension'] = isset($parsed_url['path'])
            ? mb_strtolower(pathinfo($parsed_url['path'], PATHINFO_EXTENSION))
            : '';
        $options['size'] = isset($headers['Content-Length']) ? $headers['Content-Length'] : 0;
        $options['type'] = isset($headers['Content-Type']) ? $headers['Content-Type'] : FileHelper::getMimeTypeByExtension($options['name']);

        return $options;
    }

    /**
     * Returns image extension by MIME type.
     * @param string $mimeType MIME type
     * @return mixed extension name or null if not found
     */
    public static function getExtensionByMimeType($mimeType)
    {
        $extensions = [
            'image/jpeg' => 'jpeg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
        ];

        return !empty($extensions[$mimeType]) ? $extensions[$mimeType] : null;
    }

    /**
     * Returns image file base64.
     * @param string $path image file path
     * @return bool|string base64-encoded image file
     */
    public static function getImageFileBase64($path)
    {
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);

            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return false;
    }

    /**
     * Returns thumbnail filename based on original filename, image width and height.
     * @param string $filename image filename
     * @param int $width image width
     * @param int $height image height
     * @return null|string thumbnail filename
     */
    public static function getThumbnailFileName($filename, $width, $height)
    {
        if (empty($filename)) {
            return null;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return substr($filename, 0, strrpos($filename, '.')) . '-' . $width . ($height ? ('x' . $height) : '') . '.' . $extension;
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getExtension(string $filename): string
    {
        $last = strripos($filename, '.');
        if ($last !== false)
            return mb_substr($filename, $last + 1);
        return '';
    }

    /**
     * @param string $filename
     */
    private function makeCopies(string $filename)
    {
        try {
            Yii::$app->queue->push(new ImageCopiesJob([
                'file' => $filename
            ]));
        } catch (Throwable $e) {
            Yii::error([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'images');
        }
    }
}
