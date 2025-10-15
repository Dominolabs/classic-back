<?php

namespace app\module\api\module\viber\controllers\helpers;

use Carbon\Carbon;
use Yii;

class Helper
{
    public const SITE_URL = 'https://classic.devseonet.com';

    /**
     * @param string $key
     * @param string $new_name
     * @param array $array
     */
    public static function rename_array_key(array &$array, string $key, string $new_name): void
    {
        if (array_key_exists($key, $array)) {
            $array[$new_name] = $array[$key];
            unset($array[$key]);
        }
    }

    /**
     * @param array $array
     * @param array $old_and_new_keys
     */
    public static function rename_array_keys(array &$array, array $old_and_new_keys): void
    {
        foreach ($old_and_new_keys as $key => $new_name) {
            self::rename_array_key($array, $key, $new_name);
        }
    }

    /**
     * @param null $tz
     * @return Carbon
     */
    public static function now($tz = null)
    {
        return Carbon::now($tz);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function asset(string $path): string
    {
        if (empty(self::SITE_URL)) return $path;
        $site = Str::finish(self::SITE_URL, "/");

        if ($path[0] === '/') $path = substr($path, 1);

        return $site . $path;
    }

    /**
     * @param $string
     * @return mixed|null
     */
    public static function config($string)
    {
        $path = explode('.', $string);
        $fileName = array_shift($path);
        if (file_exists($file = Yii::getAlias('@app/module/api/module/viber/config/'. $fileName . '.php'))) {
            $data = include $file;
            if (!empty($path) && is_array($data)){
                foreach ($path as $item) {
                    if (!empty($data[$item]))
                        $data = $data[$item];
                    else $data = null;
                }
            }
            return $data;
        } else return null;
    }

    /**
     * @param $message
     * @param string $category
     */
    public static function log($message, $category = 'app')
    {
        if (is_array($message)) $message = json_encode($message);
        $file = Yii::getAlias("@app/runtime/logs/$category.log");
        file_put_contents($file, $message, FILE_APPEND);
        file_put_contents($file, "\n", FILE_APPEND);
    }
}