<?php

namespace app\module\admin\module\feedback\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class FeedbackSearch extends Feedback
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['feedback_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'phone', 'email', 'feedback_id', 'text'], 'safe'],
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
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Feedback::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'feedback_id',
                'created_at',
                'updated_at',
                'name',
                'text',
                'phone',
                'email'
            ],
            'defaultOrder' => [
                'created_at' => SORT_DESC,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'feedback_id' => $this->feedback_id,
            'text' => $this->text,
            'email' => $this->email,
            'phone' => $this->phone,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
