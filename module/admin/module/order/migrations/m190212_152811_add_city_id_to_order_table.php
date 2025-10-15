<?php

use app\module\admin\models\SourceMessage;
use yii\db\Migration;

class m190212_152811_add_city_id_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'city_id', $this->integer(11)->notNull()->after('time'));

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Місто'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Місто'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'City'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'city_id');

        $this->delete('{{%message}}', [
            'translation' => 'Місто'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'City'
        ]);

        $this->delete('{{%source_message}}', [
            'message' => 'Місто'
        ]);
    }
}
