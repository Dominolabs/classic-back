<?php

use yii\db\Migration;

/**
 * Class m200806_064213_add_send_email_to_users
 */
class m200806_064213_add_send_email_to_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'send_emails', $this->boolean()->null()->defaultValue(1)->after('email'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'send_emails');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200806_064213_add_send_email_to_users cannot be reverted.\n";

        return false;
    }
    */
}
