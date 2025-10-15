<?php

use yii\db\Migration;

class m190130_075018_add_device_id_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'device_id', $this->string()->null()->after('facebook_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'device_id');
    }
}
