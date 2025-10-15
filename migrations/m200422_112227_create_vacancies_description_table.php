<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vacancies_description}}`.
 */
class m200422_112227_create_vacancies_description_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vacancies_description}}', [
            'vacancy_id'    => $this->integer(11)->unsigned()->notNull(),
            'language_id'   => $this->integer(11)->unsigned()->notNull(),
            'name'          => $this->string(255)->null(),
        ]);

        $this->addPrimaryKey('{{%vacancies_description}}', '{{%vacancies_description}}', ['vacancy_id', 'language_id']);
        $this->createIndex('name', '{{%vacancies_description}}', 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('name', '{{%vacancies_description}}');
        $this->dropTable('{{%vacancies_description}}');
    }
}
