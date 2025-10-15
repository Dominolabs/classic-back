<?php
/**
 * @author Vitaliy Viznyuk <vitaliyviznyuk@gmail.com>
 * @copyright Copyright (c) 2018 Vitaliy Viznyuk
 */

use yii\db\Migration;

/**
 * Handles the creation of table `cities`.
 */
class m190212_102536_create_cities_table extends Migration
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

        $this->createTable('{{%city}}', [
            'id' => $this->primaryKey(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%city_description}}', [
            'city_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%city_description}}', '{{%city_description}}', ['city_id', 'language_id']);
        $this->addForeignKey('{{%city_description}}', '{{%city_description}}', 'city_id', '{{%city}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%city_description}}');
        $this->dropTable('{{%city}}');
    }
}
