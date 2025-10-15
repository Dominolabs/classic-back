<?php

use yii\db\Migration;

class m200325_074531_add_prices_and_spicy_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'price2', $this->decimal(15,4)->notNull()->after('price'));
        $this->addColumn('{{%product}}', 'spicy', $this->tinyInteger()->notNull()->defaultValue(0)->after('price2'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'spicy');
        $this->dropColumn('{{%product}}', 'price2');
    }
}
