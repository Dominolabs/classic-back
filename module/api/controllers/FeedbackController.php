<?php

namespace app\module\api\controllers;


use app\module\admin\models\Language;
use app\module\admin\models\Subscriber;
use app\module\admin\models\SubscriberForm;
use app\module\admin\models\Vacancy;
use app\module\admin\models\VacancyRequest;
use app\module\admin\models\VacancyRequestForm;
use app\module\admin\module\feedback\models\Feedback;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\module\admin\module\feedback\models\FeedbackForm;


class FeedbackController extends BaseApiController
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'vacancies' => ['GET'],
                'vacancy-apply' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @param $lang
     * @return array
     */
    public function actionVacancies($lang)
    {
        try {
            Yii::$app->language = $lang;
            $vacancies = Vacancy::findAll(['status' => Vacancy::STATUS_ACTIVE]);
            $provider = new ArrayDataProvider([
                'allModels' => $vacancies
            ]);
            $vacancies = $provider->getModels();

            return [
                'status' => 'success',
                'data' => [
                    'vacancies' => $vacancies
                ]
            ];
        } catch (\Throwable $exception) {
            return $this->errorResponseHandler($exception);
        }
    }


    /**
     * @param $lang
     * @return array
     */
    public function actionVacancyApply($lang): ?array
    {
        try {
            Yii::$app->language = $lang;

            $form = new VacancyRequestForm();

            $request_data = Yii::$app->request->post();
            $form->attributes = $request_data;

            $photoFile = UploadedFile::getInstanceByName('photoFile');
            if ($photoFile !== null) {
                $form->photoFile = $photoFile;
            }

            $is_valid = $form->validate();
            if (!$is_valid) {
                $errors = $form->getErrors();
                return $this->validationErrorsResponseHandler($errors);
            }

            //Check if such vacancy exists
            $vacancy_id = $request_data['vacancy_id'];
            $vacancy = Vacancy::findOne($vacancy_id);
            if(empty($vacancy)){
                $errors = [
                    'vacancy_id'=> [Yii::t('vacancy', 'Вакансія на яку надіслана заявка відсутня')]
                ];
                return $this->validationErrorsResponseHandler($errors);
            }

            //Store file  on server and get file path
            if (isset($form->photoFile)) {
                $form->photo = $form->uploadImage('photoFile');
            }

            $model = new VacancyRequest();
            $model->attributes = $form->getAttributes();
            $model->photo = $form->photo;
            $model->lang_id = Language::getLanguageIdByCode($lang);

            if ($model->save(false)) {
                return [
                    'status' => 'success',
                    'message' => Yii::t('vacancy', 'Дані успішно збережено.'),
                ];
            } else {
                throw new \Exception('Error in saving model to DB');
            }

        } catch (\Throwable $exception) {
            return $this->errorResponseHandler($exception);
        }
    }


    /**
     * @param $lang
     * @return array
     */
    public function actionFeedbackApply($lang): ?array
    {
        try {
            Yii::$app->language = $lang;

            $form = new FeedbackForm();

            $request_data = Yii::$app->request->post();
            $form->attributes = $request_data;

            $is_valid = $form->validate();

            if (!$is_valid) {
                $errors = $form->getErrors();
                return $this->validationErrorsResponseHandler($errors);
            }

            $model = new Feedback();
            $model->attributes = $form->getAttributes();
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'feedback-html']
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' робот'])
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject('Новый отзыв Classic')
                ->send();
            $message = Yii::t('feedback', 'Дякуємо за Ваше звернення. Наш менеджер найближчим часом сконтактує з Вами.');
            if ($model->save(false)) {
                return [
                    'status' => 'success',
                    'message' => $message,
                ];
            } else {
                throw new \Exception('Error in saving model to DB');
            }

        } catch (\Throwable $exception) {
            return $this->errorResponseHandler($exception);
        }
    }


    /**
     * @param $lang
     * @return array
     */
    public function actionSubscribe($lang)
    {
        try {
            Yii::$app->language = $lang;

            $form = new SubscriberForm();

            $request_data = Yii::$app->request->post();
            $form->attributes = $request_data;

            $is_valid = $form->validate();

            if (!$is_valid) {
                $errors = $form->getErrors();
                return $this->validationErrorsResponseHandler($errors);
            }

            $model = new Subscriber();
            $model->attributes = $form->getAttributes();

            if ($model->save(false)) {
                return [
                    'status' => 'success',
                    'message' => Yii::t('feedback', 'Дані успішно збережено'),
                ];
            } else {
                throw new \Exception('Error in saving model to DB');
            }

        } catch (\Throwable $exception) {
            return $this->errorResponseHandler($exception);
        }
    }



    /**
     * @param \Throwable $exception
     * @return array
     */
    protected function errorResponseHandler(\Throwable $exception): array
    {
        $error = $this->formErrorForLogging($exception);
        Yii::error($error, 'feedback');
        $response = [
            'status' => 'error',
            'error' => 'Internal server error',
            'message' => $exception->getMessage()
        ];
        Yii::$app->response->statusCode = 500;
        return $response;
    }


    /**
     * @param $errors
     * @return array
     */
    protected function validationErrorsResponseHandler($errors): array
    {
        $response = [
            'status' => 'error',
            'errors' => $errors
        ];
        Yii::$app->response->statusCode = 422;
        return $response;
    }


    /**
     * @param \Throwable $exception
     * @return array
     */
    protected function formErrorForLogging(\Throwable $exception): array
    {
        return [
            'url' => Yii::$app->request->absoluteUrl,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage()
        ];
    }
}
