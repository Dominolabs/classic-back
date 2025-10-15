<?php

namespace app\module\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RestaurantSearch extends Restaurant
{
    public $restaurantName;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['restaurant_id', 'restaurant_category_id', 'top_banner_id', 'gallery_id', 'menu_banner_id', 'online_delivery', 'online_delivery_orders_processing', 'self_picking', 'status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['image', 'image_transparent', 'background_image', 'restaurantName'], 'safe']
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
    public function search($params)
    {
        $query = Restaurant::find();

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
                'restaurant_id',
                'restaurant_category_id',
                'top_banner_id',
                'gallery_id',
                'menu_banner_id',
                'restaurantName' => [
                    'asc' => ['tbl_restaurant_description.title' => SORT_ASC],
                    'desc' => ['tbl_restaurant_description.title' => SORT_DESC],
                    'label' => 'Название'
                ],
                'online_delivery',
                'online_delivery_orders_processing',
                'self_picking',
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
            $query->joinWith(['restaurant_description']);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'restaurant_id' => $this->restaurant_id,
            'restaurant_category_id' => $this->restaurant_category_id,
            'top_banner_id' => $this->top_banner_id,
            'gallery_id' => $this->gallery_id,
            'menu_banner_id' => $this->menu_banner_id,
            'online_delivery' => $this->online_delivery,
            'online_delivery_orders_processing' => $this->online_delivery_orders_processing,
            'self_picking' => $this->self_picking,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->joinWith(['restaurantDescription' => function ($q) {
            $q->where('tbl_restaurant_description.title LIKE "%' . $this->restaurantName . '%"');
        }]);

        return $dataProvider;
    }
}
