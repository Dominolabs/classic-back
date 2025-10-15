<?php
/**
 * @author Vitaliy Viznyuk <vitaliyviznyuk@gmail.com>
 * @copyright Copyright (c) 2019 Vitaliy Viznyuk
 */

use app\module\admin\models\SourceMessage;
use yii\db\Migration;

class m190225_142819_add_fields_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'do_not_call', 'TINYINT(1) NOT NULL AFTER `token`');
        $this->addColumn('{{%order}}', 'have_a_child', 'TINYINT(1) NOT NULL AFTER `do_not_call`');
        $this->addColumn('{{%order}}', 'have_a_dog', 'TINYINT(1) NOT NULL AFTER `have_a_child`');
        $this->addColumn('{{%order}}', 'have_a_cat', 'TINYINT(1) NOT NULL AFTER `have_a_dog`');

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Ваші побажання до замовлення'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Ваші побажання до замовлення'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Your wishes to order'
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'Не дзвонити в двері, спить дитина'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'Не дзвонити в двері, спить дитина'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'Do not call at the door, baby is sleeping'
        ]);

        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'У мене є маленька дитина'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'У мене є маленька дитина'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'I have a little child'
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'У мене є собака'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'У мене є собака'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'I have a dog'
        ]);


        $this->insert('{{%source_message}}', [
            'category' => 'order',
            'message' => 'У мене є кішка'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 1,
            'translation' => 'У мене є кішка'
        ]);

        $this->insert('{{%message}}', [
            'source_message_id' => SourceMessage::getLastId(),
            'language_id' => 2,
            'translation' => 'I have a cat'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%message}}', [
            'translation' => 'У мене є кішка'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'I have a cat'
        ]);

        $this->delete('{{%source_message}}', [
            'message' => 'У мене є кішка'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'У мене є собака'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'I have a dog'
        ]);

        $this->delete('{{%source_message}}', [
            'message' => 'У мене є собака'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'У мене є маленька дитина'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'I have a little child'
        ]);

        $this->delete('{{%source_message}}', [
            'message' => 'У мене є маленька дитина'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'Не дзвонити в двері, спить дитина'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'Do not call at the door, baby is sleeping'
        ]);

        $this->delete('{{%source_message}}', [
            'message' => 'Не дзвонити в двері, спить дитина'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'Ваші побажання до замовлення'
        ]);

        $this->delete('{{%message}}', [
            'translation' => 'Your wishes to order'
        ]);

        $this->delete('{{%source_message}}', [
            'message' => 'Ваші побажання до замовлення'
        ]);

        $this->dropColumn('{{%order}}', 'have_a_cat');
        $this->dropColumn('{{%order}}', 'have_a_dog');
        $this->dropColumn('{{%order}}', 'have_a_child');
        $this->dropColumn('{{%order}}', 'do_not_call');
    }
}
