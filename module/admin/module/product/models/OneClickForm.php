<?php
/**
 * OneClickForm model class file.
 */

namespace app\module\admin\module\product\models;

use Yii;
use yii\base\Model;

/**
 * OneClickForm is the model behind the one click form.
 */
class OneClickForm extends Model
{
    public $product_id;
    public $quantity;
    public $username;
    public $email;
    public $phone;
    public $address;
    public $comment;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'phone'], 'required'],
            [['product_id', 'quantity'], 'integer'],
            [['username', 'email'], 'string', 'max' => 255],
            ['phone', 'string', 'max' => 32],
            [['address', 'comment'], 'string', 'max' => 10000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone' => Yii::t('product', 'Телефон'),
        ];
    }
}
