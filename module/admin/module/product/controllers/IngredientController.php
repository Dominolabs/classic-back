<?php

namespace app\module\admin\module\product\controllers;

use app\components\ImageBehavior;
use app\module\admin\models\User;
use app\module\admin\models\Language;
use app\module\admin\module\product\models\IngredientDescription;
use app\module\admin\module\product\models\Ingredient;
use app\module\admin\module\product\models\IngredientSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class IngredientController extends Controller
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

                            return $identity->isUser;
                        },
                        'denyCallback' => function ($rule, $action) {
                            $this->redirect('/');
                        },
                    ],
                    [
                        'allow' => false,
                        'roles' => ['@'],
                        'matchCallback' => static function () {
                            /** @var User $identity */
                            $identity = Yii::$app->user->identity;

                            return $identity->isAdmin;
                        },
                        'denyCallback' => function () {
                            throw new \yii\web\HttpException(404);
                        },
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IngredientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var Ingredient | ImageBehavior $ingredient */
        $ingredient = new Ingredient();
        $ingredientDescriptions = [];
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $ingredientDescription = new IngredientDescription();

            if ((int) $language['language_id'] === (int) Language::getLanguageIdByCode(Yii::$app->language)) {
                $ingredientDescription->scenario = 'language-is-system';
            }

            $ingredientDescriptions[$language['language_id']] = $ingredientDescription;
        }

        if ($ingredient->load(Yii::$app->request->post()) && IngredientDescription::loadMultiple($ingredientDescriptions,
                Yii::$app->request->post())) {
            if (empty($ingredient->category_id)) {
                $ingredient->category_id = 0;
            }

            if (empty($ingredient->price)) {
                $ingredient->price = 0.0000;
            }

            $ingredient->imageFile = UploadedFile::getInstance($ingredient, 'imageFile');

            $isValid = $ingredient->validate();

            if ($ingredient->imageFile !== null) {
                $ingredient->image = $ingredient->uploadImage();
            }

            $isValid = $ingredient->validate('image') && $isValid;

            $isValid = IngredientDescription::validateMultiple($ingredientDescriptions,
                    Yii::$app->request->post()) && $isValid;

            if ($isValid && $ingredient->save(false)) {
                // Save descriptions
                foreach ($ingredientDescriptions as $key => $ingredientDescription) {
                    $ingredientDescription->ingredient_id = $ingredient->ingredient_id;
                    $ingredientDescription->language_id = $key;
                    $ingredientDescription->save(false);
                }

                return $this->goBack();
            }
        }

        if (empty($ingredient->sort_order)) {
            $ingredient->sort_order = 1;
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('create', [
            'ingredient' => $ingredient,
            'descriptions' => $ingredientDescriptions,
            'languages' => $languages,
            'placeholder' => $placeholder,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception|\Throwable
     */
    public function actionUpdate($id)
    {
        /** @var Ingredient|ImageBehavior $ingredient */
        $ingredient = $this->findModel($id);
        $ingredientDescriptions = [];
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = IngredientDescription::findOne([
                'ingredient_id' => $ingredient->ingredient_id,
                'language_id' => $language['language_id']
            ]);

            $ingredientDescriptions[$language['language_id']] = !empty($description) ? $description : new IngredientDescription();

            if ((int) $language['language_id'] === (int) Language::getLanguageIdByCode(Yii::$app->language)) {
                $ingredientDescriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($ingredient->load(Yii::$app->request->post()) && IngredientDescription::loadMultiple($ingredientDescriptions,
                Yii::$app->request->post())) {

            if (empty($ingredient->category_id)) {
                $ingredient->category_id = 0;
            }

            if (empty($ingredient->price)) {
                $ingredient->price = 0.0000;
            }

            $newImageFile = UploadedFile::getInstance($ingredient, 'imageFile');

            if ($newImageFile !== null) {
                $ingredient->removeImage($ingredient->image); // Remove old image
                $ingredient->imageFile = $newImageFile;
                $isValid = $ingredient->validate();
                $ingredient->image = $ingredient->uploadImage();
            } else {
                $isValid = $ingredient->validate();
            }

            $isValid = IngredientDescription::validateMultiple($ingredientDescriptions,
                    Yii::$app->request->post()) && $isValid;

            if ($isValid && $ingredient->save(false)) {
                // Update descriptions
                foreach ($ingredientDescriptions as $key => $description) {
                    $description->ingredient_id = $ingredient->ingredient_id;
                    $description->language_id = $key;
                    $description->save(false);
                }
                return $this->goBack();
            }
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('update', [
            'ingredient' => $ingredient,
            'descriptions' => $ingredientDescriptions,
            'languages' => $languages,
            'placeholder' => $placeholder,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception|\Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        IngredientDescription::removeByIngredientId($id);

        return $this->goBack();
    }

    /**
     * @param integer $id
     * @return Ingredient
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Ingredient::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
