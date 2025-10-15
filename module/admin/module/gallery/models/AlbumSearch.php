<?php

namespace app\module\admin\module\gallery\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class AlbumSearch extends Album
{
    public $albumName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['album_id', 'status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['albumName', 'image'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Album::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /**
         * Setup your sorting attributes
         * Note: This is setup before the $this->load($params)
         * statement below
         */
        $dataProvider->setSort([
            'attributes' => [
                'album_id',
                'image',
                'albumName' => [
                    'asc' => ['tbl_album_description.name' => SORT_ASC],
                    'desc' => ['tbl_album_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'status',
                'sort_order',
                'created_at',
                'updated_at',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $query->joinWith(['album_description']);
            $query->joinWith(['album_category_description']);
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'album_id' => $this->album_id,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'image', $this->image]);

        $query->joinWith(['albumDescription' => function ($q) {
            $q->where('tbl_album_description.name LIKE "%' . $this->albumName . '%"');
        }]);

        return $dataProvider;
    }
}
