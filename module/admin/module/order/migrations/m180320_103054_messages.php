<?php

use app\module\admin\models\SourceMessage;
use yii\db\Migration;
use yii\db\Query;

class m180320_103054_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'В очікуванні',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'В очікуванні',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Pending',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Замовлення готується',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Замовлення готується',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Being prepared',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Замовлення в дорозі',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Замовлення в дорозі',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'On the road',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Доставлено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Доставлено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Delivered',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Відмінено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Відмінено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Canceled',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Готівкою при отриманні',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Готівкою при отриманні',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Payment In Cash',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Онлайн оплата',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Онлайн оплата',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Online Payment',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'E-mail',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'E-mail',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'E-mail',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Ваше замовлення успішно оформлене!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ваше замовлення успішно оформлене!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Your order has been created successfully!',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Товар не знайдено.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Товар не знайдено.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Product not found.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Не вдалося створити замовлення.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Не вдалося створити замовлення.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Failed to create order.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Замовлення не знайдено.'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Замовлення не знайдено.'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Order not found.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Забронювати стіл'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Забронювати стіл'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Book a table',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Замовити доставку'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Замовити доставку'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Order delivery',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Замовити'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Замовити'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Order',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Щоб замовити доставку Вам необхідно завантажити мобільний додаток.'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Щоб замовити доставку Вам необхідно завантажити мобільний додаток.'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'To order delivery, you need to download the mobile app.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Щоб забронювати стіл Вам необхідно зателефонувати за номером:'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Щоб забронювати стіл Вам необхідно зателефонувати за номером:'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'To book a table you need to call the number:',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'для IOS'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'для IOS'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'for IOS',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'для Android'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'для Android'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'for Android',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Або зателефонувати нам:'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Або зателефонувати нам:'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Or call us:',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Ккал'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ккал'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Kcal',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'г'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'г'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'g',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Акція!'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Акція!'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Promotion!',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Статус замовлення змінено на:'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Статус замовлення змінено на:'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Order status changed to:',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Оплата замовлення Classic'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Оплата замовлення Classic'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Classic order payment',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Не вдалося оплатити замовлення.'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Не вдалося оплатити замовлення.'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Failed to pay the order.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Не оплачено'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Не оплачено'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Not paid',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Оплачено'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Оплачено'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Paid',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Ім\'я'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ім\'я'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Name',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'E-mail'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'E-mail'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'E-mail',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Телефон'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Телефон'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Phone',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Адреса'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Адреса'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Address',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Адреса'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Адреса'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Address',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Додаткова інформація'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Додаткова інформація'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Additional Information',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Спосіб оплати'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Спосіб оплати'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Payment Type',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Використовувати бонуси'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Використовувати бонуси'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Use bonuses',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Токен'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Токен'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Token',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Щоб замовити доставку Вам необхідно зателефонувати за номером:'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Щоб замовити доставку Вам необхідно зателефонувати за номером:'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'To order a delivery, you need to call the number:',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Ваше замовлення відхилено. Сервіс MOJO ДОСТАВКА працює щодня з 12:00 до 24:00 год. Спробуйте пізніше.'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ваше замовлення відхилено. Сервіс MOJO ДОСТАВКА працює щодня з 12:00 до 24:00 год. Спробуйте пізніше.'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Your request is blocked. Delivery works from 12pm till 12am every day. Try again later.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Забронювати номер'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Забронювати номер'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Book a room',
        ]);
    }

    /**пше
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $messages = (new Query())
            ->select('source_message_id')
            ->from('{{%source_message}}')
            ->where(['category' => 'order'])
            ->all();

        foreach ($messages as $message) {
            $this->delete('{{%message}}', [
                'source_message_id' => $message['source_message_id'],
            ]);
            $this->delete('{{%source_message}}', [
                'source_message_id' => $message['source_message_id'],
            ]);
        }
    }
}
