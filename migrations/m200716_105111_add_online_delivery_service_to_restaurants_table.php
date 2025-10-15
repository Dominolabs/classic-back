<?php

use yii\db\Migration;

/**
 * Class m200716_105111_add_online_delivery_service_to_restaurants_table
 */
class m200716_105111_add_online_delivery_service_to_restaurants_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%restaurant}}', 'online_delivery_orders_processing',
            $this->boolean()->after('online_delivery')->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%restaurant}}', 'online_delivery_orders_processing');
    }

}
