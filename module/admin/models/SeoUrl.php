<?php
/**
 * SeoUrl model class file.
 */

namespace app\module\admin\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%seo_url}}".
 *
 * @property int $seo_url_id
 * @property int $language_id
 * @property string $query
 * @property string $keyword
 */
class SeoUrl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%seo_url}}';
    }

    /**
     * @inheritdocÅ
     */
    public function rules()
    {
        return [
            [['language_id', 'query'], 'required'],
            [['language_id'], 'integer'],
            [['query', 'keyword'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'seo_url_id' => 'ID SEO URL',
            'language_id' => 'Язык',
            'query' => 'Запрос',
            'keyword' => 'URL',
        ];
    }

    /**
     * Removes SEO URLs by query.
     *
     * @param string $query query filter
     */
    public static function removeByQuery($query)
    {
        self::deleteAll(['query' => $query]);
    }


    /**
     * Returns query model id.
     *
     * @param string $query query
     * @param string $keyword keyword
     * @param int $languageId language id
     * @return bool|int model id, or false if model not found
     */
    public static function getQueryModelId($query, $keyword, $languageId)
    {
        $seoUrl = (new Query())
            ->select(['query'])
            ->from(self::tableName())
            ->where('query LIKE ' . "'$query=%'")
            ->andWhere([
                'keyword' => $keyword,
                'language_id' => $languageId,
            ])
            ->scalar();

        if (!empty($seoUrl)) {
            return (int) str_replace($query . '=', '', $seoUrl);
        }

        return false;
    }

    /**
     * Returns keyword by specified query and languageId.
     *
     * @param string $query query in format 'field=value'
     * @param int $languageId language id
     * @return false|null|string keyword
     */
    public static function getKeywordByQuery($query, $languageId)
    {
        return (new Query())
            ->select(['keyword'])
            ->from(self::tableName())
            ->where('query LIKE ' . "'$query'")
            ->andWhere([
                'language_id' => $languageId,
            ])
            ->scalar();
    }

    /**
     * Prepares SEO URL. This method add index to SEO URL if this SEO URL already exists.
     *
     * @param string $url original SEO URL
     * @param int $languageId language id
     * @param null|string $query query
     * @return string prepared SEO URL
     */
    public static function prepare($url, $languageId, $query = null)
    {
        $index = 1;

        $seoUrl = $url;

        while (true) {
            $countQuery = (new Query())
                ->select('COUNT(*)')
                ->from(self::tableName())
                ->where([
                    'language_id' => $languageId,
                    'keyword' => $seoUrl,
                ]);

            if ($query !== null) {
                $countQuery = $countQuery->andWhere('query != ' . $query);
            }

            $count = $countQuery->scalar();

            if ($count > 0) {
                $seoUrl = $url . '-' . $index;
                $index++;
            } else {
                break;
            }
        }

        return $seoUrl;
    }

    /**
     * Transliterates SEO URL.
     *
     * @param string $string source string
     * @return string transliterated string
     */
    public static function transliterate($string)
    {
        $tr = array(
            "А" => "a", "Б" => "b", "В" => "v", "Г" => "g",
            "Д" => "d", "Е" => "e", "Ж" => "j", "З" => "z", "И" => "i",
            "Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n",
            "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t",
            "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "ts", "Ч" => "ch",
            "Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "yi", "Ь" => "",
            "Э" => "e", "Ю" => "yu", "Я" => "ya", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j", "і"=> 'i', 'Ї' => 'i',
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", 'є' => 'e',
            "ц" => "ts", "ч" => "ch" ,"ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "i", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
            " " =>  "-", "." => "", "/" => "-", "_" => "-"
        );

        $a = array('/(à|á|â|ã|ä|å|æ)/','/(è|é|ê|ë)/','/(ì|í|î|ï)/','/(ð|ò|ó|ô|õ|ö|ø|œ)/','/(ù|ú|û|ü)/','/ç/','/þ/','/ñ/','/ß/','/(ý|ÿ)/','/(=|\+|\/|\\\|\.|\'|\_|\\n| |\(|\))/','/[^a-z0-9_ -]/s','/-{2,}/s');
        $b = array('a','e','i','o','u','c','d','n','ss','y','-','','-');

        return trim(preg_replace($a, $b, strtolower(strtr($string, $tr))),'-');
    }
}
