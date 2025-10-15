<?php

namespace app\commands;

use Yii;
use app\module\admin\models\Module;
use yii\console\Controller;
use yii\console\controllers\MigrateController;

class RunMigrationsController extends Controller
{
    /**
     * This command run main migrations and all migrations of all modules.
     */
    public function actionIndex()
    {
        try {
            // Up main migrations
            $migration = new MigrateController('migrate', Yii::$app);
            $migration->useTablePrefix = true;
            $migration->runAction('up', [
                'interactive' => false,
            ]);

            // Up modules migrations
            $modules = Module::find()->where(['status' => Module::STATUS_ACTIVE])->orderBy('sort_order ASC')->all();

            /** @var Module $module */
            foreach ($modules as $module) {
                $moduleAlias = '@app/module/admin/module/' . $module->name;
                $migration = new MigrateController('migrate', Yii::$app);
                $migration->useTablePrefix = true;
                $migration->runAction('up', [
                    'migrationPath' => $moduleAlias . '/migrations/',
                    'migrationTable' => "{{%migration_$module->name}}",
                    'interactive' => false,
                ]);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
