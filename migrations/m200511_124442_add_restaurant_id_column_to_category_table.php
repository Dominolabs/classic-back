<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%category}}`.
 */
class m200511_124442_add_restaurant_id_column_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%category}}', 'restaurant_id',
            $this->integer()->after('category_id')->null());

        $this->createIndex(
            'restaurant_id',
            '{{%category}}',
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
            '{{%category}}');
        $this->dropColumn('{{%category}}', 'restaurant_id');
    }
}
