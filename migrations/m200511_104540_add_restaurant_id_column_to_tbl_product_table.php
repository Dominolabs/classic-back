<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%tbl_product}}`.
 */
class m200511_104540_add_restaurant_id_column_to_tbl_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'restaurant_id',
            $this->integer()->after('product_id')->defaultValue(1));

        $this->createIndex(
            'restaurant_id',
            '{{%product}}',
            'restaurant_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'restaurant_id',
            '{{%product}}');
        $this->dropColumn('{{%product}}', 'restaurant_id');
    }
}
