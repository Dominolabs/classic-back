<?php

use yii\db\Migration;

class m180312_142052_init extends Migration
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

        $this->createTable('{{%event_category}}', [
            'event_category_id' => $this->primaryKey(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%event_category_description}}', [
            'event_category_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%event_category_description}}', '{{%event_category_description}}', ['event_category_id', 'language_id']);

        $this->createIndex('name', '{{%event_category_description}}', 'name');

        $this->createTable('{{%event}}', [
            'event_id' => $this->primaryKey(),
            'image' => $this->string(255)->notNull(),
            'event_category_id' => $this->integer(11)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createIndex('event_category_id', '{{%event}}', 'event_category_id');

        $this->createTable('{{%event_description}}', [
            'event_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'date' => $this->string(255)->notNull(),
            'text' => $this->text()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%event_description}}', '{{%event_description}}', ['event_id', 'language_id']);

        $this->createIndex('name', '{{%event_description}}', 'name');

        /** Creating demo data **/
        $this->execute(file_get_contents(__DIR__ . '/init.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('name', '{{%event_description}}');
        $this->dropPrimaryKey('{{%event_description}}', '{{%event_description}}');
        $this->dropTable('{{%event_description}}');
        $this->dropIndex('event_category_id', '{{%event}}');
        $this->dropTable('{{%event}}');
        $this->dropIndex('name', '{{%event_category_description}}');
        $this->dropPrimaryKey('{{%event_category_description}}', '{{%event_category_description}}');
        $this->dropTable('{{%event_category_description}}');
        $this->dropTable('{{%event_category}}');
    }
}
