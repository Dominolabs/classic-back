<?php

use yii\db\Migration;

/**
 * Class m200526_120135_add_badges_to_product
 */
class m200526_120135_add_badges_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', '_badges', $this->text()->null()->after('sort_order'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', '_badges');
    }

}
