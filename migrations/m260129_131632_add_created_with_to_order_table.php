<?php

use yii\db\Migration;

/**
 * Class m200429_131632_add_rating_to_order_table
 */
class m260129_131632_add_created_with_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'created_with', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'created_with');
    }
}
