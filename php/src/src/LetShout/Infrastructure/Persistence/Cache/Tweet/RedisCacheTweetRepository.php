<?php


namespace App\LetShout\Infrastructure\Persistence\Cache\Tweet;


use App\LetShout\Domain\Model\Tweet\TweetRepository;
use App\LetShout\Domain\Model\Tweet\UserNotFoundException;

class RedisCacheTweetRepository implements TweetRepository
{
    /**
     * @var TweetRepository
     */
    private $tweetRepository;
    /**
     * @var \Redis
     */
    private $cache;

    public function __construct(
        TweetRepository $tweetRepository,
        string $cacheHost,
        int $cachePort
    )
    {
        $this->tweetRepository = $tweetRepository;
        $this->cache = new \Redis();
        $this->cache->connect($cacheHost, $cachePort);
    }

    /**
     * @param string $username
     * @param int $limit
     * @return array
     * @throws UserNotFoundException
     */
    public function searchByUserName(string $username, int $limit): array
    {
        $key = $this->getKey($username, $limit);

        // Get messages from cache.
        $tweets = $this->cache->get($key);
        if (!empty($tweets)) {

            return $this->unserialize($tweets);
        }

        $tweets = $this->tweetRepository->searchByUserName($username, $limit);

        $this->cache->set($key, $this->serialize($tweets), 36000);

        return $tweets;
    }

    public function removeCache(string $username, int $limit): void
    {
        $this->cache->del($this->getKey($username, $limit));
    }

    private function getKey(string $username, int $limit): string
    {
        return $username . ':' . $limit;
    }

    private function serialize(array $tweets): string
    {
        $serializedArray = [];

        if (!empty($tweets)) {
            foreach ($tweets as $tweet) {
                $serializedArray[] = serialize($tweet);
            }

        }

        return json_encode($serializedArray);
    }

    private function unserialize(string $tweets): array
    {
        $serializedArray = json_decode($tweets, true);
        $tweets = [];
        if (!empty($serializedArray)) {
            foreach ($serializedArray as $item) {
                $tweets[] = unserialize($item);
            }
        }

        return $tweets;
    }
}