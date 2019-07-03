<?php


namespace App\LetShout\Application\Service\Tweet;


use App\LetShout\Domain\Model\Tweet\Tweet;
use App\LetShout\Domain\Model\Tweet\TweetRepository;
use App\LetShout\Domain\Model\Tweet\UserNotFoundException;
use App\LetShout\Infrastructure\Persistence\Cache\Tweet\RedisCacheTweetRepository;

class ShoutTweetsService
{
    const MAX_LIMIT = 10;

    /**
     * @var TweetRepository
     */
    private $tweetRepository;
    /**
     * @var RedisCacheTweetRepository
     */
    private $cacheTweetRepository;

    public function __construct(RedisCacheTweetRepository $cacheTweetRepository)
    {
        $this->cacheTweetRepository = $cacheTweetRepository;
    }

    /**
     * @param ShoutTweetsRequest $request
     * @return ShoutTweetsResponse
     * @throws InvalidLimitException
     * @throws UserNotFoundException
     * @throws EmptyTweetsException
     */
    public function execute($request): ShoutTweetsResponse
    {
        $this->assertLimit($request->limit());

        if (!$request->cached()) {
            $this->cacheTweetRepository->removeCache($request->username(), $request->limit());
        }
        $tweets = $this->cacheTweetRepository->searchByUserName(
            $request->username(),
            $request->limit()
        );

        if (empty($tweets)) {
            throw new EmptyTweetsException('USER HAS NOT TWEETS');
        }

        $shoutedTweets = [];

        /** @var Tweet $tweet */
        foreach ($tweets as $tweet) {
            $shoutedTweets[] = $tweet->shout();
        }

        return new ShoutTweetsResponse($shoutedTweets);
    }

    private function assertLimit(int $limit): void
    {
        if ($limit > self::MAX_LIMIT || $limit <= 0) {
            throw new InvalidLimitException(
                'LIMIT MUST BE BETWEEN 1 AND '
                . self::MAX_LIMIT . ': '
                . $limit
            );
        }
    }
}