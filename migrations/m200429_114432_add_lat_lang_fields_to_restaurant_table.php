<?php

use yii\db\Migration;

/**
 * Class m200429_114432_add_lat_lang_fields_to_restaurant_table
 */
class m200429_114432_add_lat_lang_fields_to_restaurant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%restaurant}}', 'lat', $this->text()->null()->after('sort_order'));
        $this->addColumn('{{%restaurant}}', 'long', $this->text()->null()->after('lat'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%restaurant}}', 'lat');
        $this->dropColumn('{{%restaurant}}', 'long');
    }

}
