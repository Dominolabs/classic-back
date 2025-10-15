<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_history}}`.
 */
class m200716_071545_add__restaurant_id_columns_to_order_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_history}}', 'restaurant_id', $this->integer(11)->null()->after('pizzeria_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_history}}', 'restaurant_id');
    }
}
