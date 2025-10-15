<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RemoveTempImagesController extends Controller
{
    /**
     * This command remove images older than 24 hours from temp folder.
     */
    public function actionIndex()
    {
        $tempDirectory = Yii::getAlias('@app/web/image/temp') . DIRECTORY_SEPARATOR;
        foreach (glob($tempDirectory . '*') as $file) {
            // If file is 24 hours (86400 seconds) old then delete it
            if (time() - filectime($file) > 86400) {
                unlink($file);
            }
        }
    }
}
