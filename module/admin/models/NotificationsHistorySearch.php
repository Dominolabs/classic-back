<?php

namespace app\module\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class NotificationsHistorySearch extends NotificationsHistory
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['notifications_history_id', 'created_at', 'updated_at'], 'integer'],
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
                    'header',
                    'message',
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
            'notifications_history_id' => $this->notifications_history_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'header', $this->header])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
