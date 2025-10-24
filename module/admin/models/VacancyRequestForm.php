<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;

use app\components\ImageBehavior;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use Yii;
use yii\web\UploadedFile;

/**
 * @property int $vacancy_id
 * @property string $full_name
 * @property int $age
 * @property string $phone
 * @property string $social_links
 * @property string $email
 * @property string $reason
 * @property string $photo
 *
 * @property $photoFile
 */


class VacancyRequestForm extends Model
{
    public $vacancy_id;
    public $full_name;
    public $age;
    public $email;
    public $phone;
    public $social_links;
    public $reason;
    public $photoFile;
    public $photo;


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['vacancy_id', 'full_name', 'age', 'email', 'phone', 'reason', 'social_links'], 'required'],
            [['vacancy_id'], 'integer'],
            [['age'], 'integer', 'min' => 16, 'max' => 65],
            [['full_name', 'phone', 'email', 'social_links'],  'string', 'max' => 255],
            [['reason'],  'string', 'max' => 3000],
            [['email'],  'email'],
            [['photo'], 'string', 'max' => 255],
            [['photoFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 10],
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
}
