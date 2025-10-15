<?php


namespace app\module\api\module\viber\controllers;

use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\controllers\senders\ViberSender;
use Exception;
use Throwable;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\traits\ResponseTrait;
use app\module\api\module\viber\exceptions\NotFoundException;
use app\module\api\module\viber\interfaces\HandlerInterface;
use app\module\api\module\viber\models\ViberChat;
use Yii;
use yii\base\Controller;
use yii\web\UploadedFile;

/**
 * Class ViberController
 * @package app\controllers
 * @property ViberChat $chat
 */
class ViberController extends Controller
{
    use ResponseTrait;

    protected $chat;
    public static $handlers;


    /**
     * ViberController constructor.
     * @param $id
     * @param $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
    {
        self::$log_category = 'viber';
        self::$handlers = Helper::config('handlers.events');
        parent::__construct($id, $module, $config);
    }

    /**
     * @return array
     */
    public function actionViber()
    {
        try {
            $data = Yii::$app->request->post();

            $this->chat = ViberChat::getChat();
            if (!$this->chat) throw new NotFoundException(T::t('validation', 'Chat is not found.'));

            if (array_key_exists('event', $data)
                && $this->chat
                && array_key_exists($data['event'], self::$handlers)
                && ($handler =
                    new self::$handlers[$data['event']] ($this->chat)) instanceof HandlerInterface) {
                /** @var HandlerInterface $handler */
                // Every event that was sent from viber has its own Handler.
                // Handlers list is placed in config/handlers.php. Each Handler has 'run' method, which accept data array
                // form viber and handles it. Most of Handlers must work with some viber bot, so we send it in
                // Handler's construct method.
                return $handler->run($data);
            }

            return self::jsonResponse([
                'status' => 200,
                'message' => 'No actions have been done.'
            ], 200);
        } catch (Throwable $e) {
            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'viber');
//            dd($e);
            return self::apiErrorResponse($e, true);
        }
    }

    /**
     * @return array|void
     */
    public function actionSend()
    {
        return $this->send('send');
    }

    /**
     * @return array|null
     */
    public function actionBroadcast()
    {
        return $this->send('broadcast');
    }

    /**
     * @param $method
     * @return array|null
     */
    private function send($method)
    {
        try {
            if (!in_array($method, ['send', 'broadcast'])) throw new Exception('Unknown method ' . $method . '.');
            $files = empty($file = UploadedFile::getInstancesByName('file')) ? [] : ['file' => $file];
            $pictures = empty($picture = UploadedFile::getInstancesByName('picture')) ? [] : ['picture' => $picture];
            $data = array_merge(Yii::$app->request->post(), $files, $pictures);
            $this->chat = ViberChat::getChat();
            if (!$this->chat) throw new NotFoundException();
            $method .= 'Message';
            return ViberSender::$method($data, $this->chat);
        } catch (Throwable $e) {

            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'viber');
            return self::handleException($e);
        }
    }

}