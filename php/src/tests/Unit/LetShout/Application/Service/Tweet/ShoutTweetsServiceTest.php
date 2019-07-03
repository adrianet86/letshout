<?php


namespace App\Tests\Unit\LetShout\Application\Service\Tweet;


use App\LetShout\Application\Service\Tweet\EmptyTweetsException;
use App\LetShout\Application\Service\Tweet\InvalidLimitException;
use App\LetShout\Application\Service\Tweet\ShoutTweetsRequest;
use App\LetShout\Application\Service\Tweet\ShoutTweetsService;
use App\LetShout\Domain\Model\Tweet\Tweet;
use App\LetShout\Infrastructure\Persistence\Cache\Tweet\RedisCacheTweetRepository;
use PHPUnit\Framework\TestCase;

class ShoutTweetsServiceTest extends TestCase
{
    public function testWhenLimitIsMinorThanOneItThrowsAnException()
    {
        $service = new ShoutTweetsService(
            $this->createMock(RedisCacheTweetRepository::class)
        );

        $this->expectException(InvalidLimitException::class);
        $service->execute(new ShoutTweetsRequest('username', 0));
    }

    public function testWhenLimitIsMajorThanMaxLimitItThrowsAnException()
    {
        $service = new ShoutTweetsService(
            $this->createMock(RedisCacheTweetRepository::class)
        );

        $this->expectException(InvalidLimitException::class);
        $service->execute(new ShoutTweetsRequest('username', $service::MAX_LIMIT + 1));
    }

    public function testWhenUserHasNotTweetsItThrowsAnException()
    {
        $tweetRepository = $this->createMock(RedisCacheTweetRepository::class);
        $tweetRepository->method('searchByUsername')->willReturn([]);

        $service = new ShoutTweetsService(
            $tweetRepository
        );

        $this->expectException(EmptyTweetsException::class);
        $service->execute(new ShoutTweetsRequest('username', 5, false));
    }

    public function testWhenRequestIsCorrectItReturnsAShoutedTweets()
    {
        $tweetRepository = $this->createMock(RedisCacheTweetRepository::class);
        $text = 'hi';
        $tweets = [
            new Tweet($text)
        ];
        $tweetRepository->method('searchByUsername')->willReturn($tweets);

        $service = new ShoutTweetsService(
            $tweetRepository
        );

        $response = $service->execute(new ShoutTweetsRequest('username', 5, false));

        $this->assertCount(count($tweets), $response->shoutedTweets());
        $this->assertNotEquals($text, $response->shoutedTweets()[0]);
    }

    public function testWhenRequestIsCachedMemcachedRepositoryIsUsed()
    {
        $request = new ShoutTweetsRequest('username', 5, true);

        $cacheTweetRepository = $this->createMock(RedisCacheTweetRepository::class);
        $cacheTweetRepository
            ->expects($this->once())
            ->method('searchByUsername')
            ->with($request->username(), $request->limit())
            ->willReturn([new Tweet('hi')]);

        $service = new ShoutTweetsService(
            $cacheTweetRepository
        );

        $service->execute($request);
    }
    
    public function testWhenCacheIsFalseStoredCacheItIsRemoved()
    {
        $request = new ShoutTweetsRequest('username', 5, false);

        $cacheTweetRepository = $this->createMock(RedisCacheTweetRepository::class);
        $cacheTweetRepository->method('searchByUsername')->willReturn([new Tweet('a')]);
        $cacheTweetRepository
            ->expects($this->once())
            ->method('removeCache')
            ->with($request->username(), $request->limit());

        $service = new ShoutTweetsService(
            $cacheTweetRepository
        );

        $service->execute($request);
    }
}