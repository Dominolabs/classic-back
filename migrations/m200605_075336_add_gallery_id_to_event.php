<?php

use yii\db\Migration;

/**
 * Class m200605_075336_add_gallery_id_to_event
 */
class m200605_075336_add_gallery_id_to_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%event}}', 'gallery_id', $this->bigInteger()->null()->after('event_id'));
        $this->createIndex('gallery_id', '{{%event}}', 'gallery_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropIndex('gallery_id', '{{%event}}');
       $this->dropColumn('{{%event}}', 'gallery_id');
    }
}
