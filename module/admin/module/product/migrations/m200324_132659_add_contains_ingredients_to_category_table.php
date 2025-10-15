<?php

use yii\db\Migration;

class m200324_132659_add_contains_ingredients_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%category}}', 'image', $this->string(255)->null());
        $this->addColumn('{{%category}}', 'contains_ingredients', $this->tinyInteger()->notNull()->defaultValue(0)->after('top'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%category}}', 'contains_ingredients');
        $this->alterColumn('{{%category}}', 'image', $this->string(255)->notNull());
    }
}
