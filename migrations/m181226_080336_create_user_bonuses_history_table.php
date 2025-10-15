<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_bonuses_history`.
 */
class m181226_080336_create_user_bonuses_history_table extends Migration
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
        $this->createTable('{{%user_bonuses_history}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->null(),
            'bonuses' => $this->integer(11)->notNull(),
            'admin_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull()
        ], $tableOptions);
        $this->createIndex('idx-user_bonuses_history-user_id', '{{%user_bonuses_history}}', 'user_id');
        $this->createIndex('idx-user_bonuses_history-admin_id', '{{%user_bonuses_history}}', 'admin_id');
        $this->addForeignKey('fk-user_bonuses_history-admin_id', '{{%user_bonuses_history}}', 'admin_id', '{{%user}}',
            'user_id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_bonuses_history-admin_id', '{{%user_bonuses_history}}');
        $this->dropIndex('idx-user_bonuses_history-admin_id', '{{%user_bonuses_history}}');
        $this->dropIndex('idx-user_bonuses_history-user_id', '{{%user_bonuses_history}}');
        $this->dropTable('{{%user_bonuses_history}}');
    }
}
