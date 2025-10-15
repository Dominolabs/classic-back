<?php

namespace app\module\api\controllers;

use Yii;
use app\module\admin\models\Language;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

class LanguageController extends BaseApiController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'languages' => ['GET'],
                'translations' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @return array
     */
    public function actionLanguages(): array
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Language::find()
                ->where(['status' => Language::STATUS_ACTIVE])
                ->orderBy('sort_order ASC'),
            'pagination' => false,
        ]);

        return [
            'status' => 'success',
            'data' => [
                'defaultLanguageCode' => Yii::$app->urlManager->getDefaultLanguage(),
                'languages' => $dataProvider->getModels()
            ],
        ];
    }

    /**
     * @param string $lang
     * @return array
     * @throws yii\db\Exception
     */
    public function actionTranslations($lang = null): array
    {
        if ($lang !== null) {
            Yii::$app->language = $lang;
        }

        $query = (new Query())->select(['message' => 't1.message', 'translation' => 't2.translation'])
            ->from(['t1' => '{{%source_message}}', 't2' => '{{%message}}'])
            ->where([
                't1.category' => 'site',
                't1.source_message_id' => new Expression('[[t2.source_message_id]]'),
                't2.language_id' => Language::getLanguageIdByCode(Yii::$app->language),
            ]);

        $messages = $query->createCommand()->queryAll();

        $data = [];

        foreach ($messages as $message) {
            $data[$message['message']] = $message['translation'];
        }

        return [
            'status' => 'success',
            'data' => $data,
        ];
    }
}
