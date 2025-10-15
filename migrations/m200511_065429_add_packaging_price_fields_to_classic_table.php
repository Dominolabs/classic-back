<?php

use yii\db\Migration;

/**
 * Class m200511_065429_add_packaging_price_fields_to_classic_table
 */
class m200511_065429_add_packaging_price_fields_to_classic_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%classic}}', 'packaging_price', $this->decimal(15,4)->null()->after('price2'));
        $this->addColumn('{{%classic}}', 'packaging_price2', $this->decimal(15,4)->null()->after('packaging_price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%classic}}', 'packaging_price');
        $this->dropColumn('{{%classic}}', 'packaging_price2');
    }
}
