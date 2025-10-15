<?php

use app\module\admin\models\Classic;
use yii\db\Migration;

/**
 * Class m200418_121306_create_classic_pizza_tables
 */
class m200418_121306_create_classic_pizza_tables extends Migration
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

        $this->createTable('{{%classic}}', [
            'product_id' => $this->primaryKey(),
            'image' => $this->string(255)->null(),
            'price' => $this->decimal(15,4)->null(),
            'price2' => $this->decimal(15,4)->null(),
            'properties' => $this->text()->null(),
            'status' => 'TINYINT(1) NOT NULL',
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%classic_description}}', [
            'product_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->null(),
            'description' => $this->text()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%classic_description}}', '{{%classic_description}}', ['product_id', 'language_id']);

        $this->createIndex('name', '{{%classic_description}}', 'name');

        $this->insert('{{%classic}}', [
            'image' => null,
            'price' => 120,
            'price2' => 150,
            'properties' => null,
            'status' => Classic::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%classic_description}}', [
            'product_id' => 1,
            'language_id' => 1,
            'name' => 'Піца "Класік"',
            'description' => '',
        ]);

        $this->insert('{{%classic_description}}', [
            'product_id' => 1,
            'language_id' => 2,
            'name' => 'Pizza "Classic"',
            'description' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('name', '{{%classic_description}}');
        $this->dropTable('{{%classic_description}}');
        $this->dropTable('{{%classic}}');
    }
}
