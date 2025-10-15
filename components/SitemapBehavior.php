<?php

namespace app\components;

use app\module\admin\models\Language;
use app\module\admin\models\SeoUrl;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class SitemapBehavior extends Behavior
{
    const CHANGEFREQ_ALWAYS = 'always';
    const CHANGEFREQ_HOURLY = 'hourly';
    const CHANGEFREQ_DAILY = 'daily';
    const CHANGEFREQ_WEEKLY = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY = 'yearly';
    const CHANGEFREQ_NEVER = 'never';
    const BATCH_MAX_SIZE = 100;

    /** @var callable */
    public $dataClosure;
    /** @var string|bool */
    public $defaultChangefreq = false;
    /** @var float|bool */
    public $defaultPriority = false;
    /** @var callable */
    public $scope;
    /** @var string */
    public $query;
    /** @var int */
    public $defaultLanguageId;


    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());
        if (!is_array($this->dataClosure) && !is_callable($this->dataClosure)) {
            throw new InvalidConfigException('SitemapBehavior::$dataClosure isn\'t callable or array.');
        }
    }

    /**
     * Generates site map.
     * @param array $languages languages list
     * @return array site map data
     */
    public function generateSiteMap($languages)
    {
        $result = [];
        $seoUrls = $this->getSeoUrls();
        $n = 0;
        /** @var \yii\db\ActiveRecord $owner */
        $owner = $this->owner;
        $query = $owner::find();
        if (is_array($this->scope)) {
            if (is_callable($this->owner->{$this->scope[1]}())) {
                call_user_func($this->owner->{$this->scope[1]}(), $query);
            }
        } elseif (is_callable($this->scope)) {
            call_user_func($this->scope, $query);
        }
        foreach ($languages as $language) {
            Yii::$app->language = $language['code'];
            foreach ($query->each(self::BATCH_MAX_SIZE) as $model) {
                if (isset($seoUrls[$model->{$this->query}][1])) {
                    if (is_array($this->dataClosure)) {
                        $urlData = call_user_func($this->owner->{$this->dataClosure[1]}(), $model,
                            $seoUrls[$model->{$this->query}][1]['keyword']);
                    } else {
                        $urlData = call_user_func($this->dataClosure, $model,
                            $seoUrls[$model->{$this->query}][1]['keyword']);
                    }
                    if (empty($urlData)) {
                        continue;
                    }
                } else {
                    continue;
                }
                $result[$n]['loc'] = $urlData['loc'];
                if (!empty($urlData['lastmod'])) {
                    $result[$n]['lastmod'] = $urlData['lastmod'];
                }
                if (isset($urlData['changefreq'])) {
                    $result[$n]['changefreq'] = $urlData['changefreq'];
                } elseif ($this->defaultChangefreq !== false) {
                    $result[$n]['changefreq'] = $this->defaultChangefreq;
                }
                if (isset($urlData['priority'])) {
                    $result[$n]['priority'] = $urlData['priority'];
                } elseif ($this->defaultPriority !== false) {
                    $result[$n]['priority'] = $this->defaultPriority;
                }
                if (isset($urlData['news'])) {
                    $result[$n]['news'] = $urlData['news'];
                }
                if (isset($urlData['images'])) {
                    $result[$n]['images'] = $urlData['images'];
                }
                ++$n;
            }
        }
        return $result;
    }

    /**
     * Returns SEO URLs.
     *
     * @return array SEO URLs list
     */
    public function getSeoUrls()
    {
        $seoUrls = [];
        $seoUrlModels = SeoUrl::find()->where('query LIKE "' . $this->query . '%"')->all();
        foreach ($seoUrlModels as $seoUrlModel) {
            $modelId = str_replace($this->query . '=', '', $seoUrlModel['query']);
            $seoUrls[$modelId][$seoUrlModel->language_id] = ArrayHelper::merge($seoUrlModel->getAttributes(), [
                'modelId' => $modelId
            ]);
        }

        return $seoUrls;
    }
}
