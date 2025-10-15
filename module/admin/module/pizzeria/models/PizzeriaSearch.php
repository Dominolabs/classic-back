<?php

namespace app\module\admin\module\pizzeria\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PizzeriaSearch represents the model behind the search form of `app\module\admin\module\pizzeria\models\Pizzeria`.
 */
class PizzeriaSearch extends Pizzeria
{
    public $pizzeriaName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pizzeria_id', 'status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['pizzeriaName', 'image', 'phones', 'email', 'instagram', 'gmap'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Pizzeria::find();

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
                'pizzeria_id',
                'image',
                'pizzeriaName' => [
                    'asc' => ['tbl_pizzeria_description.name' => SORT_ASC],
                    'desc' => ['tbl_pizzeria_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'phones',
                'email',
                'instagram',
                'gmap',
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
            $query->joinWith(['pizzeria_description']);
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pizzeria_id' => $this->pizzeria_id,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'phones', $this->phones])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'instagram', $this->instagram])
            ->andFilterWhere(['like', 'gmap', $this->gmap]);

        $query->joinWith(['pizzeriaDescription' => function ($q) {
            $q->where('tbl_pizzeria_description.name LIKE "%' . $this->pizzeriaName . '%"');
        }]);

        return $dataProvider;
    }
}
