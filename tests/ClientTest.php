<?php

namespace Tests\RecastAI;

use PHPUnit\Framework\TestCase;
use RecastAI\Client;

/**
 * Class ClientTest
 * @package Tests\RecastAI
 */
class ClientTest extends TestCase
{
    /**
     * @return bool|string
     */
    protected static function jsonResponse()
    {
        return file_get_contents(__DIR__ . '/data/Request.json');
    }

    /**
     *
     */
    public function testClientClassWithoutLanguage()
    {
        $token = 'TestToken';
        self::assertInstanceOf('RecastAI\Client', new Client($token, null));
    }

    /**
     *
     */
    public function testClientClassWithoutToken()
    {
        $language = 'en';
        self::assertInstanceOf('RecastAI\Client', new Client(null, $language));
    }

    /**
     *
     */
    public function testClientClassWithoutTokenAndLanguage()
    {
        self::assertInstanceOf('RecastAI\Client', new Client());
    }

    /**
     *
     */
    public function testClientClassWithTokenAndLanguage()
    {
        $token = 'TestToken';
        $language = 'en';

        self::assertInstanceOf('RecastAI\Client', new Client($token, $language));
    }

    /**
     *
     */
    public function testClientClassIfAttributesAreOkay()
    {
        $token = 'TestToken';
        $language = 'en';
        $client = new Client($token, $language);

        $this->assertEquals($client->token, $token);
        $this->assertEquals($client->language, $language);
    }

    /**
     *
     */
    public function testTextRequestIfAllOkay()
    {
        $callResult = self::jsonResponse();
        $res = (Object)[ "body" => ($callResult) ];
        $token = 'TestToken';
        $language = 'en';

        $stub = $this->getMockBuilder('RecastAI\Client')
            ->setConstructorArgs(array($token, $language))
            ->setMethods(['requestPrivate'])
            ->getMock();

        $stub->expects($this->once())
            ->method('requestPrivate')
            ->will($this->returnValue($res));
        $result = json_decode($res->body);
        $response = $stub->textRequest($result->results->source);

        $this->assertEquals('200', $response->status);
    }

    /**
     *
     */
    public function testTextRequestIfNoToken()
    {
        $client = new Client();
        $res = $client->request->analyseText('Hello world');
        $this->assertEquals($res, 'Token is missing');
    }

    /**
     *
     */
    public function testFileRequestIfAllOkay()
    {
        $callResult = self::jsonResponse();

        $file = __DIR__ . '/data/file.wav';

        if (!file_exists($file)) {
             $this->markTestSkipped('Audio test file not found');
        }

        $token = 'TestToken';
        $language = 'en';

        $stub = $this->getMockBuilder('RecastAI\Client')
            ->setConstructorArgs(array($token, $language))
            ->setMethods(['requestFilePrivate'])
            ->getMock();

        $stub->expects($this->once())
            ->method('requestFilePrivate')
            ->will($this->returnValue($callResult));

        $response = $stub->fileRequest($file);
        $this->assertEquals('200', $response->status);
    }

    /**
     *
     */
    public function testFileRequestIfNoToken()
    {
        $client = new Client();

        $res = $client->fileRequest(__DIR__ . '/data/file.wav');
        $this->assertEquals($res, 'Token is missing');
    }
}
