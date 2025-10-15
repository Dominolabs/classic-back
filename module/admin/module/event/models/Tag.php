<?php

namespace app\module\admin\module\event\models;

use Yii;
use Imagine\Image\ManipulatorInterface;
use app\module\admin\models\Language;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use app\module\admin\module\event\models\TagDescription;

/**
 * @property int $tag_id
 * @property int $created_at
 * @property int $updated_at
 * @property $tagDescription
 */



class Tag extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tbl_tag';
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'tag_id' => 'ID',
            'tagName' => 'Название',
            'created_at' => 'Создано',
            'updated_at' => 'Онволено'
        ];
    }


    /**
     * @return array|false
     */
    public function fields(): array
    {
        return [
            'tag_id',
            'name' => 'tagName'
        ];
    }



    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }


    /**
     * ActiveRelation to EventDescription model.
     *
     * @return \yii\db\ActiveQuery active query instance
     */
    public function getTagDescription()
    {
        return $this->hasOne(TagDescription::class, ['tag_id' => 'tag_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }


    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getEvents(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Event::class, ['event_id' => 'event_id'])
            ->viaTable('tbl_event_tag', ['tag_id' => 'tag_id']);
    }



    /**
     * @return mixed
     */
    public function getTagName()
    {
        return $this->tagDescription->name ?? null;
    }



    /**
     * @param integer $lang_id
     * @return array|null
     */
    public static function getListWithNames(): ?array
    {

        $tags = (new Query())
            ->select(['t.tag_id', 'd.name'])
            ->from(['t' => self::tableName()])
            ->leftJoin(['d' => 'tbl_tag_description'], 't.tag_id = d.tag_id')
            ->where(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)])
            ->all();

        if(!empty($tags)){
            $result = [];
            foreach ($tags as $tag) {
                $result[] = [
                    'tag_id' => $tag['tag_id'],
                    'name' => $tag['name']
                ];
            }
            return $result;
        }
    }



}
