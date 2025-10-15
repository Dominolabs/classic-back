<?php

use yii\db\Migration;

/**
 * Class m200512_123912_remove_bonuses
 */
class m200512_123912_remove_bonuses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%user_bonuses_history}}');
        $this->dropColumn('{{%user}}', 'bonuses');
        $this->dropColumn('{{%user}}', 'notifications_bonuses');
    }
}
