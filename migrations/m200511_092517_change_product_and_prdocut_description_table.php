<?php

use yii\db\Migration;

/**
 * Class m200511_092517_change_product_and_prdocut_description_table
 */
class m200511_092517_change_product_and_prdocut_description_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_description}}', 'weight', $this->string()->null()->after('name'));
        $this->dropColumn('{{%product}}', 'weight');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%product}}', 'weight', $this->string()->null()->after('name'));
        $this->dropColumn('{{%product_description}}', 'weight');
    }
}
