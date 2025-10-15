<?php

use yii\db\Migration;

/**
 * Class m200512_060454_change_promo_column_produt_description_table
 */
class m200512_060454_change_promo_column_produt_description_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%product_description}}', 'promo', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%product_description}}', 'promo', $this->text()->notNull());
    }
}
