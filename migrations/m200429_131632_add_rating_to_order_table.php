<?php

use yii\db\Migration;

/**
 * Class m200429_131632_add_rating_to_order_table
 */
class m200429_131632_add_rating_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'rating', $this->integer()->notNull()->defaultValue(0)->after('token'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'rating');
    }
}
