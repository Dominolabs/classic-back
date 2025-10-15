<?php
/**
 * Module model class file.
 */

namespace app\module\admin\models;

use Yii;
use yii\console\controllers\MigrateController;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "tbl_module".
 *
 * @property int $module_id
 * @property string $name
 * @property string $title
 * @property string $author
 * @property string $version
 * @property int $status
 * @property int $sort_order
 */
class Module extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_module';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'author', 'version', 'status', 'sort_order'], 'required'],
            [['sort_order'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [['name', 'version'], 'string', 'max' => 32],
            [['title', 'author'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module_id' => 'ID модуля',
            'name' => 'Имя',
            'title' => 'Название',
            'author' => 'Автор',
            'version' => 'Версия',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
        ];
    }

    /**
     * Sets module info based on info.php.
     *
     * @param string $moduleName module name
     */
    public function setModuleInfo($moduleName)
    {
        $this->name = $moduleName;

        $moduleInfoPath = Yii::getAlias('@app/module/admin/module/' . $moduleName . '/info.php');

        if (is_file($moduleInfoPath)) {
            $moduleInfo = require $moduleInfoPath;
        }

        $this->title = !empty($moduleInfo['title']) ? $moduleInfo['title'] : $moduleName;
        $this->author = !empty($moduleInfo['author']) ? $moduleInfo['author'] : '';
        $this->version = !empty($moduleInfo['version']) ? $moduleInfo['version'] : '';
        $this->sort_order = !empty($moduleInfo['sort_order']) ? $moduleInfo['sort_order'] : 1;
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * Returns module info.
     *
     * @param string $moduleName module name
     * @return array|mixed module info
     */
    public static function getModuleInfo($moduleName)
    {
        $moduleInfo = [];
        $moduleInfoPath = Yii::getAlias('@app/module/admin/module/' . $moduleName . '/info.php');

        if (is_file($moduleInfoPath)) {
            $moduleInfo = require $moduleInfoPath;
        }

        return $moduleInfo;
    }

    /**
     * Installs module. This method run all module migrations.
     *
     * @param string $moduleName module name
     * @return bool whether installing was successfully
     */
    public function installModule($moduleName)
    {
        try {
            // Sets module info
            $this->setModuleInfo($moduleName);

            ob_start();
            $moduleAlias = '@app/module/admin/module/' . $this->name;

            // Default console commands outputs to STDOUT so this needs to be declared for wep app
            if (!defined('STDOUT')) {
                define('STDOUT', fopen('/tmp/stdout', 'w'));
            }

            // Up module migrations

            $migration = new MigrateController('migrate', Yii::$app);
            $migration->useTablePrefix = true;
            $migration->runAction('up', [
                'migrationPath' => $moduleAlias . '/migrations/',
                'migrationTable' => "{{%migration_$this->name}}",
                'interactive' => false,
            ]);

            $handle = fopen('/tmp/stdout', 'r');
            $message = '';
            while (($buffer = fgets($handle, 4096)) !== false) {
                $message .= $buffer . "<br>";
            }
            fclose($handle);

            ob_get_clean();

            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Removes module. This method remove all module code and downgrade module migrations.
     *
     * @return bool whether removing was successfully
     */
    public function removeModule()
    {
        try {
            ob_start();

            $moduleAlias = '@app/module/admin/module/' . $this->name;
            $modulePath = Yii::getAlias($moduleAlias);

            // Default console commands outputs to STDOUT so this needs to be declared for wep app
            if (!defined('STDOUT')) {
                define('STDOUT', fopen('/tmp/stdout', 'w'));
            }

            // Down module migrations

            $migration = new MigrateController('migrate', Yii::$app);
            $migration->useTablePrefix = true;
            $migration->runAction('down', [
                'all',
                'migrationPath' => $moduleAlias . '/migrations/',
                'migrationTable' => "{{%migration_$this->name}}",
                'interactive' => false,
            ]);

            $handle = fopen('/tmp/stdout', 'r');
            $message = '';
            while (($buffer = fgets($handle, 4096)) !== false) {
                $message .= $buffer . "<br>";
            }
            fclose($handle);

            // Drop module migration table
            Yii::$app->db->createCommand()->dropTable("{{%migration_$this->name}}")->execute();

            // Remove module files
            if (is_dir($modulePath)) {
                FileHelper::removeDirectory($modulePath);
            }

            ob_get_clean();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Returns statuses list.
     *
     * @return array statuses list data
     */
    public static function getStatusesList()
    {
        return [
            self::STATUS_ACTIVE => 'Включено',
            self::STATUS_NOT_ACTIVE => 'Отключено'
        ];
    }

    /**
     * Returns status name by specified status constant.
     *
     * @param integer $status status constant
     * @return mixed|string status name
     */
    public static function getStatusName($status)
    {
        $statuses = self::getStatusesList();
        return isset($statuses[$status]) ? $statuses[$status] : 'Неопределено';
    }

    /**
     * Checks whether module exists.
     *
     * @param string $moduleName module name
     * @param string $parentModuleName parent module name. Defaults 'admin'
     * @return bool whether module exists
     */
    public static function exists($moduleName, $parentModuleName = 'admin')
    {
        return (Yii::$app->getModule($parentModuleName)->getModule($moduleName) !== null);
    }
}
