<?php


namespace App\LetShout\Domain\Model\Tweet;


final class Tweet
{
    private $text;

    /**
     * Tweet constructor.
     * @param string $text
     * @throws TweetEmptyException
     */
    public function __construct(string $text)
    {
        $this->assertTextIsNotEmpty($text);
        $this->text = trim($text);
    }

    /**
     * @param string $text
     * @throws TweetEmptyException
     */
    private function assertTextIsNotEmpty(string $text): void
    {
        if (empty($text)) {
            throw new TweetEmptyException('TEXT CAN NOT BE EMPTY');
        }
        return;
    }

    public function text(): string
    {
        return $this->text;
    }

    public function shout(): string
    {
        return trim(strtoupper($this->text)) . '!';
    }
}
