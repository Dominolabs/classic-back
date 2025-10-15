<?php

use yii\db\Migration;

class m180312_142051_init extends Migration
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

        $this->createTable('{{%album_category}}', [
            'album_category_id' => $this->primaryKey(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%album_category_description}}', [
            'album_category_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%album_category_description}}', '{{%album_category_description}}', ['album_category_id', 'language_id']);

        $this->createIndex('name', '{{%album_category_description}}', 'name');

        $this->createTable('{{%album}}', [
            'album_id' => $this->primaryKey(),
            'image' => $this->string(255)->notNull(),
            'album_category_id' => $this->integer(11)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createIndex('album_category_id', '{{%album}}', 'album_category_id');

        $this->createTable('{{%album_description}}', [
            'album_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%album_description}}', '{{%album_description}}', ['album_id', 'language_id']);

        $this->createIndex('name', '{{%album_description}}', 'name');

        $this->createTable('{{%album_image}}', [
            'album_image_id' => $this->primaryKey(),
            'album_id' => $this->integer(11)->notNull(),
            'image' => $this->string(255)->notNull(),
            'sort_order' => $this->integer(3)->notNull(),
        ], $tableOptions);

        $this->createIndex('album_id', '{{%album_image}}', 'album_id');

        /** Creating demo data **/
        $this->execute(file_get_contents(__DIR__ . '/init.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('album_id', '{{%album_image}}');
        $this->dropTable('{{%album_image}}');
        $this->dropIndex('name', '{{%album_description}}');
        $this->dropPrimaryKey('{{%album_description}}', '{{%album_description}}');
        $this->dropTable('{{%album_description}}');
        $this->dropIndex('album_category_id', '{{%album}}');
        $this->dropTable('{{%album}}');
        $this->dropIndex('name', '{{%album_category_description}}');
        $this->dropPrimaryKey('{{%album_category_description}}', '{{%album_category_description}}');
        $this->dropTable('{{%album_category_description}}');
        $this->dropTable('{{%album_category}}');
    }
}
