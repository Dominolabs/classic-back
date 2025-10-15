<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%restaurant_category}}`.
 */
class m200423_054448_create_restaurant_category_table extends Migration
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

        $this->createTable('{{%restaurant_category}}', [
            'restaurant_category_id' => $this->primaryKey(),
            'status' => $this->tinyInteger()->null()->defaultValue(1),
            'sort_order' => $this->integer(3)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%restaurant_category_description}}', [
            'restaurant_category_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->null(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%restaurant_category_description}}', '{{%restaurant_category_description}}', ['restaurant_category_id', 'language_id']);

        $this->createIndex('name', '{{%restaurant_category_description}}', 'name');

        $this->addColumn('{{%restaurant}}', 'restaurant_category_id', $this->integer()->null()->after('restaurant_id'));

        $this->createIndex('restaurant_category_id', '{{%restaurant}}', 'restaurant_category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('restaurant_category_id', '{{%restaurant}}');
        $this->dropColumn('{{%restaurant}}', 'restaurant_category_id');
        $this->dropTable('{{%restaurant_category_description}}');
        $this->dropTable('{{%restaurant_category}}');
    }
}
