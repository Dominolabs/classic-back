<?php

namespace app\module\api\controllers;

use app\components\ImageBehavior;
use app\module\admin\module\gallery\models\AlbumImage;
use app\module\admin\module\gallery\models\Album;
use app\module\admin\module\gallery\models\AlbumCategory;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class GalleryController extends BaseApiController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'categories' => ['GET'],
                'albums' => ['GET'],
                'album' => ['GET'],
            ],
        ];
        return $behaviors;
    }

    /**
     * Returns album categories list.
     * @param string $lang language code
     * @return array response data
     */
    public function actionCategories($lang)
    {
        Yii::$app->language = $lang;
        return [
            'status' => 'success',
            'data' => AlbumCategory::getAll()
        ];
    }

    /**
     * Returns albums list.
     * @param string $lang language code
     * @return array response data
     */
    public function actionAlbums($lang)
    {
        Yii::$app->language = $lang;
        $data = [];
        $albums = Album::getAll();
        foreach ($albums as $album) {
            $album['thumb'] = ImageBehavior::getThumbnailFileName($album['image'], 408, 584);
            $data[] = $album;
        }
        return [
            'status' => 'success',
            'data' => $data
        ];
    }

    /**
     * Returns album info.
     * @param string $lang language code
     * @param int $album_id album id
     * @return array response data
     */
    public function actionAlbum($lang, $album_id)
    {
        Yii::$app->language = $lang;
        $data = Album::getAlbum($album_id);
        if (!empty($data)) {
            $data['thumb'] = ImageBehavior::getThumbnailFileName($data['image'], 408, 584);
        }
        $images = AlbumImage::getAllByAlbumId($album_id);
        foreach ($images as $image) {
            $data['images'][] = ArrayHelper::merge($image->getAttributes(), [
                'thumb' => ImageBehavior::getThumbnailFileName($image->image, 600, 600),
            ]);
        }
        return [
            'status' => 'success',
            'data' => $data
        ];
    }
}
