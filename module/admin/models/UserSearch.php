<?php

namespace app\module\admin\models;

use app\module\admin\module\order\models\Order;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'role', 'status'], 'integer'],
            [['username', 'name', 'auth_key', 'password_hash', 'temp_password_hash', 'temp_password_created_at',
              'password_reset_token', 'password_reset_code', 'device_id', 'email', 'phone', 'address', 'promo_code', 'ref_promo_code',
              'created_at', 'updated_at', 'name', 'birth_date', 'avatar', 'ordersCount'], 'safe'
            ],
            [['notifications_news', 'notifications_delivery'], 'number'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        /* @var User $identity */
        $identity = Yii::$app->user->identity;
        $query = User::find();
        if ((int)$identity->role === User::ROLE_ADMIN) {
            $query->where(['role' => User::ROLE_ADMIN]);
        }
        if ((int)$identity->role === User::ROLE_ADMIN_HOTEL) {
            $query->where(['role' => User::ROLE_ADMIN_HOTEL]);
        }

        $query->select([User::tableName() . '.*', 'COUNT(' . Order::tableName() . '.order_id) AS ordersCount'])
            ->join('LEFT JOIN', Order::tableName(), User::tableName() . '.user_id=' . Order::tableName() . '.user_id')
            ->groupBy('tbl_user.user_id');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /**
         * Setup sorting attributes.
         */
        $dataProvider->setSort([
            'attributes' => [
                'username',
                'name',
                'email',
                'phone',
                'ordersCount',
                'bonuses',
                'status',
                'role',
                'created_at',
                'updated_at',
            ],
            'defaultOrder' => [
                'created_at' => SORT_ASC,
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
            'user_id' => $this->user_id,
            'device_id' => $this->device_id,
            'role' => $this->role,
            'status' => $this->status,
            'promo_code' => $this->promo_code,
            'ref_promo_code' => $this->ref_promo_code,
            'notifications_news' => $this->notifications_news,
            'notifications_delivery' => $this->notifications_delivery,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'username', $this->name])
            ->andFilterWhere(['like', 'birth_date', $this->birth_date])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'password_reset_code', $this->password_reset_code])
            ->andFilterWhere(['like', User::tableName() . '.email', $this->email])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', User::tableName() . '.phone', $this->phone]);
        return $dataProvider;
    }
}
