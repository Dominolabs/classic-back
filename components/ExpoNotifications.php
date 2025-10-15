<?php

namespace app\components;

class ExpoNotifications
{
    const EXPO_API_URL = 'https://exp.host/--/api/v2/push/send';

    private $ch = null;

    public function __construct()
    {
         $this->ch = curl_init();

        // Set cURL opts
        curl_setopt($this->ch, CURLOPT_URL, self::EXPO_API_URL);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'accept-encoding: gzip, deflate',
            'content-type: application/json',
        ]);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        // Temp
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * @param array $notification
     * @param array|string $recipients
     * @return array|null
     */
    public function notify($notification, $recipients)
    {
        $postData = [];

        if (is_string($recipients)) {
            $recipients = [$recipients];
        }

        foreach ($recipients as $token) {
            $postData[] = $notification + ['to' => $token];
        }

        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($postData));

        return $this->execute();
    }

    /**
     * @return array|null
     */
    private function execute()
    {
        $response = [
            'body' => curl_exec($this->ch),
            'status_code' => curl_getinfo($this->ch, CURLINFO_HTTP_CODE)
        ];

        return isset(json_decode($response['body'], true)['data']) ? json_decode($response['body'], true)['data'] : null;
    }
}
 