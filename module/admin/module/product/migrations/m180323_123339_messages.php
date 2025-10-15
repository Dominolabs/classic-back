<?php

use app\module\admin\models\SourceMessage;
use yii\db\Migration;
use yii\db\Query;

class m180323_123339_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Номер телефону',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Номер телефону',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Phone Number',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Password',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
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
            'category' => 'product',
            'message' => 'Невірний номер телефону або пароль.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Невірний номер телефону або пароль.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Invalid phone number or password.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Ця адреса електронної пошти вже використовується.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ця адреса електронної пошти вже використовується.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'This email is already in use.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Цей номер телефону вже використовується.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Цей номер телефону вже використовується.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'This phone number is already in use.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Користувача з такою адресою електронної пошти не знайдено.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Користувача з такою адресою електронної пошти не знайдено.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'User with this email address is not found.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'робот',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'робот',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'robot',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Скидання паролю для',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Скидання паролю для',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Reset password for',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Доброго дня',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Доброго дня',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Hello',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Або перейдіть по посиланню нижче, щоб скинути пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Або перейдіть по посиланню нижче, щоб скинути пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Or, follow the link below to reset your password',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Перевірте свою електронну пошту для отримання подальших інструкцій щодо скидання паролю.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Перевірте свою електронну пошту для отримання подальших інструкцій щодо скидання паролю.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Check your email for further instructions on resetting your password.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Вибачте, не вдалося скинути пароль для вказаної адреси електронної пошти. Спробуйте пізніше.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Вибачте, не вдалося скинути пароль для вказаної адреси електронної пошти. Спробуйте пізніше.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Sorry, failed to reset password for the specified email address. Try again later.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Паролі не співпадають.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Паролі не співпадають.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Passwords do not match.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Дані успішно збережено.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Дані успішно збережено.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Settings have been saved successfully.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Старий пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Старий пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Old Password',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Новий пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Новий пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'New Password',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Ви ввели невірний пароль.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ви ввели невірний пароль.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'You have entered an incorrect password.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Пароль успішно змінений.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Пароль успішно змінений.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Password successfully changed.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Тимчасовий пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Тимчасовий пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Temporary password',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Ваш новий пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ваш новий пароль',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Your new password',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Ви або хтось інший запросили відновлення пароля до вашого профілю.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ви або хтось інший запросили відновлення пароля до вашого профілю.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'You or someone else has requested a password reset to your account.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Ви увійшли з тимчасовим паролем. Будь ласка змініть ваш пароль в особистому кабінеті.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ви увійшли з тимчасовим паролем. Будь ласка змініть ваш пароль в особистому кабінеті.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'You are logged in with a temporary password. Please change your password in your personal account.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'product',
            'message' => 'Запитуваний користувач не знайдений.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Запитуваний користувач не знайдений.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'The requested user was not found.',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $messages = (new Query())
            ->select('source_message_id')
            ->from('{{%source_message}}')
            ->where(['category' => 'product'])
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
