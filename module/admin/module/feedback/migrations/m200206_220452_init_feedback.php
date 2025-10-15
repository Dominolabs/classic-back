<?php

use yii\db\Migration;

class m200206_220452_init_feedback extends Migration
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

        $this->createTable('{{%feedback}}', [
            'feedback_id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'phone' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'text' => $this->text()->notNull(),
            'status' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->insert('{{%module}}', [
            'name' => 'feedback',
            'title' => 'Отзывы',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => 1,
            'sort_order' => 10,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%feedback}}');
    }
}
