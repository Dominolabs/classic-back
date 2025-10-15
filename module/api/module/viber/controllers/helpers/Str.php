<?php


namespace app\module\api\module\viber\controllers\helpers;


use Throwable;

class Str
{
    /**
     * @param $value
     * @param $cap
     * @return string
     */
    public static function finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
    }

    /**
     * @param int $length
     * @return string
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            try {
                $bytes = @random_bytes($size);
            } catch (Throwable $e) {
                $bytes = time() . rand(0, 100000);
            }

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}