<?php


namespace App\Tests\Integration\LetShout\Api;


use App\LetShout\Application\Service\Tweet\ShoutTweetsService;
use App\LetShout\Infrastructure\Persistence\Memory\Tweet\MemoryTweetRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TweetShoutTest extends WebTestCase
{
    public function testWhenTwitterUserNotFoundItReturnsNotFoundCode()
    {
        $client = static::createClient();

        $client->request('GET', '/shout/' . MemoryTweetRepository::NOT_FOUND_USERNAME);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testWhenInvalidLimitIsSendItReturnsABadRequestCode()
    {
        $client = static::createClient();
        $limit = ShoutTweetsService::MAX_LIMIT + 1;
        $client->request('GET', '/shout/twittername?limit=' . $limit);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testWhenAValidLimitIsSetItReturnsOk()
    {
        $client = static::createClient();
        $client->request('GET', '/shout/twittername');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testAValidResponseHasAJsonStructure()
    {
        $client = static::createClient();
        $client->request('GET', '/shout/twittername');

        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
    }

    public function testWhenAValidLimitIsSetItReturnsSameNumberOfMessages()
    {
        $client = static::createClient();
        $limit = 2;
        $client->request('GET', '/shout/twittername?limit=' . $limit);

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals($limit, count($content));
    }

    public function testWhenNoLimitIsSendItReturnsDefaultLimit()
    {
        $client = static::createClient();
        $client->request('GET', '/shout/twittername');

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(ShoutTweetsService::MAX_LIMIT, count($content));
    }

    public function testCache()
    {
        $client = static::createClient();
        $client->request('GET', '/shout/twittername', [], [], [
            'Cache-control' => 'no-cache'
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(ShoutTweetsService::MAX_LIMIT, count($content));
    }
}