<?php

use yii\db\Migration;

/**
 * Class m200605_075336_add_gallery_id_to_event
 */
class m280355_084325_add_call_me_back_to_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'call_me_back', $this->boolean()->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('{{%order}}', 'call_me_back');
    }
}
