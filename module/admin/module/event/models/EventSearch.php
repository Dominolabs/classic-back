<?php

namespace app\module\admin\module\event\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class EventSearch extends Event
{
    public $eventName;
    public $eventDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'event_category_id', 'status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['eventName', 'eventDate', 'image'], 'safe'],
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
        $query = Event::find();

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
                'event_id',
                'image',
                'eventName' => [
                    'asc' => ['tbl_event_description.name' => SORT_ASC],
                    'desc' => ['tbl_event_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'eventDate' => [
                    'asc' => [
                        'UNIX_TIMESTAMP(STR_TO_DATE(tbl_event_description.date, \'%d.%m.%Y\'))' => SORT_ASC,
                    ],
                    'desc' => [
                        'UNIX_TIMESTAMP(STR_TO_DATE(tbl_event_description.date, \'%d.%m.%Y\'))' => SORT_DESC,
                    ],
                    'label' => 'Дата'
                ],
                'event_category_id',
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
            $query->joinWith(['event_description']);
            $query->joinWith(['event_category_description']);
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'event_id' => $this->event_id,
            'event_category_id' => $this->event_category_id,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'image', $this->image]);

        $query->joinWith(['eventDescription' => function ($q) {
            $q->where('tbl_event_description.name LIKE "%' . $this->eventName . '%"');
        }]);

        $query->joinWith(['eventDescription' => function ($q) {
            $q->where('tbl_event_description.date LIKE "%' . $this->eventDate . '%"');
        }]);

        return $dataProvider;
    }
}
