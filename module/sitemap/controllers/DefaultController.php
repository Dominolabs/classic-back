<?php

namespace app\module\sitemap\controllers;

use app\module\sitemap\SitemapModule;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class DefaultController extends Controller
{
    /**
     * Generates site map.
     *
     * @return string sitemap data
     * @throws \yii\base\InvalidConfigException if invalid `cacheKey` parameter was specified
     */
    public function actionIndex()
    {
        /** @var SitemapModule $module */
        $module = $this->module;
        if (!$sitemapData = $module->cacheProvider->get($module->cacheKey)) {
            $sitemapData = $module->buildSitemap();
        }
        Yii::$app->response->format = Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/xml');
        if ($module->enableGzip) {
            if (!$module->enableGzipedCache) {
                $sitemapData = gzencode($sitemapData);
            }
            $headers->add('Content-Encoding', 'gzip');
            $headers->add('Content-Length', strlen($sitemapData));
        } elseif ($module->enableGzipedCache) {
            $sitemapData = gzdecode($sitemapData);
        }

        return $sitemapData;
    }
}
