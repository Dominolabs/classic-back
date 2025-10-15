<?php

namespace app\module\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class ClassicSearch extends Classic
{
    /**
     * @var string $productName
     */
    public $productName;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['product_id', 'created_at', 'updated_at'], 'integer'],
            [['image', 'status', 'productName', 'properties'], 'safe'],
            [['price', 'price2'], 'number'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param array
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Classic::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /**
         * Setup sorting attributes.
         */
        $dataProvider->setSort([
            'attributes' => [
                'product_id',
                'productName' => [
                    'asc' => ['tbl_classic_description.name' => SORT_ASC],
                    'desc' => ['tbl_classic_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'price',
                'price2',
                'properties',
                'status',
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'price', $this->price])
            ->andFilterWhere(['like', 'price2', $this->price2])
            ->andFilterWhere(['like', 'properties', $this->properties]);

        $query->joinWith(['classicDescription' => function ($q) {
            /** @var ActiveQuery $q */
            $q->where('tbl_classic_description.name LIKE "%' . $this->productName . '%"');
        }]);

        return $dataProvider;
    }
}
