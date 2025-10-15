<?php

use yii\db\Migration;

/**
 * Class m200605_075336_add_gallery_id_to_event
 */
class m275605_064325_add_columns_to_city extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%city}}', 'free_minimum_order', $this->integer()->null()->after('minimum_order'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('{{%city}}', 'title');
    }
}
