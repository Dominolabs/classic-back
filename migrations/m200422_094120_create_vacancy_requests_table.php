<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vacancy_requests}}`.
 */
class m200422_094120_create_vacancy_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vacancy_requests}}', [
            'vacancy_request_id' => $this->primaryKey(),
            'vacancy_id'         => $this->integer(11)->unsigned()->notNull(),
            'full_name'          => $this->string(255)->notNull(),
            'age'                => $this->integer(11)->unsigned()->null(),
            'phone'              => $this->string(255)->notNull(),
            'social_links'       => $this->text()->null(),
            'email'              => $this->string(255)->notNull(),
            'reason'             => $this->text()->notNull(),
            'photo'              => $this->string(255)->null(),
            'created_at'         => $this->integer(11)->notNull(),
            'updated_at'         => $this->integer(11)->notNull(),
        ]);


        $this->createIndex('vacancy_id', '{{%vacancy_requests}}', 'vacancy_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('vacancy_id', '{{%vacancy_requests}}');
        $this->dropTable('{{%vacancy_requests}}');
    }
}
