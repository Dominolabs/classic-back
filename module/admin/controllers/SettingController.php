<?php

namespace app\module\admin\controllers;

use Yii;
use app\components\ImageBehavior;
use app\module\admin\models\User;
use app\module\admin\models\Language;
use app\module\admin\models\SettingForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;

class SettingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            /** @var User $identity */
                            $identity = Yii::$app->user->identity;

                            return $identity->isUser || $identity->isAdminHotel;
                        },
                        'denyCallback' => function ($rule, $action) {
                            $this->redirect('/');
                        },
                    ], [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all settings.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $file = __DIR__ . '/../../../config/params.inc';

        $content = file_get_contents($file);
        $array = unserialize(base64_decode($content));

        /** @var SettingForm|ImageBehavior $model */
        $model = new SettingForm();
        $model->setAttributes($array, false);

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        if ($model->load(Yii::$app->request->post()) && $model->validateEmailList()) {
            $newFamilyPageMenuImageFile = UploadedFile::getInstance($model, 'familyPageMenuImageFile');
            $newHotelPageHotelBlockImageFile = UploadedFile::getInstance($model, 'hotelPageHotelBlockImageFile');
            $newKaraokePageMenuImageFile = UploadedFile::getInstance($model, 'karaokePageMenuImageFile');
            $newSkyGardenPageMenuImageFile = UploadedFile::getInstance($model, 'skyGardenPageMenuImageFile');
            $newCateringPageMenuImageFile = UploadedFile::getInstance($model, 'cateringPageMenuImageFile');

            $skyGardenMenuPdfFile = UploadedFile::getInstance($model, 'skyGardenMenuPdfFile');
            $cateringMenuPdfFile = UploadedFile::getInstance($model, 'cateringMenuPdfFile');
            $familyPageMenuPdfFile = UploadedFile::getInstance($model, 'familyPageMenuPdfFile');
            $karaokePageMenuPdfFile = UploadedFile::getInstance($model, 'karaokePageMenuPdfFile');


            $pizzaConstructorBannerImage = UploadedFile::getInstance($model, 'pizzaConstructorBannerImage');
            $pizzaConstructorBannerImageEn = UploadedFile::getInstance($model, 'pizzaConstructorBannerImageEn');


            if ($pizzaConstructorBannerImage !== null) {
                $model->removeImage($model->pizzaConstructorBannerImage); // Remove old image
                $model->pizzaConstructorBannerImage = $pizzaConstructorBannerImage;
                $model->pizzaConstructorBanner = $model->uploadImage('pizzaConstructorBannerImage');
            }

            if ($pizzaConstructorBannerImageEn !== null) {
                $model->removeImage($model->pizzaConstructorBannerImageEn); // Remove old image
                $model->pizzaConstructorBannerImageEn = $pizzaConstructorBannerImageEn;
                $model->pizzaConstructorBannerEn = $model->uploadImage('pizzaConstructorBannerImageEn');
            }


            if ($newFamilyPageMenuImageFile !== null) {
                $model->removeImage($model->familyPageMenuImage); // Remove old image
                $model->familyPageMenuImageFile = $newFamilyPageMenuImageFile;
                $model->familyPageMenuImage = $model->uploadImage('familyPageMenuImageFile');
            }


            if ($newHotelPageHotelBlockImageFile !== null) {
                $model->removeImage($model->hotelPageHotelBlockImage); // Remove old image
                $model->hotelPageHotelBlockImageFile = $newHotelPageHotelBlockImageFile;
                $model->hotelPageHotelBlockImage = $model->uploadImage('hotelPageHotelBlockImageFile');
            }

            if ($newKaraokePageMenuImageFile !== null) {
                $model->removeImage($model->karaokePageMenuImage); // Remove old image
                $model->karaokePageMenuImageFile = $newKaraokePageMenuImageFile;
                $model->karaokePageMenuImage = $model->uploadImage('karaokePageMenuImageFile');
            }

            if ($newSkyGardenPageMenuImageFile !== null) {
                $model->removeImage($model->skyGardenPageMenuImage); // Remove old image
                $model->skyGardenPageMenuImageFile = $newSkyGardenPageMenuImageFile;
                $model->skyGardenPageMenuImage = $model->uploadImage('skyGardenPageMenuImageFile');
            }

            if ($newCateringPageMenuImageFile !== null) {
                $model->removeImage($model->cateringPageMenuImage); // Remove old image
                $model->cateringPageMenuImageFile = $newCateringPageMenuImageFile;
                $model->cateringPageMenuImage = $model->uploadImage('cateringPageMenuImageFile');
            }

            if ($skyGardenMenuPdfFile !== null) {
                $model->skyGardenMenuPdfFile = $skyGardenMenuPdfFile;
                $model->skyGardenMenuPdf = $model->uploadFile('skyGardenMenuPdfFile');
            }

            if ($cateringMenuPdfFile !== null) {
                $model->cateringMenuPdfFile = $cateringMenuPdfFile;
                $model->cateringMenuPdf = $model->uploadFile('cateringMenuPdfFile');
            }

            if ($familyPageMenuPdfFile !== null) {
                $model->familyPageMenuPdfFile = $familyPageMenuPdfFile;
                $model->familyPageMenuPdf = $model->uploadFile('familyPageMenuPdfFile');
            }

            if ($karaokePageMenuPdfFile !== null) {
                $model->karaokePageMenuPdfFile = $karaokePageMenuPdfFile;
                $model->karaokePageMenuPdf = $model->uploadFile('karaokePageMenuPdfFile');
            }

            $string = base64_encode(serialize($model->getAttributes()));
            file_put_contents($file, $string);
            Yii::$app->session->setFlash('success', 'Настройки успешно сохранены.');
        }

        $placeholder = ImageBehavior::placeholder(100, 100);
        $placeholder_banner = ImageBehavior::placeholder(290, 60);

        return $this->render('index', [
            'model' => $model,
            'languages' => $languages,
            'placeholder' => $placeholder,
            'placeholder_banner' => $placeholder_banner
        ]);
    }

    public function actionDeleteImage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $allowedFields = ['pizzaConstructorBanner', 'pizzaConstructorBannerEn'];

        if (!in_array($field, $allowedFields)) {
            return ['success' => false, 'message' => 'Невалідне поле'];
        }

        $file = __DIR__ . '/../../../config/params.inc';
        $content = file_get_contents($file);
        $array = unserialize(base64_decode($content));

        if (!empty($array[$field]) && file_exists($array[$field])) {
            @unlink($array[$field]);
        }

        $array[$field] = null;

        file_put_contents($file, base64_encode(serialize($array)));

        return ['success' => true];
    }
}
