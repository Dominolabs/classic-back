<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%event_tag}}`.
 */
class m200427_113217_create_event_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%event_tag}}', [
            'event_id' => $this->integer(11)->unsigned()->notNull(),
            'tag_id' => $this->integer(11)->unsigned()->notNull(),
        ]);

        $this->createIndex('event_id', '{{%event_tag}}', 'event_id');
        $this->createIndex('tag_id', '{{%event_tag}}', 'tag_id');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%event_tag}}');
    }
}
