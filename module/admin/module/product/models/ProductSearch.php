<?php

namespace app\module\admin\module\product\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class ProductSearch extends Product
{
    /** @var string $productName */
    public $productName;
    public $productWeight;
    public $categoryName;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['product_id', 'restaurant_id', 'weight_dish', 'caloricity', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['image', 'status', 'productName', 'productWeight', 'properties', 'categoryName'], 'safe'],
            [['price', 'price2', 'packaging_price', 'packaging_price2'], 'number'],
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
     * @param null $query
     * @return ActiveDataProvider
     */
    public function search($params,  $query = null)
    {
        if (is_null($query)) $query = Product::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

//        dd($params);
        /**
         * Setup sorting attributes.
         */
        $dataProvider->setSort([
            'attributes' => [
                'product_id',
                'productName' => [
                    'asc' => ['tbl_product_description.name' => SORT_ASC],
                    'desc' => ['tbl_product_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'categoryName' => [
                    'asc' => ['tbl_product_to_category.category_id' => SORT_ASC],
                    'desc' => ['tbl_product_to_category.category_id' => SORT_DESC],
                    'label' => 'Категория'
                ],
                'restaurant_id',
                'weight_dish',
                'productWeight' => [
                    'asc' => ['tbl_product_description.weight' => SORT_ASC],
                    'desc' => ['tbl_product_description.weight' => SORT_DESC],
                    'label' => 'Размер порции'
                ],
                'caloricity',
                'price',
                'price2',
                'packaging_price',
                'packaging_price2',
                'properties',
                'status',
                'sort_order',
                'created_at',
                'updated_at',
            ],
            'defaultOrder' => [
                'productName' => SORT_ASC,
            ]
        ]);


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'product_id' => $this->product_id,
            'restaurant_id' => $this->restaurant_id,
            'weight_dish' => $this->weight_dish,
            'caloricity' => $this->caloricity,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);


        $query->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'price', $this->price])
            ->andFilterWhere(['like', 'price2', $this->price2])
            ->andFilterWhere(['like', 'packaging_price', $this->packaging_price])
            ->andFilterWhere(['like', 'packaging_price2', $this->packaging_price2])
            ->andFilterWhere(['like', 'properties', $this->properties]);


        $query->joinWith(['productCategory' => function ($q) use ($params) {
            /** @var ActiveQuery $q */
            if (!empty($params['ProductSearch']['categoryName']))
                $q->where([
                    '=', 'tbl_product_to_category.category_id', $params['ProductSearch']['categoryName']
                ]);
        }]);


        $query->joinWith(['productDescription' => function ($q) use ($params) {
            /** @var ActiveQuery $q */
            if (!empty($params['ProductSearch']['productName'])){
                $q->where('tbl_product_description.name LIKE "%' . $this->productName . '%"');
            }
        }]);

        $query->andFilterWhere(['like', 'tbl_product_description.weight', $this->productWeight]);

        return $dataProvider;
    }
}
