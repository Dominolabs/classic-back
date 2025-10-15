<?php


namespace app\module\admin\module\product\controllers;

use app\components\ImageBehavior;
use app\module\admin\models\Language;
use app\module\admin\models\SettingForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Yii;

class CustomizationController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $model = $this->getSettingFormInstance();
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        return $this->render('index', [
            'model' => $model,
            'languages' => $languages
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionAddBadge()
    {
        if (Yii::$app->request->isAjax) {
            if (empty(Yii::$app->request->post('badge'))) return;
            $badge = Yii::$app->request->post('badge');
            $model = $this->getSettingFormInstance();
            $empty = true;
            foreach (array_keys(Language::getAll(Language::STATUS_ACTIVE)) as $key) {
                $empty = $empty && empty($badge['name'][$key]);
            }
            if ($empty) return;
            $model->productBadges[] = $badge;
            $this->saveSettings($model);
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDeleteBadge()
    {
        if (Yii::$app->request->isAjax) {
            if (is_null($key = Yii::$app->request->post('key'))) return;
            $model = $this->getSettingFormInstance();
            if (array_key_exists($key, $model->productBadges)) {
                unset($model->productBadges[$key]);
                $this->saveSettings($model);
            }
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    //==Private==//

    /**
     * @return ImageBehavior|SettingForm
     */
    private function getSettingFormInstance()
    {
        $file = Yii::getAlias('@app/config/params.inc');
        $content = file_get_contents($file);
        $array = unserialize(base64_decode($content));

        /** @var SettingForm|ImageBehavior $model */
        $model = new SettingForm();
        $model->setAttributes($array, false);
        return $model;
    }

    /**
     * @param SettingForm $model
     */
    private function saveSettings(SettingForm $model)
    {
        $file = Yii::getAlias('@app/config/params.inc');
        $string = base64_encode(serialize($model->getAttributes()));
        file_put_contents($file, $string);
        Yii::$app->session->setFlash('success', 'Настройки успешно сохранены.');
    }
}