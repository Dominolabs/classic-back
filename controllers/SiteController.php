<?php

namespace app\controllers;

use app\models\WebcamForm;
use app\module\admin\models\SettingForm;
use app\module\admin\module\hotelservice\models\Hotelservice;
use app\module\admin\module\room\models\Room;
use app\module\admin\module\team\models\Team;
use app\module\admin\module\tariff\models\Tariff;
use app\module\admin\models\User;
use app\module\admin\module\gallery\models\AlbumImage;
use app\module\admin\module\gallery\models\Album;
use app\module\admin\module\product\models\Category;
use app\module\admin\models\Banner;
use app\module\admin\models\BannerImage;
use app\module\admin\models\Language;
use app\module\admin\models\Page;
use app\module\admin\models\SeoUrl;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\feedback\models\Feedback;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'app\components\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string home page rendering result
     * @throws NotFoundHttpException if page not found
     */
    public function actionIndex()
    {
        return $this->redirect('https://classic.com.ua/'); //TODO:: remove this crutch after our front-end site will be ready!!!

        $mainPageId = isset(Yii::$app->params['mainPageId']) ? Yii::$app->params['mainPageId'] : null;

        if ($mainPageId === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $languageId = Language::getLanguageIdByCode(Yii::$app->language);
        $defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());

        $page = Page::getByIdAndLanguageId($mainPageId, $languageId);

        if ($languageId != $defaultLanguageId) {
            $defaultPage = Page::getByIdAndLanguageId($mainPageId, $defaultLanguageId);
        } else {
            $defaultPage = $page;
        }

        $menuItems = Page::getMenuItems($languageId);

        if (empty($menuItems)) {
            $menuItems = Page::getMenuItems($defaultLanguageId);
        }

        // Remove Main page menu item
        array_shift($menuItems);

        return $this->render('index', [
            'page' => $page,
            'defaultPage' => $defaultPage,
            'menuItems' => $menuItems
        ]);
    }

    /**
     * Displays static page.
     *
     * @return string the rendering result
     * @throws NotFoundHttpException if page not found
     */
    public function actionPage()
    {
        try {
            $alias = trim(substr(Yii::$app->request->getPathInfo(), strrpos(Yii::$app->request->getPathInfo(), '/')),
                '/');
        } catch (InvalidConfigException $e) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $languageId = Language::getLanguageIdByCode(Yii::$app->language);
        $defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());
        $seoUrl = SeoUrl::findOne(['keyword' => $alias, 'language_id' => $languageId]);

        if (!empty($seoUrl)) {
            if ((strncmp('page_id=', $seoUrl->query, 8) === 0)) {
                return $this->renderStaticPage($alias, (int)substr($seoUrl->query, 8), $languageId, $defaultLanguageId);
            } elseif (strncmp('album_id=', $seoUrl->query, 9) === 0) {
                return $this->renderAlbumPage((int)substr($seoUrl->query, 9));
            }
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * Displays gallery page.
     *
     * @return string the rendering result
     * @throws NotFoundHttpException if page not found
     */
    public function actionGallery()
    {
        try {
            $aliasArray = explode("/", Yii::$app->request->getPathInfo(), 2);
            $alias = $aliasArray[0];
        } catch (InvalidConfigException $e) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $languageId = Language::getLanguageIdByCode(Yii::$app->language);
        $seoUrl = SeoUrl::findOne(['keyword' => $alias, 'language_id' => $languageId]);

        if (!empty($seoUrl)) {
            $pageId = (int)substr($seoUrl->query, 8);

            $familyPageId = isset(Yii::$app->params['familyPageId']) ? Yii::$app->params['familyPageId'] : null;
            $karaokePageId = isset(Yii::$app->params['karaokePageId']) ? Yii::$app->params['karaokePageId'] : null;
            $eventHallPageId = isset(Yii::$app->params['eventHallPageId']) ? Yii::$app->params['eventHallPageId'] : null;
            $fitnessPageId = isset(Yii::$app->params['fitnessPageId']) ? Yii::$app->params['fitnessPageId'] : null;
            $skyGardenPageId = isset(Yii::$app->params['skyGardenPageId']) ? Yii::$app->params['skyGardenPageId'] : null;
            $cateringPageId = isset(Yii::$app->params['cateringPageId']) ? Yii::$app->params['cateringPageId'] : null;

            switch ($pageId) {
                case $familyPageId:
                    $albumCategoryId = !empty(Yii::$app->params['familyPageAlbumCategoryId']) ? Yii::$app->params['familyPageAlbumCategoryId'] : 0;
                    break;
                case $karaokePageId:
                    $albumCategoryId = !empty(Yii::$app->params['karaokePageAlbumCategoryId']) ? Yii::$app->params['karaokePageAlbumCategoryId'] : 0;
                    break;
                case $eventHallPageId:
                    $albumCategoryId = !empty(Yii::$app->params['eventHallPageAlbumCategoryId']) ? Yii::$app->params['eventHallPageAlbumCategoryId'] : 0;
                    break;
                case $fitnessPageId:
                    $albumCategoryId = !empty(Yii::$app->params['fitnessPageAlbumCategoryId']) ? Yii::$app->params['fitnessPageAlbumCategoryId'] : 0;
                    break;
                case $skyGardenPageId:
                    $albumCategoryId = !empty(Yii::$app->params['skyGardenPageAlbumCategoryId']) ? Yii::$app->params['skyGardenPageAlbumCategoryId'] : 0;
                    break;
                case $cateringPageId:
                    $albumCategoryId = !empty(Yii::$app->params['cateringPageAlbumCategoryId']) ? Yii::$app->params['cateringPageAlbumCategoryId'] : 0;
                    break;
                default:
                    $albumCategoryId = 0;
            }

            return $this->renderGalleryPage($albumCategoryId, $alias);
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * Displays menu page.
     *
     * @return string the rendering result
     */
    public function actionMenu()
    {
        $categories = [];
        $categoriesData = Category::getCategories();

        if (!empty($categoriesData)) {
            foreach ($categoriesData as $category) {
                $data = [];
                $data['filter_category_id'] = $category['category_id'];

                $categories[] = ArrayHelper::merge($category, [
                    'products' => Product::getProducts($data)
                ]);
            }
        }

        return $this->render('menu', [
            'categories' => $categories
        ]);
    }

    /**
     * Displays app page.
     *
     * @return string the rendering result
     * @throws NotFoundHttpException if page not found
     */
    public function actionApp()
    {
        $user = null;

        try {
            $alias = Yii::$app->request->getPathInfo();

            if ($alias == 'app') {

            } else {
                $userPromoCode = str_replace('app/', '', $alias);

                $user = User::findByPromoCode($userPromoCode);

                if (empty($user)) {
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                }
            }
        } catch (InvalidConfigException $e) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $languageId = Language::getLanguageIdByCode(Yii::$app->language);
        $defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());
        $address = !empty(Yii::$app->params['address'][$languageId]) ? Yii::$app->params['address'][$languageId] :
            (!empty(Yii::$app->params['address'][$defaultLanguageId]) ? Yii::$app->params['address'][$defaultLanguageId] : '');
        $contactsMapSrc = !empty(Yii::$app->params['contactsMapSrc']) ? Yii::$app->params['contactsMapSrc'] : '';
        $phone = !empty(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : '';

        return $this->render('app', [
            'address' => $address,
            'contactsMapSrc' => $contactsMapSrc,
            'phone' => $phone,
            'user' => $user
        ]);
    }

    public function actionReviews()
    {
        $model = new Feedback();
        $model->status = Feedback::STATUS_NOT_ACTIVE;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->save()) {
                $model->sendAdminEmail();
                return $this->asJson([
                    'success' => true,
                    'message' => Yii::t('reviews', 'Відгук успішно відправлено')
                ]);
            }

            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }

            return $this->asJson(['validation' => $result]);
        }
    }

    /**
     * Displays webcam page.
     *
     * @return string the rendering result
     */
    public function actionWebcam()
    {
        $this->layout = 'webcam';

        $model = new WebcamForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                return $this->asJson([
                    'success' => true,
                    'data' => [
                        'camera1' => '<iframe src="https://login.partizancloud.com/rest/getPublicStream/id=MTk3ODQ=/code=Nzc3MDU4MDE4NjQ=?autoplay=true&lang=ru-RU&button_color=#19ACE4" frameborder="0" autoplay="1" allowfullscreen="1"></iframe>'
                    ]
                ]);
            }

            $result = [];

            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }

            return $this->asJson(['validation' => $result]);
        }

        return $this->render('webcam', [
            'model' => $model
        ]);
    }

    /**
     * Renders static page.
     *
     * @param string $alias page SEO URL
     * @param int $pageId page id
     * @param int $languageId language id
     * @param int $defaultLanguageId default language id
     * @return string|\yii\web\Response page content
     * @throws NotFoundHttpException if page not found
     */
    protected function renderStaticPage($alias, $pageId, $languageId, $defaultLanguageId)
    {
        $familyPageId = isset(Yii::$app->params['familyPageId']) ? Yii::$app->params['familyPageId'] : null;
        $hotelPageId = isset(Yii::$app->params['hotelPageId']) ? Yii::$app->params['hotelPageId'] : null;
        $karaokePageId = isset(Yii::$app->params['karaokePageId']) ? Yii::$app->params['karaokePageId'] : null;
        $skyGardenPageId = isset(Yii::$app->params['skyGardenPageId']) ? Yii::$app->params['skyGardenPageId'] : null;
        $eventHallPageId = isset(Yii::$app->params['eventHallPageId']) ? Yii::$app->params['eventHallPageId'] : null;
        $fitnessPageId = isset(Yii::$app->params['fitnessPageId']) ? Yii::$app->params['fitnessPageId'] : null;
        $cateringPageId = isset(Yii::$app->params['cateringPageId']) ? Yii::$app->params['cateringPageId'] : null;
        $contactsPageId = isset(Yii::$app->params['contactsPageId']) ? Yii::$app->params['contactsPageId'] : null;

        if (!$pageId && $languageId != $defaultLanguageId) {
            $pageId = SeoUrl::getQueryModelId('page_id', $alias, $defaultLanguageId);
        }

        if (!$pageId) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $page = Page::getByIdAndLanguageId($pageId, $languageId);

        if ($languageId != $defaultLanguageId) {
            $defaultPage = Page::getByIdAndLanguageId($pageId, $defaultLanguageId);
        } else {
            $defaultPage = $page;
        }

        // Family page
        if ((int) $pageId === (int) $familyPageId) {
            $familyPageBannerId = isset(Yii::$app->params['familyPageBannerId']) ? Yii::$app->params['familyPageBannerId'] : null;

            $banner = Banner::getByBannerId($familyPageBannerId);

            if ($banner) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($familyPageBannerId, $languageId);
            } else {
                $images = [];
            }

            if ($banner && empty($images)) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($familyPageBannerId, $defaultLanguageId);
            }

            $familyBannerImages = [];

            foreach ($images as $image) {
                if (!empty($image['image'])) {
                    list($width, $height) = getimagesize(BannerImage::getOriginalImagePath($image['image']));
                    $familyBannerImages[] = [
                        'banner_id' => $image['banner_id'],
                        'language_id' => $image['language_id'],
                        'title' => $image['title'],
                        'link' => $image['link'],
                        'image' => $image['image'],
                        'image_width' => $width,
                        'image_height' => $height,
                        'sort_order' => $image['sort_order'],
                    ];
                }
            }

            if (!empty($page['image'])) {
                list($imageWidth, $imageHeight) = getimagesize(Page::getOriginalImagePath($page['image']));
            } else {
                $imageWidth = $imageHeight = null;
            }

            $familyPageMenuDescr = !empty(Yii::$app->params['familyPageMenuDescr'][$languageId]) ? Yii::$app->params['familyPageMenuDescr'][$languageId]
                : (!empty(Yii::$app->params['familyPageMenuDescr'][$defaultLanguageId]) ? Yii::$app->params['familyPageMenuDescr'][$defaultLanguageId] : '');

            $eventCategoryId = !empty(Yii::$app->params['familyPageEventCategoryId']) ? Yii::$app->params['familyPageEventCategoryId'] : 0;
            $albumCategoryId = !empty(Yii::$app->params['familyPageAlbumCategoryId']) ? Yii::$app->params['familyPageAlbumCategoryId'] : 0;
            $familyPageSocialNetworkCategoryId = !empty(Yii::$app->params['familyPageSocialNetworkCategoryId']) ? Yii::$app->params['familyPageSocialNetworkCategoryId'] : 0;

            return $this->render('family', [
                'page' => $page,
                'defaultPage' => $defaultPage,
                'familyBannerImages' => $familyBannerImages,
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'familyPageMenuDescr' => $familyPageMenuDescr,
                'eventCategoryId' => $eventCategoryId,
                'albumCategoryId' => $albumCategoryId,
                'familyPageSocialNetworkCategoryId' => $familyPageSocialNetworkCategoryId,
                'pageUrl' => $alias,
                'galleryUrl' => Url::to($alias . '/gallery')
            ]);
        }

        // Sky Garden page

        if ((int)$pageId === (int)$skyGardenPageId) {
            $skyGardenPageBannerId = isset(Yii::$app->params['skyGardenPageBannerId']) ? Yii::$app->params['skyGardenPageBannerId'] : null;

            $banner = Banner::getByBannerId($skyGardenPageBannerId);

            if ($banner) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($skyGardenPageBannerId, $languageId);
            } else {
                $images = [];
            }

            if ($banner && empty($images)) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($skyGardenPageBannerId, $defaultLanguageId);
            }

            $skyGardenBannerImages = [];

            foreach ($images as $image) {
                if (!empty($image['image'])) {
                    list($width, $height) = getimagesize(BannerImage::getOriginalImagePath($image['image']));
                    $skyGardenBannerImages[] = [
                        'banner_id' => $image['banner_id'],
                        'language_id' => $image['language_id'],
                        'title' => $image['title'],
                        'link' => $image['link'],
                        'image' => $image['image'],
                        'image_width' => $width,
                        'image_height' => $height,
                        'sort_order' => $image['sort_order'],
                    ];
                }
            }

            if (!empty($page['image'])) {
                list($imageWidth, $imageHeight) = getimagesize(Page::getOriginalImagePath($page['image']));
            } else {
                $imageWidth = $imageHeight = null;
            }

            $skyGardenPageMenuDescr = !empty(Yii::$app->params['skyGardenPageMenuDescr'][$languageId]) ? Yii::$app->params['skyGardenPageMenuDescr'][$languageId]
                : (!empty(Yii::$app->params['skyGardenPageMenuDescr'][$defaultLanguageId]) ? Yii::$app->params['skyGardenPageMenuDescr'][$defaultLanguageId] : '');

            $eventCategoryId = !empty(Yii::$app->params['skyGardenPageEventCategoryId']) ? Yii::$app->params['skyGardenPageEventCategoryId'] : 0;
            $albumCategoryId = !empty(Yii::$app->params['skyGardenPageAlbumCategoryId']) ? Yii::$app->params['skyGardenPageAlbumCategoryId'] : 0;
            $skyGardenPageSocialNetworkCategoryId = !empty(Yii::$app->params['skyGardenPageSocialNetworkCategoryId']) ? Yii::$app->params['skyGardenPageSocialNetworkCategoryId'] : 0;

            return $this->render('sky-garden', [
                'page' => $page,
                'defaultPage' => $defaultPage,
                'skyGardenBannerImages' => $skyGardenBannerImages,
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'skyGardenPageMenuDescr' => $skyGardenPageMenuDescr,
                'eventCategoryId' => $eventCategoryId,
                'albumCategoryId' => $albumCategoryId,
                'skyGardenPageSocialNetworkCategoryId' => $skyGardenPageSocialNetworkCategoryId,
                'pageUrl' => $alias,
                'galleryUrl' => Url::to($alias . '/gallery')
            ]);
        }

        // Catering page

        if ((int)$pageId === (int)$cateringPageId) {
            if (!empty($page['image'])) {
                list($imageWidth, $imageHeight) = getimagesize(Page::getOriginalImagePath($page['image']));
            } else {
                $imageWidth = $imageHeight = null;
            }

            $cateringPageMenuDescr = !empty(Yii::$app->params['cateringPageMenuDescr'][$languageId]) ? Yii::$app->params['cateringPageMenuDescr'][$languageId]
                : (!empty(Yii::$app->params['cateringPageMenuDescr'][$defaultLanguageId]) ? Yii::$app->params['cateringPageMenuDescr'][$defaultLanguageId] : '');

            $eventCategoryId = !empty(Yii::$app->params['cateringPageEventCategoryId']) ? Yii::$app->params['cateringPageEventCategoryId'] : 0;
            $albumCategoryId = !empty(Yii::$app->params['cateringPageAlbumCategoryId']) ? Yii::$app->params['cateringPageAlbumCategoryId'] : 0;
            $cateringPageSocialNetworkCategoryId = !empty(Yii::$app->params['cateringPageSocialNetworkCategoryId']) ? Yii::$app->params['cateringPageSocialNetworkCategoryId'] : 0;
            $tariffCategoryId = !empty(Yii::$app->params['cateringPageTariffCategoryId']) ? Yii::$app->params['cateringPageTariffCategoryId'] : 0;

            $tariffs = [];
            $tariffsData = Tariff::getByTariffCategoryId($tariffCategoryId);

            foreach ($tariffsData as $tariff) {
                $bannerImages = BannerImage::getAllByBannerIdAndLanguageIdAR($tariff['banner_id'], $languageId);

                if (empty($bannerImages)) {
                    $bannerImages = BannerImage::getAllByBannerIdAndLanguageIdAR($tariff['banner_id'],
                        $defaultLanguageId);
                }

                $tariffs[] = ArrayHelper::merge($tariff, [
                    'banner_images' => $bannerImages
                ]);
            }

            return $this->render('catering', [
                'page' => $page,
                'defaultPage' => $defaultPage,
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'cateringPageMenuDescr' => $cateringPageMenuDescr,
                'eventCategoryId' => $eventCategoryId,
                'albumCategoryId' => $albumCategoryId,
                'cateringPageSocialNetworkCategoryId' => $cateringPageSocialNetworkCategoryId,
                'tariffs' => $tariffs,
                'pageUrl' => $alias,
                'galleryUrl' => Url::to($alias . '/gallery')
            ]);
        }

        // Hotel page
        if ((int)$pageId === (int)$hotelPageId) {
            $hotelPageHotelRoomsBlockBannerId = isset(Yii::$app->params['hotelPageHotelRoomsBlockBannerId']) ? Yii::$app->params['hotelPageHotelRoomsBlockBannerId'] : null;

            $banner = Banner::getByBannerId($hotelPageHotelRoomsBlockBannerId);

            if ($banner) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($hotelPageHotelRoomsBlockBannerId, $languageId);
            } else {
                $images = [];
            }

            if ($banner && empty($images)) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($hotelPageHotelRoomsBlockBannerId, $defaultLanguageId);
            }

            $hotelPageHotelRoomsBlockBannerImages = [];

            foreach ($images as $image) {
                if (!empty($image['image'])) {
                    list($width, $height) = getimagesize(BannerImage::getOriginalImagePath($image['image']));
                    $hotelPageHotelRoomsBlockBannerImages[] = [
                        'banner_id' => $image['banner_id'],
                        'language_id' => $image['language_id'],
                        'title' => $image['title'],
                        'link' => $image['link'],
                        'image' => $image['image'],
                        'image_width' => $width,
                        'image_height' => $height,
                        'sort_order' => $image['sort_order'],
                    ];
                }
            }

            if (!empty($page['image'])) {
                list($imageWidth, $imageHeight) = getimagesize(Page::getOriginalImagePath($page['image']));
            } else {
                $imageWidth = $imageHeight = null;
            }

            $hotelPageHotelBlockImage = isset(Yii::$app->params['hotelPageHotelBlockImage']) ? Yii::$app->params['hotelPageHotelBlockImage'] : null;
            $hotelPageHotelBlockImageUrl = SettingForm::getImageUrl($hotelPageHotelBlockImage, 800, 400);

            $eventCategoryId = !empty(Yii::$app->params['hotelPageEventCategoryId']) ? Yii::$app->params['hotelPageEventCategoryId'] : 0;
            $hotelPageSocialNetworkCategoryId = !empty(Yii::$app->params['hotelPageSocialNetworkCategoryId']) ? Yii::$app->params['hotelPageSocialNetworkCategoryId'] : 0;

            $hotelPageHotelRoomsBlockTitle = !empty(Yii::$app->params['hotelPageHotelRoomsBlockTitle'][$languageId]) ? Yii::$app->params['hotelPageHotelRoomsBlockTitle'][$languageId]
                : (!empty(Yii::$app->params['hotelPageHotelRoomsBlockTitle'][$defaultLanguageId]) ? Yii::$app->params['hotelPageHotelRoomsBlockTitle'][$defaultLanguageId] : '');

            $hotelPageMapBlockTitle = !empty(Yii::$app->params['hotelPageMapBlockTitle'][$languageId]) ? Yii::$app->params['hotelPageMapBlockTitle'][$languageId]
                : (!empty(Yii::$app->params['hotelPageMapBlockTitle'][$defaultLanguageId]) ? Yii::$app->params['hotelPageMapBlockTitle'][$defaultLanguageId] : '');

            $contactsMapCoordinates = !empty(Yii::$app->params['contactsMapCoordinates']) ? Yii::$app->params['contactsMapCoordinates'] : '';

            $rooms = Room::getRooms(['status' => Room::STATUS_ACTIVE]);

            return $this->render('hotel', [
                'page' => $page,
                'defaultPage' => $defaultPage,
                'hotelServices' => Hotelservice::getAll(),
                'hotelPageHotelBlockImageUrl' => $hotelPageHotelBlockImageUrl,
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'eventCategoryId' => $eventCategoryId,
                'hotelPageSocialNetworkCategoryId' => $hotelPageSocialNetworkCategoryId,
                'pageUrl' => $alias,
                'galleryUrl' => Url::to($alias . '/gallery'),
                'hotelPageHotelRoomsBlockTitle' => $hotelPageHotelRoomsBlockTitle,
                'hotelPageMapBlockTitle' => $hotelPageMapBlockTitle,
                'hotelPageHotelRoomsBlockBannerImages' => $hotelPageHotelRoomsBlockBannerImages,
                'contactsMapCoordinates' => $contactsMapCoordinates,
                'rooms' => $rooms,
            ]);
        }

        // Karaoke page
        if ((int)$pageId === (int)$karaokePageId) {
            $karaokePageBannerId = isset(Yii::$app->params['karaokePageBannerId']) ? Yii::$app->params['karaokePageBannerId'] : null;

            $banner = Banner::getByBannerId($karaokePageBannerId);

            if ($banner) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($karaokePageBannerId, $languageId);
            } else {
                $images = [];
            }

            if ($banner && empty($images)) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($karaokePageBannerId, $defaultLanguageId);
            }

            $karaokeBannerImages = [];

            foreach ($images as $image) {
                if (!empty($image['image'])) {
                    list($width, $height) = getimagesize(BannerImage::getOriginalImagePath($image['image']));
                    $karaokeBannerImages[] = [
                        'banner_id' => $image['banner_id'],
                        'language_id' => $image['language_id'],
                        'title' => $image['title'],
                        'link' => $image['link'],
                        'image' => $image['image'],
                        'image_width' => $width,
                        'image_height' => $height,
                        'sort_order' => $image['sort_order'],
                    ];
                }
            }

            if (!empty($page['image'])) {
                list($imageWidth, $imageHeight) = getimagesize(Page::getOriginalImagePath($page['image']));
            } else {
                $imageWidth = $imageHeight = null;
            }

            $karaokePageMenuDescr = !empty(Yii::$app->params['karaokePageMenuDescr'][$languageId]) ? Yii::$app->params['karaokePageMenuDescr'][$languageId]
                : (!empty(Yii::$app->params['karaokePageMenuDescr'][$defaultLanguageId]) ? Yii::$app->params['karaokePageMenuDescr'][$defaultLanguageId] : '');

            $eventCategoryId = !empty(Yii::$app->params['karaokePageEventCategoryId']) ? Yii::$app->params['karaokePageEventCategoryId'] : 0;
            $albumCategoryId = !empty(Yii::$app->params['karaokePageAlbumCategoryId']) ? Yii::$app->params['karaokePageAlbumCategoryId'] : 0;
            $karaokePageSocialNetworkCategoryId = !empty(Yii::$app->params['karaokePageSocialNetworkCategoryId']) ? Yii::$app->params['karaokePageSocialNetworkCategoryId'] : 0;

            return $this->render('karaoke', [
                'page' => $page,
                'defaultPage' => $defaultPage,
                'karaokeBannerImages' => $karaokeBannerImages,
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'karaokePageMenuDescr' => $karaokePageMenuDescr,
                'eventCategoryId' => $eventCategoryId,
                'albumCategoryId' => $albumCategoryId,
                'karaokePageSocialNetworkCategoryId' => $karaokePageSocialNetworkCategoryId,
                'pageUrl' => $alias,
                'galleryUrl' => Url::to($alias . '/gallery')
            ]);
        }

        // Event Hall page
        if ((int)$pageId === (int)$eventHallPageId) {
            if (file_exists(Page::getOriginalImagePath($page['image']))) {
                list($imageWidth, $imageHeight) = getimagesize(Page::getOriginalImagePath($page['image']));
            } else {
                $imageWidth = $imageHeight = null;
            }

            $eventHallPageBannerId = isset(Yii::$app->params['eventHallPageBannerId']) ? Yii::$app->params['eventHallPageBannerId'] : null;

            $banner = Banner::getByBannerId($eventHallPageBannerId);

            if ($banner) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($eventHallPageBannerId, $languageId);
            } else {
                $images = [];
            }

            if ($banner && empty($images)) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($eventHallPageBannerId, $defaultLanguageId);
            }

            $eventHallBannerImages = [];

            foreach ($images as $image) {
                if (!empty($image['image'])) {
                    list($width, $height) = getimagesize(BannerImage::getOriginalImagePath($image['image']));
                    $eventHallBannerImages[] = [
                        'banner_id' => $image['banner_id'],
                        'language_id' => $image['language_id'],
                        'title' => $image['title'],
                        'link' => $image['link'],
                        'image' => $image['image'],
                        'image_width' => $width,
                        'image_height' => $height,
                        'sort_order' => $image['sort_order'],
                    ];
                }
            }

            $eventCategoryId = !empty(Yii::$app->params['eventHallPageEventCategoryId']) ? Yii::$app->params['eventHallPageEventCategoryId'] : 0;
            $albumCategoryId = !empty(Yii::$app->params['eventHallPageAlbumCategoryId']) ? Yii::$app->params['eventHallPageAlbumCategoryId'] : 0;
            $tariffCategoryId = !empty(Yii::$app->params['eventHallPageTariffCategoryId']) ? Yii::$app->params['eventHallPageTariffCategoryId'] : 0;
            $eventHallPageSocialNetworkCategoryId = !empty(Yii::$app->params['eventHallPageSocialNetworkCategoryId']) ? Yii::$app->params['eventHallPageSocialNetworkCategoryId'] : 0;

            $tariffs = [];
            $tariffsData = Tariff::getByTariffCategoryId($tariffCategoryId);

            foreach ($tariffsData as $tariff) {
                $bannerImages = BannerImage::getAllByBannerIdAndLanguageIdAR($tariff['banner_id'], $languageId);

                if (empty($bannerImages)) {
                    $bannerImages = BannerImage::getAllByBannerIdAndLanguageIdAR($tariff['banner_id'],
                        $defaultLanguageId);
                }

                $tariffs[] = ArrayHelper::merge($tariff, [
                    'banner_images' => $bannerImages
                ]);
            }

            return $this->render('event-hall', [
                'page' => $page,
                'defaultPage' => $defaultPage,
                'eventHallBannerImages' => $eventHallBannerImages,
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'tariffs' => $tariffs,
                'eventCategoryId' => $eventCategoryId,
                'albumCategoryId' => $albumCategoryId,
                'eventHallPageSocialNetworkCategoryId' => $eventHallPageSocialNetworkCategoryId,
                'pageUrl' => $alias,
                'galleryUrl' => Url::to($alias . '/gallery')
            ]);
        }

        // Fitness page
        if ((int)$pageId === (int)$fitnessPageId) {
            $fitnessPageBannerId = isset(Yii::$app->params['fitnessPageBannerId']) ? Yii::$app->params['fitnessPageBannerId'] : null;

            $banner = Banner::getByBannerId($fitnessPageBannerId);

            if ($banner) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($fitnessPageBannerId, $languageId);
            } else {
                $images = [];
            }

            if ($banner && empty($images)) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($fitnessPageBannerId, $defaultLanguageId);
            }

            $fitnessBannerImages = [];

            foreach ($images as $image) {
                if (!empty($image['image'])) {
                    list($width, $height) = getimagesize(BannerImage::getOriginalImagePath($image['image']));
                    $fitnessBannerImages[] = [
                        'banner_id' => $image['banner_id'],
                        'language_id' => $image['language_id'],
                        'title' => $image['title'],
                        'link' => $image['link'],
                        'image' => $image['image'],
                        'image_width' => $width,
                        'image_height' => $height,
                        'sort_order' => $image['sort_order'],
                    ];
                }
            }

            if (!empty($page['image'])) {
                list($imageWidth, $imageHeight) = getimagesize(Page::getOriginalImagePath($page['image']));
            } else {
                $imageWidth = $imageHeight = null;
            }

            $fitnessPageTariffsDescr = !empty(Yii::$app->params['fitnessPageTariffsDescr'][$languageId]) ? Yii::$app->params['fitnessPageTariffsDescr'][$languageId]
                : (!empty(Yii::$app->params['fitnessPageTariffsDescr'][$defaultLanguageId]) ? Yii::$app->params['fitnessPageTariffsDescr'][$defaultLanguageId] : '');
            $fitnessPageTeamDescr = !empty(Yii::$app->params['fitnessPageTeamDescr'][$languageId]) ? Yii::$app->params['fitnessPageTeamDescr'][$languageId]
                : (!empty(Yii::$app->params['fitnessPageTeamDescr'][$defaultLanguageId]) ? Yii::$app->params['fitnessPageTeamDescr'][$defaultLanguageId] : '');

            $eventCategoryId = !empty(Yii::$app->params['fitnessPageEventCategoryId']) ? Yii::$app->params['fitnessPageEventCategoryId'] : 0;
            $albumCategoryId = !empty(Yii::$app->params['fitnessPageAlbumCategoryId']) ? Yii::$app->params['fitnessPageAlbumCategoryId'] : 0;
            $fitnessPageSocialNetworkCategoryId = !empty(Yii::$app->params['fitnessPageSocialNetworkCategoryId']) ? Yii::$app->params['fitnessPageSocialNetworkCategoryId'] : 0;

            return $this->render('fitness', [
                'page' => $page,
                'defaultPage' => $defaultPage,
                'fitnessBannerImages' => $fitnessBannerImages,
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'fitnessPageTariffsDescr' => $fitnessPageTariffsDescr,
                'fitnessPageTeamDescr' => $fitnessPageTeamDescr,
                'eventCategoryId' => $eventCategoryId,
                'albumCategoryId' => $albumCategoryId,
                'fitnessPageSocialNetworkCategoryId' => $fitnessPageSocialNetworkCategoryId,
                'team' => Team::getAll(),
                'pageUrl' => $alias,
                'galleryUrl' => Url::to($alias . '/gallery')
            ]);
        }

        // Contacts page
        if ((int) $pageId === (int) $contactsPageId) {
            $contactsPageBannerId = isset(Yii::$app->params['contactsPageBannerId']) ? Yii::$app->params['contactsPageBannerId'] : null;

            $banner = Banner::getByBannerId($contactsPageBannerId);

            if ($banner) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($contactsPageBannerId, $languageId);
            } else {
                $images = [];
            }

            if ($banner && empty($images)) {
                $images = BannerImage::getAllByBannerIdAndLanguageIdAR($contactsPageBannerId, $defaultLanguageId);
            }

            $contactsBannerImages = [];

            foreach ($images as $image) {
                if (!empty($image['image'])) {
                    list($width, $height) = getimagesize(BannerImage::getOriginalImagePath($image['image']));
                    $contactsBannerImages[] = [
                        'banner_id' => $image['banner_id'],
                        'language_id' => $image['language_id'],
                        'title' => $image['title'],
                        'link' => $image['link'],
                        'image' => $image['image'],
                        'image_width' => $width,
                        'image_height' => $height,
                        'sort_order' => $image['sort_order'],
                    ];
                }
            }

            if (!empty($page['image'])) {
                list($imageWidth, $imageHeight) = getimagesize(Page::getOriginalImagePath($page['image']));
            } else {
                $imageWidth = $imageHeight = null;
            }

            $address = !empty(Yii::$app->params['address'][$languageId]) ? Yii::$app->params['address'][$languageId] :
                (!empty(Yii::$app->params['address'][$defaultLanguageId]) ? Yii::$app->params['address'][$defaultLanguageId] : '');
            $contactsMapCoordinates = !empty(Yii::$app->params['contactsMapCoordinates']) ? Yii::$app->params['contactsMapCoordinates'] : '';
            $phone = !empty(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : '';
            $supportEmail = !empty(Yii::$app->params['supportEmail']) ? Yii::$app->params['supportEmail'] : '';

            return $this->render('contacts', [
                'page' => $page,
                'defaultPage' => $defaultPage,
                'contactsBannerImages' => $contactsBannerImages,
                'imageWidth' => $imageWidth,
                'imageHeight' => $imageHeight,
                'address' => $address,
                'contactsMapCoordinates' => $contactsMapCoordinates,
                'phone' => $phone,
                'email' => $supportEmail,
            ]);
        }

        return $this->render('page', [
            'page' => $page,
            'defaultPage' => $defaultPage,
        ]);
    }

    /**
     * Renders album page.
     * @param int $albumId album id
     * @return string|\yii\web\Response page content
     * @throws NotFoundHttpException if page not found
     */
    protected function renderAlbumPage($albumId)
    {
        $album = Album::getAlbum($albumId);
        if (!empty($album)) {
            if (Yii::$app->request->referrer !== null) {
                $backUrl = Yii::$app->request->referrer;
            } else {
                $backUrl = substr(Yii::$app->request->url, 0, strrpos(Yii::$app->request->url, '/'));
            }

            return $this->render('album', [
                'backUrl' => $backUrl,
                'album' => $album,
                'albumImages' => AlbumImage::getAllByAlbumId($albumId)
            ]);
        }
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * Renders gallery page.
     *
     * @param int $albumCategoryId album category id
     * @param string $pageUrl page URL
     * @return string|\yii\web\Response page content
     * @throws NotFoundHttpException if page not found
     */
    protected function renderGalleryPage($albumCategoryId, $pageUrl)
    {
        $albums = Album::getByAlbumCategoryId($albumCategoryId, Album::STATUS_ACTIVE, 'a.created_at DESC', 10);

        if (!empty($albums)) {
            $pageTitle = Yii::t('family', 'Галереи') . ' ' . $albums[0]['album_category_name'];

            return $this->render('gallery', [
                'albums' => $albums,
                'pageUrl' => $pageUrl,
                'pageTitle' => $pageTitle
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}
