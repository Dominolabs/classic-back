<?php
/**
 * m180312_103428_init class file.
 */

use app\module\admin\module\currency\models\Currency;
use yii\db\Migration;

/**
 * Class m180312_103428_init
 */
class m180312_103428_init extends Migration
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

        /** Creating tables **/

        $this->createTable('{{%currency}}', [
            'currency_id' => $this->primaryKey(),
            'title' => $this->string(32)->notNull(),
            'code' => $this->string(3)->notNull(),
            'symbol_left' => $this->string(16)->notNull(),
            'symbol_right' => $this->string(16)->notNull(),
            'decimal_place' => $this->string(1)->notNull(),
            'value' => $this->decimal(15,8)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        /** Creating currency **/

        $this->insert('{{%currency}}', [
            'title' => 'Гривна',
            'code' => 'UAH',
            'symbol_left' => '',
            'symbol_right' => ' грн',
            'decimal_place' => '2',
            'value' => 1,
            'status' => Currency::STATUS_ACTIVE,
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%currency}}');
    }
}
