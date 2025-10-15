<?php

use yii\db\Migration;

/**
 * Class m200604_065201_add_operation_id_to_category_top_product
 */
class m200604_065201_add_operation_id_to_category_top_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%category_top_products}}', 'operation_id', $this->bigInteger()->null()->after('product_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%category_top_products}}', 'operation_id');
    }
}
