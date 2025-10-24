<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;


use app\components\ImageBehavior;
use yii\behaviors\TimestampBehavior;
use Imagine\Image\ManipulatorInterface;
use yii\db\Query;
use Yii;

/**
 * @property int $vacancy_request_id
 * @property int $vacancy_id
 * @property int $lang_id
 * @property string $full_name
 * @property int $age
 * @property string $phone
 * @property string $social_links
 * @property string $email
 * @property string $reason
 * @property string $photo
 * @property int $created_at
 * @property int $updated_at
 */


class VacancyRequest extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%vacancy_requests}}';
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['vacancy_id', 'full_name', 'phone', 'email', 'reason'], 'required'],
            [['created_at', 'updated_at', 'vacancy_id', 'lang_id'], 'integer'],
            [['age'], 'integer', 'min' => 16, 'max' => 65],
            [['full_name', 'phone', 'email', 'social_links'],  'string', 'max' => 255],
            [['reason'],  'string', 'max' => 3000],
            [['email'],  'email'],
            [['photo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'vacancy_request_id' => Yii::t('vacancy', 'ID запиту'),
            'vacancy_id'         => Yii::t('vacancy', 'Вакансія'),
            'full_name'          => Yii::t('vacancy', 'ФІО'),
            'age'                => Yii::t('vacancy', 'Вік'),
            'phone'              => Yii::t('vacancy', 'Телефон'),
            'social_links'       => Yii::t('vacancy', 'Соціальні мережі'),
            'email'              => Yii::t('vacancy', 'Email'),
            'reason'             => Yii::t('vacancy', 'Причина'),
            'photo'              => Yii::t('vacancy', 'Фото'),
            'created_at'         => Yii::t('vacancy', 'Создано'),
            'updated_at'         => Yii::t('vacancy', 'Обнволено')
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            'photo' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'vacancy_requests',
            ]
        ];
    }




    public static function getImageUrl(
        $filename,
        $width,
        $height,
        $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND,
        $quality = 100
    ) {
        return (new self())->resizeImage($filename, $width, $height, $mode, $quality);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVacancy()
    {
        return $this->hasOne(Vacancy::class, ['vacancy_id' => 'vacancy_id']);
    }



    /**
     * @return mixed
     */
    public function getVacancyName()
    {
        return $this->vacancy->vacancyDescription->name ?? null;
    }

}
