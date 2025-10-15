<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tag_description}}`.
 */
class m200427_114336_create_tag_description_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tag_description}}', [
            'tag_id' => $this->integer(11)->unsigned()->notNull(),
            'language_id' => $this->integer(11)->unsigned()->notNull(),
            'name' => $this->string(255)->notNull(),
        ]);

        $this->addPrimaryKey('{{%tag_description}}', '{{%tag_description}}', ['tag_id', 'language_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tag_description}}');
    }
}
