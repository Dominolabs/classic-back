<?php

use yii\db\Migration;

class m200324_092952_add_ingredients_to_product_module extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%ingredient}}', [
            'ingredient_id' => $this->primaryKey(),
            'price' => $this->integer()->notNull(),
            'image' => $this->string()->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%ingredient_description}}', [
            'ingredient_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'portion_size' => $this->string()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('ingredient_description', '{{%ingredient_description}}', ['ingredient_id', 'language_id']);

        $this->createTable('{{%product_ingredient}}', [
            'product_ingredient_id' => $this->primaryKey(),
            'product_id' => $this->integer(11)->notNull(),
            'ingredient_id' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createIndex('product_id', '{{%product_ingredient}}', ['product_id', 'ingredient_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_ingredient}}');
        $this->dropTable('{{%ingredient_description}}');
        $this->dropTable('{{%ingredient}}');
    }
}
