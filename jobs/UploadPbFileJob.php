<?php

namespace app\jobs;

use app\models\DbLog;
use app\module\admin\module\product\models\IngredientDescription;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\product\models\ProductDescription;
use app\module\admin\module\product\models\UploadPbFile as Model;
use Exception;
use Throwable;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class UploadPbFileJob extends BaseObject implements JobInterface
{
    public $id;
    public $modelClass;

    protected function log($msg)
    {
        if ($msg instanceof Throwable) {
            $msg = $msg->getMessage();
        }
        DbLog::add([
            'category' => 'UploadPbFileJob',
            'msg' => $msg,
            'trace' => (new Exception)->getTraceAsString()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            if (empty($this->id) || empty($this->modelClass)) {
                throw new Exception('Empty id');
            }
            /** @var Model $model */
            $model = Model::findOne(['id' => $this->id]);
            if (empty($model->file) || !file_exists($model->file)) {
                throw new Exception('Файл не найден');
            }
            $file = fopen($model->file, 'r+');
            $idColumn = $nameColumn = null;
            $data = fgetcsv($file);
            foreach ($data as $key => $item) {
                $item = trim(mb_strtolower($item ?? ''));
                if ($item === 'id') {
                    $idColumn = $key;
                } elseif ($item === 'name') {
                    $nameColumn = $key;
                }
            }
            if (is_null($idColumn)) {
                throw new Exception('Колонка id не найдена');
            }
            if (is_null($nameColumn)) {
                throw new Exception('Колонка name не найдена');
            }
            $counter = 0;
            while ($data = fgetcsv($file)) {
                $name = $data[$nameColumn] ?? null;
                $pbId = $data[$idColumn] ?? null;
                if (empty($name) || empty($pbId)) {
                    continue;
                }
                if ($this->modelClass === Product::class) {
                    $desc = ProductDescription::findOne(['name' => $name]);
                    $column = 'product_id';
                } else {
                    $desc = IngredientDescription::findOne(['name' => $name]);
                    $column = 'ingredient_id';
                }
                if (!$desc) {
                    continue;
                }
                $counter++;
                $sql = "UPDATE " . $this->modelClass::tableName() . ' set pb_id = :pbId WHERE ' . $column . ' = :id';
                \Yii::$app->db->createCommand($sql, [
                    ':pbId' => $pbId,
                    ':id' => $this->modelClass === Product::class ? $desc->product_id : $desc->ingredient_id
                ])->execute();
            }

            $model->status = Model::STATUS_SUCCESS;
            $model->message = "Данные загружены. Обновлено $counter записей";
            $model->save();
            @unlink($model->file);
        } catch (Throwable $e) {
            $this->log($e);
            if (!empty($model)) {
                $model->status = Model::STATUS_ERROR;
                $model->message = $e->getMessage();
                $model->save();
            }
        }
    }
}