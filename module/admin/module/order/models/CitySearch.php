<?php

namespace app\module\admin\module\order\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class CitySearch extends City
{
    public $cityName;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'minimum_order', 'free_minimum_order','status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['delivery_price', 'cityName'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = City::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'cityName' => [
                    'asc' => [CityDescription::tableName() . '.name' => SORT_ASC],
                    'desc' => [CityDescription::tableName() . '.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'delivery_price',
                'minimum_order',
                'free_minimum_order',
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
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'minimum_order', $this->minimum_order])
            ->andFilterWhere(['like', 'free_minimum_order', $this->minimum_order])
            ->andFilterWhere(['like', 'delivery_price', $this->delivery_price]);

//        $query->joinWith([
//            'cityDescription' => function ($q) {
//                $q->where(CityDescription::tableName() . '.name LIKE "%' . $this->cityName . '%"');
//            }
//        ]);

        return $dataProvider;
    }
}
