<?php

namespace app\module\admin\module\order\models;

use app\module\admin\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OrderSearch extends Order
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'order_id',
                    'user_id',
                    'city_id',
                    'do_not_call',
                    'have_a_child',
                    'have_a_dog',
                    'time',
                    'payment_type',
                    'payment_status',
                    'language_id',
                    'currency_id',
                    'pizzeria_id',
                    'rating',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'integer'
            ],
            [['name', 'email', 'phone', 'street', 'entrance', 'house_number', 'apartment_number', 'promotions_applied', 'comment', 'currency_code', 'is_deleted'], 'safe'],
            [['currency_value', 'sum', 'packing', 'delivery', 'total'], 'number'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @param bool $is_deleted
     * @return ActiveDataProvider
     */
    public function search($params, $is_deleted = false): ActiveDataProvider
    {
        $query = Order::find()->where(['is_deleted' => $is_deleted]);

        // add conditions that should always apply here

        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (($user instanceof User) && $user->role === User::ROLE_ADMIN) {
            $query->where(['pizzeria_id' => $user->pizzeria_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'order_id' => SORT_DESC,
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
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'city_id' => $this->city_id,
            'do_not_call' => $this->do_not_call,
            'call_me_back' => $this->call_me_back,
            'have_a_child' => $this->have_a_child,
            'have_a_dog' => $this->have_a_dog,
            'time' => $this->time,
            'payment_type' => $this->payment_type,
            'payment_status' => $this->payment_status,
            'language_id' => $this->language_id,
            'currency_id' => $this->currency_id,
            'currency_code' => $this->currency_code,
            'currency_value' => $this->currency_value,
            'pizzeria_id' => $this->pizzeria_id,
            'rating' => $this->rating,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'entrance', $this->entrance])
            ->andFilterWhere(['like', 'house_number', $this->house_number])
            ->andFilterWhere(['like', 'apartment_number', $this->apartment_number])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'sum', $this->sum])
            ->andFilterWhere(['like', 'packing', $this->packing])
            ->andFilterWhere(['like', 'delivery', $this->delivery])
            ->andFilterWhere(['like', 'total', $this->total]);


        return $dataProvider;
    }
}
