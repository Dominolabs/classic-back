<?php

use yii\db\Migration;

/**
 * Class m200430_133226_remove_unused_tables
 */
class m200430_133226_remove_unused_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%team_description}}');
        $this->dropTable('{{%team}}');
        $this->dropTable('{{%tariff_category_description}}');
        $this->dropTable('{{%tariff_category}}');
        $this->dropTable('{{%tariff_description}}');
        $this->dropTable('{{%tariff}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%team}}', [
            'team_id' => $this->primaryKey(),
            'image' => $this->string(255)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%team_description}}', [
            'team_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'position' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%team_description}}', '{{%team_description}}', ['team_id', 'language_id']);

        $this->createIndex('name', '{{%team_description}}', 'name');

        $this->createTable('{{%tariff}}', [
            'tariff_id' => $this->primaryKey(),
            'banner_id' => $this->integer(11)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%tariff_description}}', [
            'tariff_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'content' => $this->text()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%tariff_description}}', '{{%tariff_description}}', ['tariff_id', 'language_id']);

        $this->createIndex('name', '{{%tariff_description}}', 'name');

        $this->createTable('{{%tariff_category}}', [
            'tariff_category_id' => $this->primaryKey(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%tariff_category_description}}', [
            'tariff_category_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%tariff_category_description}}', '{{%tariff_category_description}}', ['tariff_category_id', 'language_id']);

        $this->createIndex('name', '{{%tariff_category_description}}', 'name');

        $this->addColumn('{{%tariff}}', 'tariff_category_id', $this->integer(11)->notNull()->after('banner_id'));

        $this->createIndex('tariff_category_id', '{{%tariff}}', 'tariff_category_id');
    }
}
