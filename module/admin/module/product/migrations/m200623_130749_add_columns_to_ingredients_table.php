<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%ingredients}}`.
 */
class m200623_130749_add_columns_to_ingredients_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%ingredient}}', 'show_in_constructor_main', $this->tinyInteger()->null()->after('sort_order'));
        $this->addColumn('{{%ingredient}}', 'show_in_constructor_additional', $this->tinyInteger()->null()->after('show_in_constructor_main'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'show_in_constructor_main');
        $this->dropColumn( 'show_in_constructor_additional');
    }
}
