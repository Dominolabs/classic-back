<?php

use yii\db\Migration;

/**
 * Class m200325_092039_remove_spicy_from_product_table
 */
class m200325_092039_remove_spicy_from_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%product}}', 'spicy');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%product}}', 'spicy', $this->tinyInteger()->notNull()->defaultValue(0)->after('price2'));
    }
}
