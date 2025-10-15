<?php

use yii\db\Migration;

/**
 * Class m200511_123010_add_product_type_to_order_product_table
 */
class m200511_123010_add_product_type_to_order_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_product}}', 'product_type', $this->integer()->null()->after('product_id'));
    }
}
