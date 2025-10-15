<?php

namespace app\module\admin\module\order\models;

use app\module\admin\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderHistorySearch represents the model behind the search form of `app\module\admin\module\order\models\OrderHistory`.
 */
class OrderHistorySearch extends OrderHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_history_id', 'order_id', 'pizzeria_id', 'created_at'], 'integer'],
            [['status', 'comment'], 'safe'],
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
        $query = OrderHistory::find();

        // add conditions that should always apply here

        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (($user instanceof User) && $user->role === User::ROLE_ADMIN) {
            $query->where(['pizzeria_id' => $user->pizzeria_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_history_id' => $this->order_history_id,
            'order_id' => $this->order_id,
            'pizzeria_id' => $this->pizzeria_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
