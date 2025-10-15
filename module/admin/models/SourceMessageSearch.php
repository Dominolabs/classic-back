<?php
/**
 * SourceMessageSearch class file.
 */

namespace app\module\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SourceMessageSearch represents the model behind the search form of `app\module\admin\models\SourceMessage`.
 */
class SourceMessageSearch extends SourceMessage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_message_id'], 'integer'],
            [['category', 'message'], 'safe'],
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
        $query = SourceMessage::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'source_message_id' => $this->source_message_id,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
