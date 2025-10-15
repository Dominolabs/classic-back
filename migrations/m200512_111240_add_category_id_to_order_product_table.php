<?php

use yii\db\Migration;

/**
 * Class m200512_111240_add_category_id_to_order_product_table
 */
class m200512_111240_add_category_id_to_order_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_product}}', 'category_id', $this->integer()->null()->after('product_id'));
    }
}
