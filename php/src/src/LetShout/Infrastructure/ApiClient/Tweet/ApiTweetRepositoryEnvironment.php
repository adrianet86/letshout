<?php


namespace App\LetShout\Infrastructure\ApiClient\Tweet;


use App\LetShout\Domain\Model\Tweet\TweetEmptyException;
use App\LetShout\Domain\Model\Tweet\TweetRepository;
use App\LetShout\Domain\Model\Tweet\UserNotFoundException;
use App\LetShout\Infrastructure\Persistence\Memory\Tweet\MemoryTweetRepository;

final class ApiTweetRepositoryEnvironment implements TweetRepository
{
    private $environment;
    private $memoryTweetRepository;
    private $apiTweetRepository;

    public function __construct(
        string $environment,
        MemoryTweetRepository $memoryTweetRepository,
        ApiTweetRepository $apiTweetRepository
    )
    {
        $this->environment = $environment;
        $this->memoryTweetRepository = $memoryTweetRepository;
        $this->apiTweetRepository = $apiTweetRepository;
    }

    private function repository(): TweetRepository
    {
        if ($this->environment === 'prod') {
            return $this->apiTweetRepository;
        }

        return $this->memoryTweetRepository;
    }

    /**
     * @param string $username
     * @param int $limit
     * @return array
     * @throws TweetEmptyException
     * @throws UserNotFoundException
     *
     */
    public function searchByUserName(string $username, int $limit): array
    {
        return $this->repository()->searchByUserName($username, $limit);
    }
}