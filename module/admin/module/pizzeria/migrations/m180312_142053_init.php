<?php


use yii\db\Migration;

class m180312_142053_init extends Migration
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

        $this->createTable('{{%pizzeria}}', [
            'pizzeria_id' => $this->primaryKey(),
            'image' => $this->string(255)->notNull(),
            'phones' => $this->text()->notNull(),
            'email' => $this->string(255)->null(),
            'instagram' => $this->string(255)->null(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%pizzeria_description}}', [
            'pizzeria_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'address' => $this->string(255)->null(),
            'schedule' => $this->string(255)->null(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%pizzeria_description}}', '{{%pizzeria_description}}', ['pizzeria_id', 'language_id']);

        $this->execute(file_get_contents(__DIR__ . '/init.sql'));

        $this->insert('{{%module}}', [
            'name' => 'pizzeria',
            'title' => 'Пиццерии',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => 1,
            'sort_order' => 8,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pizzeria_description}}');
        $this->dropTable('{{%pizzeria}}');
    }
}
