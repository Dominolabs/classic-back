<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%page_category}}`.
 */
class m200413_122236_create_page_category_table extends Migration
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

        $this->createTable('{{%page_category}}', [
            'page_category_id' => $this->primaryKey(),
            'parent_id' => $this->integer(11)->notNull(),
            'top' => 'TINYINT(1) NOT NULL',
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createIndex('parent_id', '{{%page_category}}', 'parent_id');

        $this->createTable('{{%page_category_description}}', [
            'page_category_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'meta_title' => $this->string(255)->notNull(),
            'meta_description' => $this->string(255)->notNull(),
            'meta_keyword' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%page_category_description}}', '{{%page_category_description}}', ['page_category_id', 'language_id']);

        $this->createIndex('name', '{{%page_category_description}}', 'name');

        $this->createTable('{{%page_category_path}}', [
            'page_category_id' => $this->integer(11)->notNull(),
            'path_id' => $this->integer(11)->notNull(),
            'level' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%page_category_path}}', '{{%page_category_path}}', ['page_category_id', 'path_id']);

        $this->createTable('{{%page_to_category}}', [
            'page_id' => $this->integer(11)->notNull(),
            'page_category_id' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%page_to_category}}', '{{%page_to_category}}', ['page_id', 'page_category_id']);

        $this->createIndex('page_category_id', '{{%page_to_category}}', 'page_category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%page_to_category}}');
        $this->dropTable('{{%page_category_path}}');
        $this->dropTable('{{%page_category_description}}');
        $this->dropTable('{{%page_category}}');
    }
}
