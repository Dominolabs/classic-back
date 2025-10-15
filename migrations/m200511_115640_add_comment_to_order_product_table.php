<?php

use yii\db\Migration;

/**
 * Class m200511_115640_add_comment_to_order_product_table
 */
class m200511_115640_add_comment_to_order_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_product}}', 'comment', $this->text()->null());
    }
}
