<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notifications_history}}`.
 */
class m200330_132652_create_notifications_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notifications_history}}', [
            'notifications_history_id' => $this->primaryKey(),
            'header' => $this->string()->notNull(),
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
        $this->dropTable('{{%notifications_history}}');
    }
}
