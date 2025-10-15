<?php

use yii\db\Migration;

/**
 * Class m200424_120754_add_gmap_to_restaurant_table
 */
class m200424_120754_add_gmap_to_restaurant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%restaurant_description}}', 'gmap', $this->text()->null()->after('address'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%restaurant_description}}', 'gmap');
    }
}
