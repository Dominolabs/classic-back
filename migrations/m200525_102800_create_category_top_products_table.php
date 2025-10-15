<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category_top_products}}`.
 */
class m200525_102800_create_category_top_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category_top_products}}', [
            'category_top_product_id' => $this->primaryKey(),
            'category_id' => $this->bigInteger(),
            'product_id' => $this->bigInteger(),
        ]);

        $this->createIndex('category_id', '{{%category_top_products}}', 'category_id');
        $this->createIndex('product_id', '{{%category_top_products}}', 'product_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%category_top_products}}');
    }
}
