<?php

use yii\db\Migration;

/**
 * Class m200512_080603_remove_have_a_cat_from_order_table
 */
class m200512_080603_remove_have_a_cat_from_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%order}}', 'have_a_cat');
    }
}
