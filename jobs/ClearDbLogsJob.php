<?php

namespace app\jobs;

use app\models\DbLog;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class ClearDbLogsJob extends BaseObject implements JobInterface
{
    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $date = date('Y-m-d H:i:s', time() - 24 * 60 * 60 * 14);
        \Yii::$app
            ->db
            ->createCommand()
            ->delete(DbLog::tableName(), 'created_at IS NULL or created_at < :date', [':date' => $date])
            ->execute();
    }
}