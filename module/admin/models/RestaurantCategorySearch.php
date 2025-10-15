<?php

namespace app\module\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class RestaurantCategorySearch extends RestaurantCategory
{
    public $restaurantCategoryName;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['restaurant_category_id', 'status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['restaurantCategoryName'], 'safe']
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = RestaurantCategory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'restaurant_category_id',
                'restaurantCategoryName' => [
                    'asc' => ['tbl_restaurant_category_description.name' => SORT_ASC],
                    'desc' => ['tbl_restaurant_category_description.name' => SORT_DESC],
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
            $query->joinWith(['restaurant_category_description']);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'restaurant_category_id' => $this->restaurant_category_id,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->joinWith(['restaurantCategoryDescription' => function ($q) {
            $q->where('tbl_restaurant_category_description.name LIKE "%' . $this->restaurantCategoryName . '%"');
        }]);

        return $dataProvider;
    }
}
