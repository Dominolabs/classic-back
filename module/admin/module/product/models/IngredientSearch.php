<?php

namespace app\module\admin\module\product\models;

use app\module\admin\models\Language;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class IngredientSearch extends Ingredient
{
    /**
     * @var string
     */
    public $name;

    /**
     * @varstring $categoryName
     */
    public $categoryName;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['ingredient_id', 'price', 'category_id', 'show_in_constructor_main', 'show_in_constructor_additional', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['image', 'status', 'name', 'categoryName'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Ingredient::find();

        $query->select('`tbl_ingredient`.*, (SELECT GROUP_CONCAT(cd.name ORDER BY level SEPARATOR " > ") 
            FROM  `tbl_category_path` AS cp LEFT JOIN `tbl_category_description` AS cd ON cp.path_id = cd.category_id 
            WHERE cp.category_id = `tbl_ingredient`.category_id AND cd.language_id = '
            . Language::getLanguageIdByCode(Yii::$app->language) . ') AS categoryName'
        );

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /**
         * Setup sorting attributes.
         */
        $dataProvider->setSort([
            'attributes' => [
                'ingredient_id',
                'name' => [
                    'asc' => ['tbl_ingredient_description.name' => SORT_ASC],
                    'desc' => ['tbl_ingredient_description.name' => SORT_DESC],
                    'label' => 'Название'
                ],
                'categoryName' => [
                    'asc' => ['categoryName' => SORT_ASC],
                    'desc' => ['categoryName' => SORT_DESC],
                    'label' => 'Название'
                ],
                'price',
                'status',
                'sort_order',
                'created_at',
                'updated_at',
            ],
            'defaultOrder' => [
                'name' => SORT_ASC,
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
            'ingredient_id' => $this->ingredient_id,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'price', $this->price]);

        $query->joinWith(['ingredientDescription' => function ($q) {
            /** @var ActiveQuery $q */
            $q->where('tbl_ingredient_description.name LIKE "%' . $this->name . '%"');
        }]);

        $query->andFilterWhere(['like', '(SELECT GROUP_CONCAT(cd.name ORDER BY level SEPARATOR " > ")
            FROM  `tbl_category_path` AS cp LEFT JOIN `tbl_category_description` AS cd ON cp.path_id = cd.category_id
            WHERE cp.category_id = `tbl_ingredient`.category_id AND cd.language_id = '
            . Language::getLanguageIdByCode(Yii::$app->language) . ')', $this->categoryName
        ]);

        return $dataProvider;
    }
}
