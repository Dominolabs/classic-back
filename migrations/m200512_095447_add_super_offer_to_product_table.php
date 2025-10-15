<?php

use yii\db\Migration;

/**
 * Class m200512_095447_add_super_offer_to_product_table
 */
class m200512_095447_add_super_offer_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'super_offer',
            $this->boolean()->after('weight_dish')->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'super_offer');
    }
}
