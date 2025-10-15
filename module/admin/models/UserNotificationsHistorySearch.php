<?php

namespace app\module\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserNotificationsHistorySearch extends UserNotificationsHistory
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['user_notifications_history_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['header', 'message'], 'safe'],
        ];
    }

    /**
     * @return array
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
        $query = NotificationsHistory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'notifications_history_id',
                    'user_id',
                    'header',
                    'message',
                    'status',
                    'created_at',
                    'updated_at',
                ],
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

        $query->andFilterWhere([
            'user_notifications_history_id' => $this->user_notifications_history_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'header', $this->header])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
