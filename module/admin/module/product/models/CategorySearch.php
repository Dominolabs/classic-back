<?php

namespace app\module\admin\module\product\models;

use Yii;
use app\module\admin\models\Language;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CategorySearch represents the model behind the search form of `app\module\admin\module\product\models\Category`.
 */
class CategorySearch extends Category
{
    /**
     * @var string $categoryName
     */
    public $categoryName;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['category_id', 'restaurant_id', 'parent_id', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['image', 'top', 'contains_ingredients', 'status', 'categoryName'], 'safe'],

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
        $query = Category::find();

        $query->select('`tbl_category`.*, (SELECT GROUP_CONCAT(cd.name ORDER BY level SEPARATOR " > ") 
            FROM  `tbl_category_path` AS cp LEFT JOIN `tbl_category_description` AS cd ON cp.path_id = cd.category_id 
            WHERE cp.category_id = `tbl_category`.category_id AND cd.language_id = '
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
                'category_id',
                'categoryName' => [
                    'asc' => ['categoryName' => SORT_ASC],
                    'desc' => ['categoryName' => SORT_DESC],
                    'label' => 'Название'
                ],
                'status',
                'restaurant_id',
                'sort_order',
                'created_at',
                'updated_at',
            ],
            'defaultOrder' => [
                'categoryName' => SORT_ASC,
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
            'category_id' => $this->category_id,
            'parent_id' => $this->parent_id,
            'restaurant_id' => $this->restaurant_id,
            'top' => $this->top,
            'contains_ingredients' => $this->contains_ingredients,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'image', $this->image]);

        $query->andFilterWhere(['like', '(SELECT GROUP_CONCAT(cd.name ORDER BY level SEPARATOR " > ")
            FROM  `tbl_category_path` AS cp LEFT JOIN `tbl_category_description` AS cd ON cp.path_id = cd.category_id
            WHERE cp.category_id = `tbl_category`.category_id AND cd.language_id = '
            . Language::getLanguageIdByCode(Yii::$app->language) . ')', $this->categoryName
        ]);

        return $dataProvider;
    }
}
