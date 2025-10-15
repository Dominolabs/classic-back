<?php
/**
 * BannerImageSearch model class file.
 */

namespace app\module\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\module\admin\models\BannerImage;

/**
 * BannerImageSearch represents the model behind the search form of `app\module\admin\models\BannerImage`.
 */
class BannerImageSearch extends BannerImage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['banner_image_id', 'banner_id', 'language_id', 'sort_order'], 'integer'],
            [['title', 'link', 'image'], 'safe'],
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
        $query = BannerImage::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'banner_image_id' => $this->banner_image_id,
            'banner_id' => $this->banner_id,
            'language_id' => $this->language_id,
            'sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'image', $this->image]);

        return $dataProvider;
    }
}
