<?php

use yii\db\Migration;

class m200422_121402_create_restaurant_table extends Migration
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

        $this->createTable('{{%restaurant}}', [
            'restaurant_id' => $this->primaryKey(),
            'image' => $this->string()->null(),
            'top_banner_id' => $this->integer()->null(),
            'gallery_id' => $this->integer()->null(),
            'menu_banner_id' => $this->integer()->null(),
            'facebook' => $this->string()->null(),
            'instagram' => $this->string()->null(),
            'youtube' => $this->string()->null(),
            'vk' => $this->string()->null(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%restaurant_description}}', [
            'restaurant_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'title' => $this->string()->null(),
            'description1' => $this->text()->null(),
            'description2' => $this->text()->null(),
            'meta_title' => $this->string()->null(),
            'meta_description' => $this->string()->null(),
            'meta_keyword' => $this->string()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%restaurant_description}}', '{{%restaurant_description}}', ['restaurant_id', 'language_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%restaurant_description}}');
        $this->dropTable('{{%restaurant}}');
    }
}
