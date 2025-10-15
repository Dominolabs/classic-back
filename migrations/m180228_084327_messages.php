<?php

use app\module\admin\models\SourceMessage;
use yii\db\Migration;

/**
 * Class m180228_084327_messages
 */
class m180228_084327_messages extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%source_message}}', [
            'category' => 'footer',
            'message' => 'Контакти',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Контакти',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Contacts',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'footer',
            'message' => 'Подивитися на карті',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Подивитися на карті',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'View on map',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => '404',
            'message' => 'Ой!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ой!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Oh!',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => '404',
            'message' => 'Нажаль сторінки не знайдено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Нажаль сторінки не знайдено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Sorry, the page was not found',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => '404',
            'message' => 'Сталась помилка. Ви можете спробувати оновити сторінку. Інколи це працює, або перейти на іншу сторінку сайту.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Сталась помилка. Ви можете спробувати оновити сторінку. Інколи це працює, або перейти на іншу сторінку сайту.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'An error occurred. You can try refreshing the page. Sometimes it works, or go to another site page.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => '404',
            'message' => 'Перейти на головну',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Перейти на головну',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Go to homepage',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'family',
            'message' => 'Інтер\'єр',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Інтер\'єр',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Interior',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'family',
            'message' => 'Акції та події',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Акції та події',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Promotions and events',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'family',
            'message' => 'Галереи',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Галереи',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Gallery',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'family',
            'message' => 'Переглянути усі альбоми',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Переглянути усі альбоми',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'View all albums',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'family',
            'message' => 'Перейти в меню',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Перейти в меню',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Go to menu',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'family',
            'message' => 'Переглянути меню бару',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Переглянути меню бару',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'View pub menu',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'menu',
            'message' => 'Меню',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Меню',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Menu',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'menu',
            'message' => 'грн',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'грн',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'UAH',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'recommend',
            'message' => 'Ваш друг рекомендує',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ваш друг рекомендує',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Your friend recommends',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'recommend',
            'message' => 'Мобільний додаток',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Мобільний додаток',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Mobile app',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'recommend',
            'message' => 'Завантажте мобільний додаток, та введіть промо-код друга:',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Завантажте мобільний додаток, та введіть промо-код друга:',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Download the mobile app and enter your friend\'s promotional code:',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'recommend',
            'message' => 'Завантажте мобільний додаток.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Завантажте мобільний додаток.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Download the mobile app.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'event-hall',
            'message' => 'Тарифи',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Тарифи',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Tariffs',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'fitness',
            'message' => 'Команда',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Команда',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Team',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'webcam',
            'message' => 'Отримати доступ до камери',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Отримати доступ до камери',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Get access to camera',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'webcam',
            'message' => 'Щоб отримати доступ до камери введіть код доступу.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Щоб отримати доступ до камери введіть код доступу.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'To access the camera, enter the access code.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'webcam',
            'message' => 'Введіть код',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Введіть код',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Enter the code',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'webcam',
            'message' => 'Отримати доступ',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Отримати доступ',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Get access',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'webcam',
            'message' => 'Невірний пароль.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Невірний пароль.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Invalid password.',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'webcam',
            'message' => 'Код',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Код',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Code',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'webcam',
            'message' => 'KIDS ONLINE',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'KIDS ONLINE',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'KIDS ONLINE',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'catering',
            'message' => 'Catering',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Catering',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Catering',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'api',
            'message' => 'Оновіть, будь ласка, додаток до найновішої версії.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Оновіть, будь ласка, додаток до найновішої версії.',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Please update application to the latest version.',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'одномісні',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'одномісні',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'single',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'одномісне ліжко',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'одномісне ліжко',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'single bed',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'двомісне ліжко',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'двомісне ліжко',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'double bed',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Згорнути',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Згорнути',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Less',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Вільних номерів',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Вільних номерів',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Free rooms',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'ночі',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'ночі',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'nights',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'номера',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'номера',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'room',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Номер',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Номер',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Room',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Дорослих',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Дорослих',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Adults',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Дітей',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Дітей',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Children',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'На даний період вільні номери відсутні',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'На даний період вільні номери відсутні',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Sorry, we have no free rooms for chosen period',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Забронювати',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Забронювати',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Book the rooms',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'номер',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'номер',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'room',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'гостя',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'гостя',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'guests',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'ночі',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'ночі',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'nights',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => '{n, plural, =0{ночей} =1{ніч} one{# ніч} few{# ночі} many{# ночей} other{# ночей}}',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => '{n, plural, =0{ночей} =1{ніч} one{# ніч} few{# ночі} many{# ночей} other{# ночей}}',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => '{n, plural, =0{nights} =1{night} one{# night} few{# nights} many{# nights} other{# nights}}',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => '{n, plural, =0{номерів} =1{номер} one{# номер} few{# номерів} many{# номерів} other{# номерів}}',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => '{n, plural, =0{номерів} =1{номер} one{# номер} few{# номерів} many{# номерів} other{# номерів}}',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => '{n, plural, =0{rooms} =1{room} one{# rooms} few{# rooms} many{# rooms} other{# rooms}}',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => '{n, plural, =0{гостей} =1{гість} one{# гість} few{# гостя} many{# гостей} other{# гостей}}',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => '{n, plural, =0{гостей} =1{гість} one{# гість} few{# гостя} many{# гостей} other{# гостей}}',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => '{n, plural, =0{guests} =1{guest} one{# guests} few{# guests} many{# guests} other{# guests}}',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Очистити',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Очистити',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Clear',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Корзина успішно очищена!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Корзина успішно очищена!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Cart cleared successfully!',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Успішно додано до корзини!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Успішно додано до корзини!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Added to cart successfully!',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Дякуємо!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Дякуємо!',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Thank you!',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Ваше замовлення отримано. Підтвердження надійде Вам на електронну пошту',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ваше замовлення отримано. Підтвердження надійде Вам на електронну пошту',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Your booking is successful. You\'ll receive an email confirmation',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Оформлення бронювання',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Оформлення бронювання',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Checkout',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Інформація про замовника',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Інформація про замовника',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Customer information',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Прізвище',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Прізвище',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Surname',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Ім\'я',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ім\'я',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Name',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'По батькові',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'По батькові',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Second name',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Дата народження',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Дата народження',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Birth date',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Громадянство',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Громадянство',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Citizenship',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Код країни',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Код країни',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Country code',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Телефон',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Телефон',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Phone',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Електронна пошта',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Електронна пошта',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Email',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Додаткова інформація',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Додаткова інформація',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Additional information',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Виїзд',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Виїзд',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Move out',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Заїзд',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Заїзд',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Move in',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Спосіб оплати',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Спосіб оплати',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Payment method',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Статус оплати',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Статус оплати',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Payment status',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Сума',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Сума',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Sum',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Мова',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Мова',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Language',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Валюта',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Валюта',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Currency',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Код валюти',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Код валюти',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Currency code',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Значення валюти',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Значення валюти',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Currency value',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Кошик',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Кошик',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Cart',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Статус',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Статус',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Status',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Коментар',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Коментар',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Comment',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Фактом бронювання Ви погоджуєтесь з',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Фактом бронювання Ви погоджуєтесь з',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Doing booking you give permissions on',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'обробкою персональних даних та політикою конфіденційності',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'обробкою персональних даних та політикою конфіденційності',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'personal data processing and agree with our privacy policy',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'та',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'та',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'and',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'угодою користувача',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'угодою користувача',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'customer agreement',
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Готівкою',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Готівкою',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'In cash',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Онлайн',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Онлайн',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Online',
        ]);



        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => '№ бронювання',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => '№ бронювання',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => '№ of booking',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Створено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Створено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Created',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Оновлено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Оновлено',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Updated',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'ПІБ',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'ПІБ',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Full name',
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'hotel',
            'message' => 'Контакти',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Контакти',
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Contacts',
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->truncateTable('{{%message}}');
        $this->truncateTable('{{%source_message}}');
    }
}
