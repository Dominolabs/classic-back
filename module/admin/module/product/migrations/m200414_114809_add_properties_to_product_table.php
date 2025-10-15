<?php

use yii\db\Migration;

/**
 * Class m200414_114809_add_properties_to_product_table
 */
class m200414_114809_add_properties_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'properties', $this->text()->null()->after('is_promo'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'properties');
    }
}
