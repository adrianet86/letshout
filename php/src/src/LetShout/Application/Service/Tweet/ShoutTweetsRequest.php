<?php


namespace App\LetShout\Application\Service\Tweet;


class ShoutTweetsRequest
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var bool
     */
    private $cached;

    public function __construct(string $username, int $limit, bool $cached = true)
    {
        $this->username = $username;
        $this->limit = $limit;
        $this->cached = $cached;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        return $this->limit;
    }

    /**
     * @return bool
     */
    public function cached(): bool
    {
        return $this->cached;
    }
}