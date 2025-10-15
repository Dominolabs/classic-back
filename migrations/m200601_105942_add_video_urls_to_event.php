<?php

use yii\db\Migration;

/**
 * Class m200601_105942_add_video_urls_to_event
 */
class m200601_105942_add_video_urls_to_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%event}}', '_video_urls', $this->text()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%event}}', '_video_urls');
    }
}
