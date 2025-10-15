<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;


class VacancySearch extends Vacancy
{

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['vacancy_id', 'status'], 'integer'],
            [['vacancyName'], 'safe'],
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
        $query = Vacancy::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'vacancy_id',
                'vacancyName' => [
                    'asc' => ['tbl_vacancies_description.name' => SORT_ASC],
                    'desc' => ['tbl_vacancies_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'status',
                'created_at',
                'updated_at',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->joinWith(['vacancies_description']);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'vacancy_id' => $this->vacancy_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);


        $query->joinWith(['vacancyDescription' => function ($q) {
            $q->where('tbl_vacancies_description.name LIKE "%' . $this->vacancyName . '%"');
        }]);

        return $dataProvider;
    }
}
