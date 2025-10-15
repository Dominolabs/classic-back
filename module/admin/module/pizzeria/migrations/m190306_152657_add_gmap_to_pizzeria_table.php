<?php

use yii\db\Migration;

class m190306_152657_add_gmap_to_pizzeria_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pizzeria}}', 'gmap', $this->string(255)->null()->after('instagram'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%pizzeria}}', 'gmap');
    }
}
