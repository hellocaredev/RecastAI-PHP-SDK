<?php

namespace RecastAI;

/**
 * Class Client
 * @package RecastAI
 */
class Client
{
    public $token;
    public $language;
    public $request;
    public $connect;

    /**
     * Client constructor.
     * @param null $token
     * @param null $language
     */
    public function __construct($token = null, $language = null)
    {
        $this->token = $token;
        $this->language = $language;

        $this->request = new Request($token, $language);
        $this->connect = new Connect($token, $language);
    }

    /**
     * Request static function
     */
    public static function Request($token = null, $language = null)
    {
        return new Request($token, $language);
    }

    /**
     * Connect static function
     */
    public static function Connect($token = null, $language = null)
    {
        return new Connect($token, $language);
    }
}
