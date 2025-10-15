<?php

use yii\db\Migration;

class m200325_071410_add_weight_dish_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%product}}', 'is_promo', $this->tinyInteger()->notNull()->defaultValue(0)->after('price'));
        $this->addColumn('{{%product}}', 'weight_dish', $this->tinyInteger()->notNull()->defaultValue(0)->after('product_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'weight_dish');
        $this->alterColumn('{{%product}}', 'is_promo', $this->tinyInteger()->notNull()->after('is_promo'));
    }
}
