<?php

namespace app\module\admin\module\gallery\models;

/**
 * @property int $album_id
 * @property int $language_id
 * @property string $name
 */
class AlbumDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_album_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['album_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['album_id', 'language_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['album_id', 'language_id'], 'unique', 'targetAttribute' => ['album_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'album_id' => 'ID альбома',
            'language_id' => 'ID языка',
            'name' => 'Название',
        ];
    }

    /**
     * Removes album descriptions by album id.
     *
     * @param string $albumId album id
     */
    public static function removeByAlbumId($albumId)
    {
        self::deleteAll(['album_id' => $albumId]);
    }
}
