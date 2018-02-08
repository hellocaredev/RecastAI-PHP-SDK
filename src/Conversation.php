<?php

namespace RecastAI;

/**
 * Class Conversation
 * @package RecastAI
 */
class Conversation
{
    public $token;
    public $raw;
    public $uuid;
    public $source;
    public $replies;
    public $action;
    public $nextActions;
    public $memory;
    public $entities;
    public $sentiment;
    public $intents;
    public $conversationToken;
    public $language;
    public $processing_language;
    public $version;
    public $timestamp;
    public $status;

    /**
     * Conversation constructor.
     * @param $token
     * @param $responseBody
     */
    public function __construct($token, $responseBody)
    {
        $this->token = $token;

        $this->raw = $responseBody;
        $this->uuid = $responseBody->uuid;
        $this->source = $responseBody->source;
        $this->replies = $responseBody->replies;
        $this->action = $responseBody->action;
        $this->nextActions = $responseBody->next_actions;
        $this->memory = $responseBody->memory;
        $this->entities = $responseBody->entities;
        $this->sentiment = $responseBody->sentiment;
        $this->intents = $responseBody->intents;
        $this->conversationToken = $responseBody->conversation_token;
        $this->language = $responseBody->language;
        $this->processing_language = $responseBody->processing_language;
        $this->version = $responseBody->version;
        $this->timestamp = $responseBody->timestamp;
        $this->status = $responseBody->status;
    }

    /**
     * Returns the first reply if there is one
     * @return {String}: this first reply or null
     */
    public function reply()
    {
        return (count($this->replies) > 0 ? $this->replies[0] : null);
    }

    /**
     * Returns a concatenation of the replies
     * @return {String}: the concatenation of the replies
     */
    public function replies()
    {
        return ($this->replies);
    }

    /**
     * @param string $separator
     * Returns a concatenation of the replies
     * @return string|null : the concatenation of the replies
     */
    public function joinedReplies($separator = ' ')
    {
        return ($this->replies ? join($separator, $this->replies) : null);
    }

    /**
     * Returns all the action whose name matches the parameter
     * @return array: returns an array of action, or null
     */
    public function action()
    {
        return ($this->action ? $this->action : null);
    }

    /**
     * Returns the first nextActions whose name matches the parameter
     * @return array: returns an array of first nextActions, or null
     */
    public function nextAction()
    {
        return (count($this->nextActions) > 0 ? $this->nextActions[0] : null);
    }

    /**
     * Returns all nextActions
     * @return array: returns an array of nextActions, or []
     */
    public function nextActions()
    {
        return ($this->nextActions ? $this->nextActions : []);
    }

    /**
     * SENTIMENT HELPERS
     * Returns whether or not the response sentiment corresponds to the checked one
     * @return boolean: true or false
     */

    public function isPositive()
    {
        return ($this->sentiment === Constants::SENTIMENT_POSITIVE);
    }

    /**
     * @return bool
     */
    public function isNeutral()
    {
        return ($this->sentiment === Constants::SENTIMENT_NEUTRAL);
    }

    /**
     * @return bool
     */
    public function isNegative()
    {
        return ($this->sentiment === Constants::SENTIMENT_NEGATIVE);
    }

    /**
     * @return bool
     */
    public function isVPositive()
    {
        return ($this->sentiment === Constants::SENTIMENT_VERY_POSITIVE);
    }

    /**
     * @return bool
     */
    public function isVNegative()
    {
        return ($this->sentiment === Constants::SENTIMENT_VERY_NEGATIVE);
    }

    /**
     * @see Conversation::getMemory()
     * @param null $name
     * @return null|object
     */
    public function memory($name = null)
    {
        return $this->getMemory($name);
    }

    /**
     * Returns the memory matching the alias
     * or all the memory if no alias provided
     * @param null $name
     * @return object|null: the memory
     */
    public function getMemory($name = null)
    {
        if ($name === null) {
            return ($this->memory);
        } else if (array_key_exists($name, $this->memory)) {
            return ($this->memory->$name);
        } else {
            return (null);
        }
    }

    /**
     * Merge the conversation memory with the one in parameter
     * Returns the memory updated
     * @param $memory
     * @return {object}: the memory updated
     * @throws \Exception
     */
    public function setMemory($memory)
    {
        $headers = ['Content-Type' => 'application/json', 'Authorization' => "Token " . $this->token];
        $body = json_encode([
            'conversationToken' => $this->conversationToken,
            'memory' => $memory
        ]);

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('PUT', Constants::CONVERSE_ENDPOINT, [
                'headers' => $headers,
                'body' => $body
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error: API is not accessible: ' . $e->getMessage());
        }

        $responseBody = json_decode($response->getBody()->getContents())->results;

        return $responseBody->memory;
    }

    /**
     * Reset the memory of the conversation
     * @param null $alias
     * @return {object}: the updated memory
     * @throws \Exception
     */
    public function resetMemory($alias = null)
    {
        $headers = ['Content-Type' => 'application/json', 'Authorization' => "Token " . $this->token];

        if ($alias === null) {
            $body = json_encode(['conversationToken' => $this->conversationToken]);
        } else {
            $body = json_encode(['conversationToken' => $this->conversationToken, 'memory' => (object)[$alias => null]]);
        }

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('PUT', Constants::CONVERSE_ENDPOINT, [
                'headers' => $headers,
                'body' => $body
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error: API is not accessible: ' . $e->getMessage());
        }

        $responseBody = json_decode($response->getBody()->getContents())->results;

        return $responseBody->memory;
    }

    /**
     * Reset the conversation
     * @return {object}: the updated memory
     * @throws \Exception
     */
    public function resetConversation()
    {
        $headers = ['Content-Type' => 'application/json', 'Authorization' => "Token " . $this->token];

        $body = json_encode(['conversationToken' => $this->conversationToken]);

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('DELETE', Constants::CONVERSE_ENDPOINT, [
                'headers' => $headers,
                'body' => $body
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error: API is not accessible: ' . $e->getMessage());
        }

        $responseBody = json_decode($response->getBody()->getContents())->results;

        return $responseBody->memory;
    }
}
