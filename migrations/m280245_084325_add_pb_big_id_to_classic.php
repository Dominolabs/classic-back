<?php

use yii\db\Migration;

/**
 * Class m200605_075336_add_gallery_id_to_event
 */
class m280245_084325_add_pb_big_id_to_classic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%classic}}', 'pb_big_id', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('{{%classic}}', 'pb_big_id');
    }
}
