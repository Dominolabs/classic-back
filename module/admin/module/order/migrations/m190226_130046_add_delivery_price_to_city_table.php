<?php

use yii\db\Migration;

class m190226_130046_add_delivery_price_to_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%city}}', 'delivery_price', $this->decimal(15,4)->notNull()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%city}}', 'delivery_price');
    }
}
