<?php

use yii\db\Migration;

/**
 * Class m200512_081040_add_online_delivery_to_restaurant_table
 */
class m200512_081040_add_online_delivery_to_restaurant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%restaurant}}', 'online_delivery',
            $this->boolean()->after('vk')->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%restaurant}}', 'online_delivery');
    }
}
