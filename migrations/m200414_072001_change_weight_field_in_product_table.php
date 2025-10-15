<?php

use yii\db\Migration;

/**
 * Class m200414_072001_change_weight_field_in_product_table
 */
class m200414_072001_change_weight_field_in_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%product}}', 'weight', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%product}}', 'weight', $this->integer()->null());
    }
}
