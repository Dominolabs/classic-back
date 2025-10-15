<?php

namespace app\module\admin\module\order\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class OrderProductSearch extends OrderProduct
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['order_product_id', 'order_id', 'product_id', 'category_id', 'quantity', 'product_type'], 'integer'],
            [['name', 'type', 'ingredients', 'properties', 'comment'], 'safe'],
            [['price', 'total'], 'number'],
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
        $query = OrderProduct::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'order_product_id' => SORT_ASC,
                ]
            ],
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_product_id' => $this->order_product_id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'category_id' => $this->category_id,
            'product_type' => $this->product_type,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'ingredients', $this->ingredients])
            ->andFilterWhere(['like', 'properties', $this->properties])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
