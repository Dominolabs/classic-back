<?php

namespace app\module\admin\module\feedback\models;

use Yii;
use Imagine\Image\ManipulatorInterface;
use app\module\admin\models\Language;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * @property int $feedback_id
 * @property string $text
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tbl_feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'text', 'phone'], 'required'],
            ['email', 'email'],
            [['status', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'feedback_id' => 'ID',
            'name' =>  Yii::t('reviews', 'Ім\'я'),
            'phone' => Yii::t('reviews', 'Телефон'),
            'email' => Yii::t('reviews', 'Email'),
            'text' => Yii::t('reviews', 'Відгук'),
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }


    /**
     * @return int|string
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }


    /**
     *
     */
    public function sendAdminEmail()
    {
//        $siteName = isset(Yii::$app->params['siteName']) ? Yii::$app->params['siteName'] : Yii::$app->name;
//
//        return Yii::$app
//            ->mailer
//            ->compose(
//                ['html' => '@app/mail/frontend/adminNewFeedback-html', 'text' => '@app/mail/frontend/adminNewFeedback-text'],
//                [
//                    'feedback' => $this,
//                ]
//            )
//            ->setFrom([Yii::$app->params['supportEmail'] => $siteName . ' робот'])
//            ->setTo(Yii::$app->params['hotelServiceEmail'])
//            ->setSubject('Новый отзыв № ' . $this->feedback_id)
//            ->send();
    }
}
