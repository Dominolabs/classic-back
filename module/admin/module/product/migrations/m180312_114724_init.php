<?php

use yii\db\Migration;

class m180312_114724_init extends Migration
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

        /** Creating tables **/

        $this->createTable('{{%category}}', [
            'category_id' => $this->primaryKey(),
            'image' => $this->string(255)->notNull(),
            'packing_price' => $this->decimal(15,4)->notNull(),
            'parent_id' => $this->integer(11)->notNull(),
            'top' => 'TINYINT(1) NOT NULL',
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createIndex('parent_id', '{{%category}}', 'parent_id');

        $this->createTable('{{%category_description}}', [
            'category_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'meta_title' => $this->string(255)->notNull(),
            'meta_description' => $this->string(255)->notNull(),
            'meta_keyword' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%category_description}}', '{{%category_description}}', ['category_id', 'language_id']);

        $this->createIndex('name', '{{%category_description}}', 'name');

        $this->createTable('{{%category_path}}', [
            'category_id' => $this->integer(11)->notNull(),
            'path_id' => $this->integer(11)->notNull(),
            'level' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%category_path}}', '{{%category_path}}', ['category_id', 'path_id']);

        $this->createTable('{{%product}}', [
            'product_id' => $this->primaryKey(),
            'weight' => $this->integer(11)->null(),
            'caloricity' => $this->integer(11)->null(),
            'image' => $this->string(255)->notNull(),
            'price' => $this->decimal(15,4)->notNull(),
            'is_promo' => 'TINYINT(1) NOT NULL',
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%product_description}}', [
            'product_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'promo' => $this->text()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%product_description}}', '{{%product_description}}', ['product_id', 'language_id']);

        $this->createIndex('name', '{{%product_description}}', 'name');

        $this->createTable('{{%product_to_category}}', [
            'product_id' => $this->integer(11)->notNull(),
            'category_id' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%product_to_category}}', '{{%product_to_category}}', ['product_id', 'category_id']);

        $this->createIndex('category_id', '{{%product_to_category}}', 'category_id');

        /** Creating demo data **/
        $this->execute(file_get_contents(__DIR__ . '/init.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('category_id', '{{%product_to_category}}');
        $this->dropPrimaryKey('{{%product_to_category}}', '{{%product_to_category}}');
        $this->dropTable('{{%product_to_category}}');
        $this->dropIndex('name', '{{%product_description}}');
        $this->dropPrimaryKey('{{%product_description}}', '{{%product_description}}');
        $this->dropTable('{{%product_description}}');
        $this->dropTable('{{%product}}');
        $this->dropPrimaryKey('{{%category_path}}', '{{%category_path}}');
        $this->dropTable('{{%category_path}}');
        $this->dropIndex('name', '{{%category_description}}');
        $this->dropPrimaryKey('{{%category_description}}', '{{%category_description}}');
        $this->dropTable('{{%category_description}}');
        $this->dropIndex('parent_id', '{{%category}}');
        $this->dropTable('{{%category}}');
    }
}
