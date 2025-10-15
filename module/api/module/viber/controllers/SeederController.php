<?php


namespace app\module\api\module\viber\controllers;


use app\module\admin\models\Language;
use app\module\api\module\viber\controllers\traits\ResponseTrait;
use app\module\api\module\viber\exceptions\ValidationException;
use app\module\api\module\viber\models\ViberCommand;
use app\module\api\module\viber\models\ViberCommandTranslation;
use Throwable;
use Yii;
use yii\base\Controller;

/**
 * Class SeederController
 * @package app\module\api\module\viber\controllers
 * @property ViberCommand $command
 */
class SeederController extends Controller
{
    use ResponseTrait;

    public $command;

    /**
     * @return array|null
     */
    public function actionCreateCommand()
    {
        try {
            $data = Yii::$app->request->post();
            if (empty($data['command']) || empty($data['translations']))
                throw new ValidationException([], 'Required fields are empty.');
            if (!is_array($data['translations']))
                throw new ValidationException(['translations' => ['This field must be an array.']]);
            $this->command = $this->createCommand($data['command']);

            foreach ($data['translations'] as $languageCode => $translation) {
                $this->createTranslation($languageCode, $translation);
            }

            return self::jsonResponse([
                'status' => 'Success.'
            ]);
        } catch (Throwable $e) {

            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'viber');
            return self::handleException($e);
        }
    }

    /**
     * @param string $commandName
     * @return ViberCommand
     * @throws ValidationException
     */
    private function createCommand(string $commandName)
    {
         return  ViberCommand::create([
            'name' => $commandName
        ]);
    }

    /**
     * @param $lang_code
     * @param $translation
     * @return ViberCommandTranslation
     * @throws ValidationException
     */
    private function createTranslation ($lang_code, $translation)
    {
        return ViberCommandTranslation::create([
            'command_id' => $this->command->command_id,
            'language_id' => Language::getLanguageIdByCode($lang_code),
            'translation' => $translation
        ]);
    }
}