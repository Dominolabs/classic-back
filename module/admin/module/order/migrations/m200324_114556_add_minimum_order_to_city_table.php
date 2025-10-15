<?php

use yii\db\Migration;

class m200324_114556_add_minimum_order_to_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%city}}', 'minimum_order', $this->integer()->notNull()->defaultValue(0)->after('delivery_price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%city}}', 'minimum_order');
    }
}
