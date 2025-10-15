<?php

use yii\db\Migration;

/**
 * Class m200424_062613_add_changes_to_page_and_page_description_tables
 */
class m200424_062613_add_changes_to_page_and_page_description_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%page}}', 'top');
        $this->dropColumn('{{%page}}', 'css_class');
        $this->dropColumn('{{%page_description}}', 'content');

        $this->addColumn('{{%page}}', 'top_banner_id', $this->integer()->null());
        $this->addColumn('{{%page}}', 'gallery_id', $this->integer()->null());
        $this->addColumn('{{%page}}', 'facebook', $this->string()->null());
        $this->addColumn('{{%page}}', 'instagram', $this->string()->null());
        $this->addColumn('{{%page}}', 'youtube', $this->string()->null());
        $this->addColumn('{{%page}}', 'vk', $this->string()->null());
        $this->addColumn('{{%page}}', 'footer_columns', $this->text()->null());

        $this->addColumn('{{%page_description}}', 'description1', $this->text()->null());
        $this->addColumn('{{%page_description}}', 'description2', $this->text()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%page}}', 'top', 'TINYINT(1) NOT NULL');
        $this->addColumn('{{%page}}', 'css_class', $this->string(255)->notNull());
        $this->addColumn('{{%page_description}}', 'content', $this->text()->notNull());

        $this->dropColumn('{{%page}}', 'top_banner_id');
        $this->dropColumn('{{%page}}', 'gallery_id');
        $this->dropColumn('{{%page}}', 'facebook');
        $this->dropColumn('{{%page}}', 'instagram');
        $this->dropColumn('{{%page}}', 'youtube');
        $this->dropColumn('{{%page}}', 'vk');
        $this->dropColumn('{{%page}}', 'footer_columns');

        $this->dropdColumn('{{%page_description}}', 'description1');
        $this->dropColumn('{{%page_description}}', 'description2');
    }


}
