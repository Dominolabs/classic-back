<?php

use app\module\api\module\viber\models\ViberCommand;
use app\module\api\module\viber\models\ViberCommandTranslation;
use yii\db\Migration;

/**
 * Class m200626_122233_init
 */
class m200626_122233_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%viber_chat}}', [
            'viber_chat_id' => $this->primaryKey(),
            'chat_number' => $this->string()->null(),
            'chat_hostname' => $this->string()->null(),
            'name' => $this->string()->null(),
            'uri' => $this->string()->null(),
            'icon' => $this->string()->null(),
            'background' => $this->text()->null(),
            'category' => $this->string()->null(),
            'subcategory' => $this->string()->null(),
            'location' => $this->text()->null(),
            'country' => $this->string()->null(),
            'webhook' => $this->text()->null(),
            'token' => $this->text()->null(),
        ], $tableOptions);


        $this->createTable('{{%viber_chat_viber_user}}', [
            'viber_chat_viber_user_id' => $this->primaryKey(),
            'viber_chat_id' => $this->bigInteger(),
            'viber_user_id' => $this->bigInteger(),
            'subscribed_at' => $this->bigInteger()->null(),
            'unsubscribed_at' => $this->bigInteger()->null(),
        ], $tableOptions);

        $this->createIndex('viber_chat_id', '{{%viber_chat_viber_user}}', 'viber_chat_id');
        $this->createIndex('viber_user_id', '{{%viber_chat_viber_user}}', 'viber_user_id');


        $this->createTable('{{%viber_user}}', [
            'viber_user_id' => $this->primaryKey(),
            'user_id' => $this->bigInteger(),
            'viber_id' => $this->string()->unique(),
            'name' => $this->string()->null(),
            'avatar' => $this->text()->null(),
            'country' => $this->string()->null(),
            'language' => $this->string()->null(),
            'api_version' => $this->string()->null(),
            'phone' => $this->string()->null(),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%viber_user}}', 'user_id');

        $this->createTable('{{%viber_message}}', [
            'viber_message_id' => $this->primaryKey(),
            'viber_chat_id' => $this->bigInteger()->null(),
            'message_token' => $this->string()->null(),
            'sender' => $this->text()->null(),
            'message_type' => $this->string()->null(),
            'message' => $this->text()->null(),
            'tracking_data' => $this->string()->null(),
            'sent_at' => $this->bigInteger()->null(),
            'type' => $this->string()->null(),
            'file' => $this->text()->null()
        ]);

        $this->createIndex('viber_chat_id', '{{%viber_message}}', 'viber_chat_id');

        $this->createTable('{{%viber_message_viber_user}}', [
            'viber_message_viber_user_id' => $this->primaryKey(),
            'viber_message_id' => $this->bigInteger(),
            'viber_user_id' => $this->bigInteger(),
            'delivered_at' => $this->bigInteger()->null(),
            'seen_at' => $this->bigInteger()->null(),
        ]);

        $this->createIndex('viber_message_id', '{{%viber_message_viber_user}}', 'viber_message_id');
        $this->createIndex('viber_user_id', '{{%viber_message_viber_user}}', 'viber_user_id');

        $this->createTable('{{%viber_command}}', [
            'command_id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->createTable('{{%viber_command_translation}}', [
            'translation_id' => $this->primaryKey(),
            'command_id' => $this->bigInteger(),
            'language_id' => $this->bigInteger(),
            'translation' => $this->string()
        ]);

        $this->createIndex('command_id', '{{%viber_command_translation}}', 'command_id');
        $this->createIndex('language_id', '{{%viber_command_translation}}', 'language_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%viber_command}}');
        $this->dropTable('{{%viber_command_translation}}');
        $this->dropTable('{{%viber_message_viber_user}}');
        $this->dropTable('{{%viber_message}}');
        $this->dropTable('{{%viber_user}}');
        $this->dropTable('{{%viber_chat_viber_user}}');
        $this->dropTable('{{%viber_chat}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200626_122233_init cannot be reverted.\n";

        return false;
    }
    */
}
