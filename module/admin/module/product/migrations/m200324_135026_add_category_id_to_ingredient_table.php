<?php

use yii\db\Migration;

class m200324_135026_add_category_id_to_ingredient_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%ingredient}}', 'category_id', $this->integer(11)->notNull()->after('image'));
        $this->createIndex('category_id', '{{%ingredient}}', 'category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('category_id', '{{%ingredient}}');
        $this->dropColumn('{{%ingredient}}', 'category_id');
    }
}
