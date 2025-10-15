<?php

namespace app\module\admin\module\event\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;





class TagSearch extends Tag
{

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['tag_id'], 'integer'],
            [['tagName'], 'safe'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }



    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Tag::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'tag_id',
                'tagName' => [
                    'asc' => ['tbl_tag_description.name' => SORT_ASC],
                    'desc' => ['tbl_tag_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'created_at',
                'updated_at',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->joinWith(['tag_description']);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tag_id' => $this->tag_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);


        $query->joinWith(['tagDescription' => function ($q) {
            $q->where('tbl_tag_description.name LIKE "%' . $this->tagName . '%"');
        }]);

        return $dataProvider;
    }

}
