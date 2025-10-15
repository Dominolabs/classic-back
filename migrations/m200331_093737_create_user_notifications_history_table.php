<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_notifications_history}}`.
 */
class m200331_093737_create_user_notifications_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_notifications_history}}', [
            'user_notifications_history_id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'header' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ]);

        $this->createIndex('user_id', '{{%user_notifications_history}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('user_id', '{{%user_notifications_history}}');
        $this->dropTable('{{%user_notifications_history}}');
    }
}
