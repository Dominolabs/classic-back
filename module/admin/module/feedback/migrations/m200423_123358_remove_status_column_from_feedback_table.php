<?php

use yii\db\Migration;

/**
 * Class m200423_123358_remove_status_column_from_feedback_table
 */
class m200423_123358_remove_status_column_from_feedback_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%feedback}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%feedback}}', 'status', 'TINYINT(1) NOT NULL DEFAULT 0');
    }
}
