<?php

use app\module\admin\module\order\models\Order;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%orders}}`.
 */
class m200716_062247_add_delivery_type_and_restaurant_id_columns_to_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'delivery_type', $this->tinyInteger(1)->defaultValue(Order::DELIVERY_TYPE_ADDRESS)->after('payment_type'));
        $this->addColumn('{{%order}}', 'restaurant_id', $this->integer(11)->null()->after('pizzeria_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'delivery_type');
        $this->dropColumn('{{%order}}', 'restaurant_id');
    }
}
