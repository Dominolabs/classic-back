<?php

use yii\db\Migration;

/**
 * Class m200508_055403_add_packaging_price_fields_to_product_table
 */
class m200508_055403_add_packaging_price_fields_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'packaging_price', $this->decimal(15,4)->notNull()->after('price2'));
        $this->addColumn('{{%product}}', 'packaging_price2', $this->decimal(15,4)->notNull()->after('packaging_price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'packaging_price');
        $this->dropColumn('{{%product}}', 'packaging_price2');
    }
}
