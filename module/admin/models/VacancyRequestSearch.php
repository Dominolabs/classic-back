<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\components\ImageBehavior;


class VacancyRequestSearch extends VacancyRequest
{

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['vacancy_request_id', 'vacancy_id', 'age', 'lang_id'], 'integer'],
            [['full_name', 'phone', 'email', 'social_links', 'reason', 'photo', 'vacancyName'], 'safe'],
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
        $query = VacancyRequest::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $dataProvider->setSort([
            'attributes' => [
                'vacancy_request_id',
                'vacancyName' => [
                    'asc' => ['tbl_vacancies_description.name' => SORT_ASC],
                    'desc' => ['tbl_vacancies_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'created_at',
                'updated_at',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->joinWith(['vacancies']);
            return $dataProvider;
        }


        $query->andFilterWhere([
            'vacancy_request_id' => $this->vacancy_request_id,
            'vacancy_id'         => $this->vacancy_id,
            'age'                => $this->age,
            'full_name'          => $this->full_name,
            'phone'              => $this->phone,
            'social_links'       => $this->social_links,
            'email'              => $this->email,
            'reason'             => $this->reason,
            'photo'              => $this->photo,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'age', $this->age])
              ->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'phone', $this->phone])
              ->andFilterWhere(['like', 'full_name', $this->full_name])
              ->andFilterWhere(['like', 'reason', $this->reason])
              ->andFilterWhere(['like', 'social_links', $this->social_links]);


        $query->joinWith(['vacancy' => function ($q) {
            $q->joinWith(['vacancyDescription' => function ($q) {
                $q->where('tbl_vacancies_description.name LIKE "%' . $this->vacancyName . '%"');
            }]);
        }]);


        return $dataProvider;
    }
}
