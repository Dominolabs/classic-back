<?php

namespace app\jobs;

use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SendEmailJob extends BaseObject implements JobInterface
{
    public $to;
    public $subject;
    public $from;
    public $compose_html_text;
    public $compose_params;


    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            return Yii::$app
                ->mailer
                ->compose(
                    unserialize(base64_decode($this->compose_html_text)),
                    unserialize(base64_decode($this->compose_params))
                )
                ->setFrom(unserialize(base64_decode($this->from)))
                ->setTo($this->to)
                ->setSubject($this->subject)
                ->send();
        } catch (Throwable $e) {
            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'emails');
        }
    }
}