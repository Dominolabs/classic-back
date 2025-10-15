<?php


namespace app\module\api\module\viber\controllers\handlers\events;


use app\module\api\module\viber\controllers\handlers\traits\NewConversationTrait;
use app\module\api\module\viber\controllers\senders\ViberSender;
use Throwable;
use app\module\api\module\viber\controllers\handlers\ViberUserHandler;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\exceptions\ValidationException;
use app\module\api\module\viber\interfaces\HandlerInterface;
use app\module\api\module\viber\models\ViberMessage;
use app\module\api\module\viber\models\ViberUser;

/**
 * Class ConversationStartedHandler
 * @package app\module\api\module\viber\controllers\handlers\events
 * @property ViberUser $v_u
 */
class ConversationStartedHandler extends EventHandler implements HandlerInterface
{
    use NewConversationTrait;

    /**
     * @param $data
     * @return array|null
     * @throws Throwable
     */
    public function run($data)
    {
        $this->v_u = (new ViberUserHandler($this->chat))->run($data);
        ViberSender::setLanguage($this->v_u);
        $response = array_merge([
            'sender' => Helper::config('main.sender'),
        ], $this->getNewConversationData());
        if (!empty($v = $this->v_u->api_version) && $v >= 4) {
            $response = array_merge($response, $this->getKeyboard());
        }
        $this->saveWelcomeMessage($data['message_token'] ?? null);
        return self::jsonResponse($response, 200);
    }

    /**
     * @return array
     */
    private function getNewConversationData()
    {
        return [
            "type" => "text",
            "text" => $this->getNewConversationMessage(),
            'tracking_data' => 'new_conversation'
        ];
    }

    /**
     * @param $m_token
     * @throws ValidationException
     */
    private function saveWelcomeMessage($m_token)
    {
        $m = ViberMessage::create([
            'viber_chat_id' => $this->chat->viber_chat_id ?? 0,
            'message_token' => $m_token,
            'sender' => json_encode(Helper::config('main.sender')),
            'message_type' => 'sent',
            'message' => json_encode($this->getNewConversationData()),
            'tracking_data' => 'new_conversation',
            'sent_at' => Helper::now()->getTimestamp(),
            'type' => 'text',
        ]);
        if (!empty($this->v_u)) $m->link('viberUsers', $this->v_u);
    }
}