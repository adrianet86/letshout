<?php


namespace App\LetShout\Application\Service\Tweet;


class ShoutTweetsResponse
{
    /**
     * @var array
     */
    private $shoutedTweets;

    public function __construct(array $shoutedTweets)
    {
        $this->shoutedTweets = $shoutedTweets;
    }

    /**
     * @return array
     */
    public function shoutedTweets(): array
    {
        return $this->shoutedTweets;
    }
}