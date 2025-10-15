<?php

use app\module\admin\models\Language;
use app\module\admin\models\Module;
use app\module\admin\models\User;

use yii\db\Migration;

class m180209_073205_init extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        /** Creating tables **/

        $this->createTable('{{%user}}', [
            'user_id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'birth_date' => $this->date()->null(),
            'avatar' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'temp_password_hash' => $this->string(255)->notNull(),
            'temp_password_created_at' => $this->integer(11)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'facebook_id' => $this->string(255)->null()->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'phone' => $this->string(32)->notNull(),
            'address' => $this->string(255)->notNull(),
            'bonuses' => $this->integer(11)->notNull(),
            'promo_code' => $this->string(10)->notNull(),
            'ref_promo_code' => $this->string(10)->notNull(),
            'role' => 'TINYINT(1) NOT NULL',
            'status' => 'TINYINT(1) NOT NULL',
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%language}}', [
            'language_id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull(),
            'code' => $this->string(16)->notNull(),
            'image' => $this->string(255)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
        ], $tableOptions);

        $this->createIndex('name', '{{%language}}', 'name');

        $this->createTable('{{%page}}', [
            'page_id' => $this->primaryKey(),
            'top' => 'TINYINT(1) NOT NULL',
            'image' => $this->string(255)->notNull(),
            'css_class' => $this->string(255)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%page_description}}', [
            'page_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'title' => $this->string(128)->notNull(),
            'content' => $this->text()->notNull(),
            'meta_title' => $this->string(255)->notNull(),
            'meta_description' => $this->string(255)->notNull(),
            'meta_keyword' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%page_description}}', '{{%page_description}}', ['page_id', 'language_id']);

        $this->createTable('{{%banner}}', [
            'banner_id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%banner_image}}', [
            'banner_image_id' => $this->primaryKey(),
            'banner_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'title' => $this->string(255)->notNull(),
            'link' => $this->string(255)->notNull(),
            'image' => $this->string(255)->notNull(),
            'sort_order' => $this->integer(3)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%source_message}}', [
            'source_message_id' => $this->primaryKey(),
            'category' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
        ], $tableOptions);

        $this->createIndex('category', '{{%source_message}}', 'category');

        $this->createTable('{{%message}}', [
            'source_message_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'translation' => $this->text()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%message}}', '{{%message}}', ['source_message_id', 'language_id']);
        $this->createIndex('source_message_id', '{{%message}}', 'source_message_id');
        $this->createIndex('language_id', '{{%message}}', 'language_id');

        $this->createTable('{{%seo_url}}', [
            'seo_url_id' => $this->primaryKey(),
            'language_id' => $this->integer(11)->notNull(),
            'query' => $this->string(255)->notNull(),
            'keyword' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->createIndex('query', '{{%seo_url}}', 'query');
        $this->createIndex('keyword', '{{%seo_url}}', 'keyword');

        $this->createTable('{{%social_network_category}}', [
            'social_network_category_id' => $this->primaryKey(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%social_network_category_description}}', [
            'social_network_category_id' => $this->integer(11)->notNull(),
            'language_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%social_network_category_description}}', '{{%social_network_category_description}}', ['social_network_category_id', 'language_id']);

        $this->createIndex('name', '{{%social_network_category_description}}', 'name');

        $this->createTable('{{%social_network}}', [
            'social_network_id' => $this->primaryKey(),
            'title' => $this->string(128)->notNull(),
            'css_class' => $this->string(32)->notNull(),
            'image' => $this->string(255)->notNull(),
            'link' => $this->string(255)->notNull(),
            'social_network_category_id' => $this->integer(11)->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
        ], $tableOptions);

        $this->createIndex('social_network_category_id', '{{%social_network}}', 'social_network_category_id');

        $this->createTable('{{%module}}', [
            'module_id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull(),
            'title' => $this->string(128)->notNull(),
            'author' => $this->string(128)->notNull(),
            'version' => $this->string(32)->notNull(),
            'setting' => $this->text()->notNull(),
            'status' => 'TINYINT(1) NOT NULL',
            'sort_order' => $this->integer(3)->notNull(),
        ], $tableOptions);

        $this->createIndex('name', '{{%module}}', 'name');

        /** Creating admin user **/

        $this->insert('{{%user}}', [
            'username' => 'admin',
            'name' => '',
            'birth_date' => null,
            'avatar' => '',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('admin'),
            'email' => 'admin@devseonetcms.com',
            'phone' => '380660475777',
            'address' => '',
            'bonuses' => 0,
            'promo_code' => '00001',
            'ref_promo_code' => '',
            'role' => User::ROLE_SUPER_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        /** Creating languages **/

        $this->insert('{{%language}}', [
            'name' => 'Українська',
            'code' => 'uk',
            'image' => 'bc73735c7d0c42ab8a8f4957670cf025.png',
            'status' => Language::STATUS_ACTIVE,
            'sort_order' => 1,
        ]);

        $this->insert('{{%language}}', [
            'name' => 'English',
            'code' => 'en',
            'image' => 'cb97b36298c30dca1345c18f78a94766.png',
            'status' => Language::STATUS_ACTIVE,
            'sort_order' => 2,
        ]);

        /** Creating modules */
        $this->insert('{{%module}}', [
            'name' => 'currency',
            'title' => 'Валюта',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => Module::STATUS_ACTIVE,
            'sort_order' => 1,
        ]);

        $this->insert('{{%module}}', [
            'name' => 'product',
            'title' => 'Продукция',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => Module::STATUS_ACTIVE,
            'sort_order' => 2,
        ]);

        $this->insert('{{%module}}', [
            'name' => 'order',
            'title' => 'Заказы',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => Module::STATUS_ACTIVE,
            'sort_order' => 3,
        ]);

        $this->insert('{{%module}}', [
            'name' => 'reservation',
            'title' => 'Бронирование',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => Module::STATUS_ACTIVE,
            'sort_order' => 4,
        ]);

        $this->insert('{{%module}}', [
            'name' => 'gallery',
            'title' => 'Галереи',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => Module::STATUS_ACTIVE,
            'sort_order' => 5,
        ]);

        $this->insert('{{%module}}', [
            'name' => 'event',
            'title' => 'Новости',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => Module::STATUS_ACTIVE,
            'sort_order' => 6,
        ]);

        $this->insert('{{%module}}', [
            'name' => 'tariff',
            'title' => 'Тарифы',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => Module::STATUS_ACTIVE,
            'sort_order' => 7,
        ]);

        $this->insert('{{%module}}', [
            'name' => 'team',
            'title' => 'Команда',
            'author' => 'Devseonet',
            'version' => '1.0.0',
            'setting' => '',
            'status' => Module::STATUS_ACTIVE,
            'sort_order' => 8,
        ]);

        /** Creating demo data **/
        $this->execute(file_get_contents(__DIR__ . '/init.sql'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('name', '{{%module}}');
        $this->dropTable('{{%module}}');
        $this->dropIndex('social_network_category_id', '{{%social_network}}');
        $this->dropTable('{{%social_network}}');
        $this->dropIndex('name', '{{%social_network_category_description}}');
        $this->dropPrimaryKey('{{%social_network_category_description}}', '{{%social_network_category_description}}');
        $this->dropTable('{{%social_network_category_description}}');
        $this->dropTable('{{%social_network_category}}');
        $this->dropIndex('keyword', '{{%seo_url}}');
        $this->dropIndex('query', '{{%seo_url}}');
        $this->dropTable('{{%seo_url}}');
        $this->dropIndex('language_id', '{{%message}}');
        $this->dropIndex('source_message_id', '{{%message}}');
        $this->dropPrimaryKey('{{%message}}', '{{%message}}');
        $this->dropTable('{{%message}}');
        $this->dropIndex('category', '{{%source_message}}');
        $this->dropTable('{{%source_message}}');
        $this->dropTable('{{%banner_image}}');
        $this->dropTable('{{%banner}}');
        $this->dropPrimaryKey('{{%page_description}}', '{{%page_description}}');
        $this->dropTable('{{%page_description}}');
        $this->dropTable('{{%page}}');
        $this->dropIndex('name', '{{%language}}');
        $this->dropTable('{{%language}}');
        $this->dropTable('{{%user}}');
    }
}
