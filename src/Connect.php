<?php

namespace RecastAI;

/**
 * Class Connect
 * @package RecastAI
 */
class Connect
{
    public $token;
    public $language;

    /**
     * Connect constructor.
     * @param null $token
     * @param null $language
     */
    public function __construct($token = null, $language = null)
    {
        $this->token = $token;
        $this->language = $language;
    }

    /**
     * @param $body
     * @param callable $callback
     */
    public function handleMessage($body, callable $callback)
    {
        if (is_callable($callback)) {
            $callback(new Message($this->token, $body));
        }
    }

    /**
     * @param $messages
     * @param $conversationId
     * @return mixed
     * @throws \Exception
     */
    public function sendMessage($messages, $conversationId)
    {
        $headers = ['Content-Type' => 'application/json', 'Authorization' => "Token " . $this->token];
        $body = json_encode(['messages' => $messages]);

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('POST', str_replace(":conversation_id", $conversationId, Constants::MESSAGE_ENDPOINT), [
                'headers' => $headers,
                'body' => $body
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error: API is not accessible: ' . $e->getMessage());
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param $messages
     * @return mixed
     * @throws \Exception
     */
    public function broadcastMessage($messages)
    {
        $headers = ['Content-Type' => 'application/json', 'Authorization' => "Token " . $this->token];
        $body = json_encode(['messages' => $messages]);

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('POST', Constants::CONVERSATION_ENDPOINT, [
                'headers' => $headers,
                'body' => $body
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error: API is not accessible: ' . $e->getMessage());
        }

        return json_decode($response->getBody()->getContents());
    }
}
