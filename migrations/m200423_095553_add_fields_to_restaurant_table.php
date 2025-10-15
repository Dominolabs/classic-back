<?php

use yii\db\Migration;

/**
 * Class m200423_095553_add_fields_to_restaurant_table
 */
class m200423_095553_add_fields_to_restaurant_table extends Migration
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

        $this->addColumn('{{%restaurant_description}}', 'address', $this->string()->null()->after('description2'));
        $this->addColumn('{{%restaurant_description}}', 'phone', $this->string()->null()->after('description2'));
        $this->addColumn('{{%restaurant_description}}', 'schedule', $this->text()->null()->after('description2'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%restaurant_description}}', 'schedule');
        $this->dropColumn('{{%restaurant_description}}', 'phone');
        $this->dropColumn('{{%restaurant_description}}', 'address');
    }
}
