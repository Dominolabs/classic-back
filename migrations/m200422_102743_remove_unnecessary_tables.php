<?php

use yii\db\Migration;

/**
 * Class m200422_102743_remove_unnecessary_tables
 */
class m200422_102743_remove_unnecessary_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%page_to_category}}');
        $this->dropTable('{{%page_category_path}}');
        $this->dropTable('{{%page_category_description}}');
        $this->dropTable('{{%page_category}}');

        $this->dropIndex('album_category_id', '{{%album}}');
        $this->dropColumn('{{%album}}', 'album_category_id');
        $this->dropIndex('name', '{{%album_category_description}}');
        $this->dropPrimaryKey('{{%album_category_description}}', '{{%album_category_description}}');
        $this->dropTable('{{%album_category_description}}');
        $this->dropTable('{{%album_category}}');

        $this->dropIndex('social_network_category_id', '{{%social_network}}');
        $this->dropTable('{{%social_network}}');
        $this->dropIndex('name', '{{%social_network_category_description}}');
        $this->dropPrimaryKey('{{%social_network_category_description}}', '{{%social_network_category_description}}');
        $this->dropTable('{{%social_network_category_description}}');
        $this->dropTable('{{%social_network_category}}');
    }
}
