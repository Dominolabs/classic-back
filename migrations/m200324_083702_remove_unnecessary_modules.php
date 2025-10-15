<?php

use app\module\admin\models\Module;
use yii\db\Migration;

class m200324_083702_remove_unnecessary_modules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%migration_booking}}');
        $this->dropTable('{{%migration_hotelservice}}');
        $this->dropTable('{{%migration_reservation}}');
        $this->dropTable('{{%migration_room}}');
        $this->dropTable('{{%booking_history}}');
        $this->dropTable('{{%booking}}');
        $this->dropTable('{{%hotelservice}}');
        $this->dropTable('{{%hotelservice_description}}');
        $this->dropTable('{{%reservation}}');
        $this->dropTable('{{%room_booking_history}}');
        $this->dropTable('{{%room_booking}}');
        $this->dropTable('{{%room_description}}');
        $this->dropTable('{{%room_image}}');
        $this->dropTable('{{%room_type_description}}');
        $this->dropTable('{{%room_type}}');
        $this->dropTable('{{%room}}');

        Module::deleteAll(['name' => 'booking']);
        Module::deleteAll(['name' => 'hotelservice']);
        Module::deleteAll(['name' => 'room']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
