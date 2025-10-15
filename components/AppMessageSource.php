<?php
/**
 * AppMessageSource class file.
 */

namespace app\components;

use app\module\admin\models\Language;

use yii\db\Expression;
use yii\db\Query;
use yii\i18n\DbMessageSource;
use yii\helpers\ArrayHelper;

/**
 * Class AppMessageSource.
 *
 * @package app\components
 */
class AppMessageSource extends DbMessageSource
{
    /**
     * Loads the messages from database.
     * You may override this method to customize the message storage in the database.
     *
     * @param string $category the message category.
     * @param string $language the target language.
     * @return array the messages loaded from database.
     * @throws \yii\db\Exception execution failed
     */
    protected function loadMessagesFromDb($category, $language)
    {
        $languageId = Language::getLanguageIdByCode(substr($language, 0, 2));

        $mainQuery = (new Query())->select(['message' => 't1.message', 'translation' => 't2.translation'])
            ->from(['t1' => $this->sourceMessageTable, 't2' => $this->messageTable])
            ->where([
                't1.source_message_id' => new Expression('[[t2.source_message_id]]'),
                't1.category' => $category,
                't2.language_id' => $languageId,
            ]);

        $fallbackLanguage = substr($language, 0, 2);
        $fallbackSourceLanguage = substr($this->sourceLanguage, 0, 2);

        if ($fallbackLanguage !== $language) {
            $mainQuery->union($this->createFallbackQuery($category, $language, $fallbackLanguage), true);
        } elseif ($language === $fallbackSourceLanguage) {
            $mainQuery->union($this->createFallbackQuery($category, $language, $fallbackSourceLanguage), true);
        }

        $messages = $mainQuery->createCommand($this->db)->queryAll();

        return ArrayHelper::map($messages, 'message', 'translation');
    }

    /**
     * The method builds the [[Query]] object for the fallback language messages search.
     * Normally is called from [[loadMessagesFromDb]].
     *
     * @param string $category the message category
     * @param string $language the originally requested language
     * @param string $fallbackLanguage the target fallback language
     * @return Query
     * @see loadMessagesFromDb
     * @since 2.0.7
     */
    protected function createFallbackQuery($category, $language, $fallbackLanguage)
    {
        $languageId = Language::getLanguageIdByCode(substr($language, 0, 2));
        $fallbackLanguageId = Language::getLanguageIdByCode(substr($language, 0, 2));
        return (new Query())->select(['message' => 't1.message', 'translation' => 't2.translation'])
            ->from(['t1' => $this->sourceMessageTable, 't2' => $this->messageTable])
            ->where([
                't1.source_message_id' => new Expression('[[t2.source_message_id]]'),
                't1.category' => $category,
                't2.language_id' => $fallbackLanguageId,
            ])->andWhere([
                'NOT IN', 't2.source_message_id', (new Query())->select('[[source_message_id]]')->from($this->messageTable)->where(['language_id' => $languageId]),
            ]);
    }
}
