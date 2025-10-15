<?php

namespace app\module\admin\module\product\controllers;

use app\components\ImageBehavior;
use app\module\admin\models\Classic;
use app\module\admin\models\ClassicDescription;
use app\module\admin\models\SeoUrl;
use app\module\admin\models\User;
use app\module\admin\models\Language;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ClassicController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['@'],
                        'matchCallback' => static function ($rule, $action) {
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
     * @return mixed index view
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        Url::remember();

        /**
         * @var Classic|ImageBehavior $classic
         */
        $classic = $this->findModel(1);

        $classicDescriptions = [];
        $seoUrls = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = ClassicDescription::findOne([
                'product_id' => $classic->product_id,
                'language_id' => $language['language_id']
            ]);
            $seoUrl = SeoUrl::findOne([
                'query' => 'classic_id=' . $classic->product_id,
                'language_id' => $language['language_id']
            ]);

            $classicDescriptions[$language['language_id']] = $description ?? new ClassicDescription();
            $seoUrls[$language['language_id']] = $seoUrl ?? new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $classicDescriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($classic->load(Yii::$app->request->post())
            && ClassicDescription::loadMultiple($classicDescriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            if (empty($classic->price)) {
                $classic->price = 0.0000;
            }
            if (empty($classic->price2)) {
                $classic->price2 = 0.0000;
            }

            $newImageFile = UploadedFile::getInstance($classic, 'imageFile');

            if ($newImageFile !== null) {
                $classic->removeImage($classic->image); // Remove old image
                $classic->imageFile = $newImageFile;

                $isValid = $classic->validate();

                $classic->image = $classic->uploadImage();
            } else {
                $isValid = $classic->validate();
            }

            $isValid = ClassicDescription::validateMultiple($classicDescriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $classic->save(false)) {
                foreach ($classicDescriptions as $key => $productDescription) {
                    $productDescription->product_id = $classic->product_id;
                    $productDescription->language_id = $key;
                    $productDescription->save(false);
                }

                $name = $classicDescriptions[Language::getLanguageIdByCode(Yii::$app->language)]->name;

                /**
                 * @var int $key language id
                 * @var SeoUrl $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'classic_id=' . $classic->product_id;

                    if (empty($seoUrl->keyword)) {
                        $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($name), $key);
                    }

                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('index', [
            'product' => $classic,
            'descriptions' => $classicDescriptions,
            'languages' => $languages,
            'placeholder' => $placeholder,
            'seoUrls' => $seoUrls,
        ]);
    }

    /**
     * @param integer $id
     * @return Classic
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Classic
    {
        if (($model = Classic::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
