<?php

use yii\db\Migration;

/**
 * Class m200410_064803_add_pizzeria_id_to_order_history_table
 */
class m200410_064803_add_pizzeria_id_to_order_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_history}}', 'pizzeria_id', $this->integer(11)->null()->after('order_id'));
        $this->createIndex('pizzeria_id', '{{%order_history}}', 'pizzeria_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_history}}', 'pizzeria_id');
    }
}
