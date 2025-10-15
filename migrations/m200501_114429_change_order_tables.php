<?php

use yii\db\Migration;

/**
 * Class m200501_114429_change_order_tables
 */
class m200501_114429_change_order_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%order}}', 'user_id', $this->integer()->null());
        $this->dropColumn('{{%order}}', 'username');
        $this->addColumn('{{%order}}', 'name', $this->string()->null()->after('user_id'));
        $this->alterColumn('{{%order}}', 'phone', $this->string(32)->null());
        $this->dropColumn('{{%order}}', 'address');
        $this->alterColumn('{{%order}}', 'city_id', $this->integer()->null()->after('phone'));
        $this->addColumn('{{%order}}', 'street', $this->string()->null()->after('city_id'));
        $this->addColumn('{{%order}}', 'entrance', $this->string()->null()->after('street'));
        $this->addColumn('{{%order}}', 'house_number', $this->string()->null()->after('entrance'));
        $this->addColumn('{{%order}}', 'apartment_number', $this->string()->null()->after('house_number'));
        $this->alterColumn('{{%order}}', 'do_not_call', $this->tinyInteger(1)->null()->after('apartment_number'));
        $this->alterColumn('{{%order}}', 'have_a_child', $this->tinyInteger(1)->null()->after('do_not_call'));
        $this->alterColumn('{{%order}}', 'have_a_dog', $this->tinyInteger(1)->null()->after('have_a_child'));
        $this->alterColumn('{{%order}}', 'have_a_cat', $this->tinyInteger(1)->null()->after('have_a_dog'));
        $this->alterColumn('{{%order}}', 'comment', $this->text()->null()->after('have_a_cat'));
        $this->alterColumn('{{%order}}', 'time', $this->integer()->null()->after('comment'));
        $this->alterColumn('{{%order}}', 'payment_type', $this->tinyInteger(1)->null()->after('time'));
        $this->alterColumn('{{%order}}', 'payment_status', $this->tinyInteger(1)->null()->after('payment_type'));
        $this->alterColumn('{{%order}}', 'language_id', $this->integer()->null()->after('payment_status'));
        $this->alterColumn('{{%order}}', 'currency_id', $this->integer()->null()->after('language_id'));
        $this->alterColumn('{{%order}}', 'currency_code', $this->string(3)->null()->after('currency_id'));
        $this->alterColumn('{{%order}}', 'currency_value', $this->decimal(15, 8)->null()->after('currency_code'));
        $this->alterColumn('{{%order}}', 'sum', $this->decimal(15, 4)->null()->after('currency_value'));
        $this->alterColumn('{{%order}}', 'bonuses', $this->decimal(15, 4)->null()->after('sum'));
        $this->alterColumn('{{%order}}', 'packing', $this->decimal(15, 4)->null()->after('bonuses'));
        $this->alterColumn('{{%order}}', 'delivery', $this->decimal(15, 4)->null()->after('packing'));
        $this->alterColumn('{{%order}}', 'total', $this->decimal(15, 4)->null()->after('delivery'));
        $this->dropColumn('{{%order}}', 'token');
        $this->alterColumn('{{%order}}', 'rating', $this->integer()->null()->defaultValue(0)->after('pizzeria_id'));
        $this->alterColumn('{{%order}}', 'status', $this->tinyInteger(1)->null()->defaultValue(1)->after('rating'));
        $this->alterColumn('{{%order}}', 'created_at', $this->integer()->null()->after('status'));
        $this->alterColumn('{{%order}}', 'updated_at', $this->integer()->null()->after('created_at'));
        $this->alterColumn('{{%order_product}}', 'name', $this->string()->null()->after('product_id'));
        $this->alterColumn('{{%order_product}}', 'quantity', $this->integer()->null()->after('name'));
        $this->alterColumn('{{%order_product}}', 'price', $this->decimal(15,4)->null()->after('quantity'));
        $this->alterColumn('{{%order_product}}', 'total', $this->decimal(15,4)->null()->after('price'));
        $this->addColumn('{{%order_product}}', 'type', $this->string()->null()->defaultValue('product'));
        $this->addColumn('{{%order_product}}', 'ingredients', $this->text()->null());
        $this->addColumn('{{%order_product}}', 'properties', $this->text()->null());

    }
}
