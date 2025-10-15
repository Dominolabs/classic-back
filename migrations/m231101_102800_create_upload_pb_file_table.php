<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category_top_products}}`.
 */
class m231101_102800_create_upload_pb_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%upload_pb_file}}', [
            'id' => $this->primaryKey(),
            'status' => $this->tinyInteger(),
            'file' => $this->string(1024),
            'message' => $this->text(),
        ]);
        $this->createTable('{{%db_log}}', [
            'id' => $this->primaryKey(),
            'category' => $this->string(),
            'msg' => $this->text(),
            'trace' => $this->text(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%upload_pb_file}}');
        $this->dropTable('{{%db_log}}');
    }
}
