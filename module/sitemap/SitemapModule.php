<?php

namespace app\module\sitemap;

use app\components\SitemapBehavior;
use app\module\admin\models\Language;
use Yii;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\helpers\Url;

class SitemapModule extends \yii\base\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'app\module\sitemap\controllers';
    /**
     * @var int
     */
    public $cacheExpire = 86400;
    /**
     * @var Cache|string
     */
    public $cacheProvider = 'cache';
    /**
     * @var string
     */
    public $cacheKey = 'sitemap';
    /**
     * @var boolean use php's gzip compressing
     */
    public $enableGzip = false;
    /**
     * @var boolean
     */
    public $enableGzipedCache = false;
    /**
     * @var array
     */
    public $models = [];
    /**
     * @var array
     */
    public $urls = [];


    /**
     * {@inheritdoc}
     * @throws InvalidConfigException if invalid `cacheKey` parameter was specified
     */
    public function init()
    {
        parent::init();
        if (is_string($this->cacheProvider)) {
            $this->cacheProvider = Yii::$app->{$this->cacheProvider};
        }
        if (!$this->cacheProvider instanceof Cache) {
            throw new InvalidConfigException('Invalid `cacheKey` parameter was specified.');
        }
    }

    /**
     * Build and cache the site map.
     *
     * @return string sitemap data
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidParamException
     */
    public function buildSitemap()
    {
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        $urls = [];
        foreach ($languages as $language) {
            Yii::$app->language = $language['code'];
            foreach ($this->urls as $url) {
                $url['loc'] = rtrim(Url::to(['/' . trim($url['loc'], '/')], 'https'), '/');
                $urls[] = $url;
            }
        }
        foreach ($this->models as $modelName) {
            /** @var \yii\db\ActiveRecord $model */
            if (is_array($modelName)) {
                $model = new $modelName['class'];
                if (isset($modelName['behaviors'])) {
                    $model->attachBehaviors($modelName['behaviors']);
                }
            } else {
                $model = new $modelName;
            }

            /** @var SitemapBehavior $model */
            $urls = array_merge($urls, $model->generateSiteMap($languages));
        }
        $sitemapData = $this->createControllerByID('default')->renderPartial('index', ['urls' => $urls]);
        if ($this->enableGzipedCache) {
            $sitemapData = gzencode($sitemapData);
        }
        $this->cacheProvider->set($this->cacheKey, $sitemapData, $this->cacheExpire);

        return $sitemapData;
    }

    /**
     * Flush cached sitemap data.
     */
    public function clearCache()
    {
        $this->cacheProvider->delete($this->cacheKey);
    }
}
