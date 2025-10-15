<?php

namespace app\module\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PageSearch extends Page
{
    public $pageName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'status', 'sort_order', 'created_at', 'updated_at', 'top_banner_id', 'gallery_id'], 'integer'],
            [['image', 'pageName', 'facebook', 'instagram', 'youtube', 'vk', 'footer_columns'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Page::find();

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
                'page_id',
                'pageName' => [
                    'asc' => ['tbl_page_description.title' => SORT_ASC],
                    'desc' => ['tbl_page_description.title' => SORT_DESC],
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
            $query->joinWith(['page_description']);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'page_id' => $this->page_id,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->joinWith(['pageDescription' => function ($q) {
            $q->where('tbl_page_description.title LIKE "%' . $this->pageName . '%"');
        }]);

        return $dataProvider;
    }
}
