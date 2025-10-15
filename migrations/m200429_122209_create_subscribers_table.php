<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscribers}}`.
 */
class m200429_122209_create_subscribers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscribers}}', [
            'subscriber_id' => $this->primaryKey(),
            'name'          => $this->string(255)->notNull(),
            'email'         => $this->string(255)->notNull(),
            'created_at'    => $this->integer()->notNull(),
            'updated_at'    => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subscribers}}');
    }
}
