<?php


namespace app\module\api\module\viber\controllers\helpers;


use app\module\api\module\viber\models\ActiveModel;
use Exception;
use Throwable;
use yii\web\UploadedFile;

class FileHelper
{

    /**
     * @param ActiveModel $model
     * @param $link
     * @param string|null $dir
     * @param string|null $client_name
     * @return bool
     * @throws Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function saveFileFromLink(ActiveModel $model, $link, ?string $dir = null, string $client_name = null): bool
    {
        $result = true;
        $dir = self::getDir($model, $dir);

        list($name, $ext) = self::getNewNameFromLink($link);
        $ReadFile = fopen($link ?? '', "rb");
        if ($ReadFile) {
            $path = Str::finish($dir, '/') . $name;
            $WriteFile = fopen($path, "wb");
            if ($WriteFile) {
                while (!feof($ReadFile)) {
                    fwrite($WriteFile, fread($ReadFile, 4096));
                }
                fclose($WriteFile);
                $mime = is_file($path) ? mime_content_type($path) : null;
                if (empty($ext) && !empty($mime) && !empty($s = strripos($mime, '/'))) {
                    $ext = substr($mime, $s + 1);
                    $name .= $ext;
                }
                $model->update(['file' => [
                    'path' => $path,
                    'filename' => $name,
                    'file_extension' => $ext,
                    'mime' => $mime,
                    'original_name' => empty($client_name) ? $name : $client_name
                ]
                ]);
            }
            fclose($ReadFile);
        }
        return $result;
    }

    /**
     * @param $model
     * @param $dir
     * @return string
     * @throws Throwable
     */
    public static function getDir($model, $dir = null)
    {
        if (is_null($dir)) {
            if (empty($model->defaultDir)) {
                throw new Exception('Directory not found.');
            }
            $dir = Str::finish($model->defaultDir, '/') . date('Y') . '/' . date('m');
        }
        if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
            throw new Exception('Directory creation failed.');
        }
        return $dir;
    }

    /**
     * @param $string
     * @return array
     */
    public static function getNewNameFromLink($string)
    {
        $ext = basename($string);
        $q = strpos($ext, '?');
        if ($q !== false) $ext = substr($ext, 0, $q);
        $d = strripos($ext, '.');
        if ($d !== false) $ext = substr($ext, $d + 1); else $ext = '';
        $new_name = md5(microtime() . rand(0, 9999)) . "." . $ext;
        return [$new_name, $ext];
    }

    /**
     * @param ActiveModel $model
     * @param UploadedFile $file
     * @param $errors
     * @return string
     * @throws Throwable
     */
    public static function getFileLink(ActiveModel $model, $file, &$errors)
    {
        $new_name = md5(microtime() . rand(0, 9999)) . "." . $file->extension;
        $dir = FileHelper::getDir($model);
        $full_name = Str::finish($dir, '/') . $new_name;
        if (!$file->saveAs($full_name)) $errors[] = $file->baseName;
        return Helper::asset($full_name);
    }
}