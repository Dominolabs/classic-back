<?php

use app\module\admin\models\SourceMessage;
use yii\db\Migration;

class m190115_081700_add_time_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'time', $this->integer(11)->notNull()->after('payment_status'));

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Час доставки'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Час доставки'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Delivery Time'
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Час доставки повинен бути між {time} та 23:59'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Час доставки повинен бути між {time} та 23:59'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Delivery time must be between {time} and 23:59 PM'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'time');
        $this->delete('{{%message}}', [
            'translation' => 'Час доставки'
        ]);
        $this->delete('{{%message}}', [
            'translation' => 'Delivery Time'
        ]);
        $this->delete('{{%source_message}}', [
            'message' => 'Час доставки'
        ]);
        $this->delete('{{%message}}', [
            'translation' => 'Час доставки повинен бути між {time} та 23:59'
        ]);
        $this->delete('{{%message}}', [
            'translation' => 'Delivery time must be between {time} and 11:59 PM'
        ]);
        $this->delete('{{%source_message}}', [
            'message' => 'Час доставки повинен бути між {time} та 23:59'
        ]);
    }
}
