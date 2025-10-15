<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vacancies}}`.
 */
class m200422_093757_create_vacancies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vacancies}}', [
            'vacancy_id' => $this->primaryKey(),
            'status'     => 'TINYINT(1) NOT NULL',
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%vacancies}}');
    }
}
