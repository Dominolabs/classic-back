<?php
/**
 * ModuleUploadForm model class file.
 */

namespace app\module\admin\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use ZipArchive;

/**
 * ModuleUploadForm is the model behind the module upload form.
 */
class ModuleUploadForm extends Model
{
    /** @var UploadedFile */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => 'zip'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => 'Установка модуля',
        ];
    }

    /**
     * Uploads module file and install module.
     *
     * @return bool whether upload was successfully
     */
    public function upload()
    {
        try {
            if ($this->validate()) {
                $moduleFilePath = Yii::getAlias('@app/module/admin/module/' . $this->file->baseName . '.' . $this->file->extension);

                if ($this->file->saveAs($moduleFilePath)) {
                    $moduleZipArchive = new ZipArchive;

                    $moduleDirectory = pathinfo(realpath($moduleFilePath), PATHINFO_DIRNAME);

                    if ($moduleZipArchive->open($moduleFilePath)) {
                        if ($moduleZipArchive->extractTo($moduleDirectory)) {

                            $moduleName = trim($moduleZipArchive->getNameIndex(0), '/');

                            $moduleZipArchive->close();

                            // Remove module file
                            if (is_file($moduleFilePath)) {
                                unlink($moduleFilePath);
                            }

                            // Remove MacOS temp directory
                            $macOSXTempDirectory = realpath($moduleDirectory . '/__MACOSX');
                            if (is_dir($macOSXTempDirectory)) {
                                FileHelper::removeDirectory($macOSXTempDirectory);
                            }

                            // Create and install module
                            $module = new Module();

                            $module->installModule($moduleName);

                            if ($module->save(false)) {
                                return true;
                            }
                        }
                        $moduleZipArchive->close();
                    }
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
