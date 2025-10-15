<?php

use yii\db\Migration;

/**
 * Class m200214_114400_add_notifictations_fields_to_user_table
 */
class m200214_114400_add_notifictations_fields_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'notifications_news', $this->tinyInteger(1)->null()->after('ref_promo_code'));
        $this->addColumn('{{%user}}', 'notifications_bonuses', $this->tinyInteger(1)->null()->after('notifications_news'));
        $this->addColumn('{{%user}}', 'notifications_delivery', $this->tinyInteger(1)->null()->after('notifications_bonuses'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'notifications_delivery');
        $this->dropColumn('{{%user}}', 'notifications_bonuses');
        $this->dropColumn('{{%user}}', 'notifications_news');
    }
}
