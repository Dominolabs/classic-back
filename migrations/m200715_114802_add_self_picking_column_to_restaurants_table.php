<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%restaurants}}`.
 */
class m200715_114802_add_self_picking_column_to_restaurants_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%restaurant}}', 'self_picking',
            $this->boolean()->after('online_delivery')->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%restaurant}}', 'self_picking');
    }
}
