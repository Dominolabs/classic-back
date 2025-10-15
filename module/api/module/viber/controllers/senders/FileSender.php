<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\api\module\viber\controllers\helpers\FileHelper;
use app\module\api\module\viber\exceptions\ValidationException;
use app\module\api\module\viber\models\ViberMessage;
use Throwable;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\web\UploadedFile;

class FileSender extends MessageSender
{
    public $type = 'file';
    public $errors = [];
    public $failedToSend = [];
    private $maxFileSize = 50;
    private $forbiddenFormats = [
        'ACTION',
        'APK',
        'APP',
        'BAT',
        'BIN',
        'CMD',
        'COM',
        'COMMAND',
        'CPL',
        'CSH',
        'EXE',
        'GADGET',
        'INF1',
        'INS',
        'INX',
        'IPA',
        'ISU',
        'JOB',
        'JSE',
        'KSH',
        'LNK',
        'MSC',
        'MSI',
        'MSP',
        'MST',
        'OSX',
        'OUT',
        'PAF',
        'PIF',
        'PRG',
        'PS1',
        'REG',
        'RGS',
        'RUN',
        'SCT',
        'SHB',
        'SHS',
        'U3P',
        'VB',
        'VBE',
        'VBS',
        'VBSCRIPT',
        'WORKFLOW',
        'WS',
        'WSF',
    ];

    /**
     * @param $to
     * @param $files
     * @param array $additional
     * @throws ValidationException
     * @throws Throwable
     */
    public function send($to, $files, array $additional = [])
    {
        if (!$this->validateFiles($files)) throw new ValidationException($this->errors);
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $link = FileHelper::getFileLink(new ViberMessage(), $file, $this->errors);
            $message = [
                'type' => $this->type,
                'media' => $link,
                'size' => $file->size,
                'file_name' => $file->baseName . '.' . $file->extension
            ];
            parent::send($to, $message, $additional);
        }
    }

    /**
     * @param $files
     * @return bool
     * @throws InvalidConfigException
     */
    private function validateFiles($files)
    {
        $model = DynamicModel::validateData(compact('files'), [
            ['files', 'file', 'maxFiles' => 10, 'maxSize' => $this->maxFileSize*1024*1024],
        ]);

        if ($model->hasErrors()) {
            $this->errors = $model->getErrors();
            return false;
        } else {
           return true;
        }
    }
}