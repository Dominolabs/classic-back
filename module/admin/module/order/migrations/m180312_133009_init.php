<?php

use app\module\admin\models\User;
use yii\db\Migration;

class m180312_133009_init extends Migration
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

        $this->createTable('{{%order}}', [
            'order_id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'username' => $this->string(255)->notNull(),
            'email' => $this->string(255)->null(),
            'phone' => $this->string(32)->notNull(),
            'address' => $this->string(255)->null(),
            'comment' => $this->text()->null(),
            'sum' => $this->decimal(15,4)->notNull(),
            'bonuses' => $this->decimal(15,4)->notNull(),
            'packing' => $this->decimal(15,4)->notNull(),
            'delivery' => $this->decimal(15,4)->notNull(),
            'total' => $this->decimal(15,4)->notNull(),
            'language_id' => 'TINYINT(1) NOT NULL',
            'currency_id' => $this->integer(11)->notNull(),
            'currency_code' => $this->string(3)->notNull(),
            'currency_value' => $this->decimal(15,8)->notNull(),
            'payment_type' => 'TINYINT(1) NOT NULL',
            'payment_status' => 'TINYINT(1) NOT NULL',
            'token' => $this->string(255)->null(),
            'status' => 'TINYINT(1) NOT NULL',
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%order_history}}', [
            'order_history_id' => $this->primaryKey(),
            'order_id' => $this->integer(11)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'comment' => $this->text()->notNull(),
            'created_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%order_product}}', [
            'order_product_id' => $this->primaryKey(),
            'order_id' => $this->integer(11)->notNull(),
            'product_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'quantity' => $this->integer(11)->notNull(),
            'price' => $this->decimal(15,4)->notNull(),
            'total' => $this->decimal(15,4)->notNull(),
        ], $tableOptions);

        $this->createIndex('order_id', '{{%order_product}}', 'order_id');

        /** Creating test user */

        $this->insert('{{%user}}', [
            'username' => 'vitaliy',
            'name' => '',
            'birth_date' => null,
            'avatar' => '',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('vitaliy'),
            'email' => 'vitaliyviznyuk@gmail.com',
            'phone' => '380683689604',
            'address' => 'м. Луцьк',
            'bonuses' => 0,
            'promo_code' => '00002',
            'ref_promo_code' => '',
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        /** Deleting test user */

        $this->delete('{{%user}}', [
            'username' => 'vitaliy',
        ]);

        /** Deleting tables */
        $this->dropIndex('order_id', '{{%order_product}}');
        $this->dropTable('{{%order_product}}');
        $this->dropTable('{{%order_history}}');
        $this->dropTable('{{%order}}');
    }
}
