<?php

use yii\db\Migration;

class m200410_050132_add_pizzeria_id_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'pizzeria_id', $this->integer(11)->null()->after('time'));
        $this->createIndex('pizzeria_id', '{{%order}}', 'pizzeria_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'pizzeria_id');
    }
}
