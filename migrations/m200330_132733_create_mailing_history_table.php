<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mailing_history}}`.
 */
class m200330_132733_create_mailing_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mailing_history}}', [
            'mailing_history_id' => $this->primaryKey(),
            'header' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mailing_history}}');
    }
}
