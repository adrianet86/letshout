<?php


namespace App\LetShout\Domain\Model\Tweet;


interface TweetRepository
{
    /**
     * @param string $username
     * @param int $limit
     * @return Tweet[]
     * @throws UserNotFoundException
     */
    public function searchByUserName(string $username, int $limit): array;
}
