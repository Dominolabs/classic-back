<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_product}}`.
 */
class m200521_083616_add_is_weight_dish_column_to_order_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_product}}', 'weight_dish', $this->tinyInteger()->notNull()->defaultValue(0)->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_product}}', 'weight_dish');
    }
}
