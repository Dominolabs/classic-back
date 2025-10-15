<?php
/**
 * @author Vitaliy Viznyuk <vitaliyviznyuk@gmail.com>
 * @copyright Copyright (c) 2019 Vitaliy Viznyuk
 */

use yii\db\Migration;

/**
 * Class m191008_131518_add_password_reset_code_to_user_table
 */
class m191008_131518_add_password_reset_code_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'password_reset_code', $this->string()->unique()->null()->after('password_reset_token'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'password_reset_code');
    }
}
