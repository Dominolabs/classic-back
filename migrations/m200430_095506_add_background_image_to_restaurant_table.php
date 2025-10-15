<?php

use yii\db\Migration;

/**
 * Class m200430_095506_add_background_image_to_restaurant_table
 */
class m200430_095506_add_background_image_to_restaurant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%restaurant}}', 'background_image',  $this->string()->null()->after('image'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%restaurant}}', 'background_image');
    }
}
