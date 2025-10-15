<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * @property int $subscriber_id
 * @property string $name
 * @property string $email
 * @property int $created_at
 * @property int $updated_at
 */


class SubscriberSearch extends Subscriber
{


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
        $query = Subscriber::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'subscriber_id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ]
        ]);

        $this->load($params);


        $query->andFilterWhere([
            'subscriber_id' => $this->subscriber_id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);


        return $dataProvider;
    }
}
