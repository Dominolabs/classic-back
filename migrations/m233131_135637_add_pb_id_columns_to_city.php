<?php

use yii\db\Migration;

/**
 * Class m210225_135637_add_image_transparent_to_restaurant_table
 */
class m233131_135637_add_pb_id_columns_to_city extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%city}}', 'pb_id', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%city}}', 'pb_id');
    }
}
