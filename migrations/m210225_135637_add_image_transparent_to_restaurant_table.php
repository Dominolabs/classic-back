<?php

use yii\db\Migration;

/**
 * Class m210225_135637_add_image_transparent_to_restaurant_table
 */
class m210225_135637_add_image_transparent_to_restaurant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%restaurant}}', 'image_transparent', $this->string()->null()->after('image'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%restaurant}}', 'image_transparent');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210225_135637_add_image_transparent_to_restaurant_table cannot be reverted.\n";

        return false;
    }
    */
}
