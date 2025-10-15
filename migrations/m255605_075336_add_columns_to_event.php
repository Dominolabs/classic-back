<?php

use yii\db\Migration;

/**
 * Class m200605_075336_add_gallery_id_to_event
 */
class m255605_075336_add_columns_to_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%event}}', 'title', $this->string()->null()->after('sort_order'));
        $this->addColumn('{{%event}}', 'description', $this->text()->null()->after('title'));
        $this->addColumn('{{%event}}', 'slug', $this->string()->null()->after('description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('{{%event}}', 'title');
       $this->dropColumn('{{%event}}', 'description');
       $this->dropColumn('{{%event}}', 'slug');
    }
}
