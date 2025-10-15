<?php

use yii\db\Migration;

/**
 * Class m200511_051820_change_packaging_price_fileds_product_table
 */
class m200511_051820_change_packaging_price_fileds_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%product}}', 'packaging_price', $this->decimal(15,4)->null()->after('price2'));
        $this->alterColumn('{{%product}}', 'packaging_price2', $this->decimal(15,4)->null()->after('packaging_price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%product}}', 'packaging_price', $this->decimal(15,4)->notNull()->after('price2'));
        $this->alterColumn('{{%product}}', 'packaging_price2', $this->decimal(15,4)->notNull()->after('packaging_price'));
    }

}
