<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%vacancy_requests}}`.
 */
class m200423_121426_add_lang_id_column_to_vacancy_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%vacancy_requests}}', 'lang_id', $this->integer(11)->notNull()->after('photo'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%vacancy_requests}}', 'lang_id');
    }
}
