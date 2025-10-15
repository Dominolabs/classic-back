<?php

use yii\db\Migration;

/**
 * Class m210225_135637_add_image_transparent_to_restaurant_table
 */
class m231031_135637_add_pb_id_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'pb_id', $this->string()->null());
        $this->addColumn('{{%ingredient}}', 'pb_id', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'pb_id');
        $this->dropColumn('{{%ingredient}}', 'pb_id');
    }
}
