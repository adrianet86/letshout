<?php


namespace App\Tests\Unit\LetShout\Domain\Model\Tweet;


use App\LetShout\Domain\Model\Tweet\Tweet;
use App\LetShout\Domain\Model\Tweet\TweetEmptyException;
use PHPUnit\Framework\TestCase;

class TweetTest extends TestCase
{
    public function testWhenTriesNewTweetWithEmptyTextItThrowsAnException()
    {
        $this->expectException(TweetEmptyException::class);
        new Tweet('');
    }

    public function testWhenANewTweetIsCreatedItIsTrimmed()
    {
        $text = ' Hello Maikel Nait ';
        $expectedShoutedText = trim($text);

        $tweet = new Tweet($text);
        $this->assertEquals($expectedShoutedText, $tweet->text());
    }

    public function testWhenATweetIsShoutItHasExpectedFormat()
    {
        $text = ' Hello Maikel Nait ';
        $expectedShoutedText = trim(strtoupper($text)) . '!';

        $tweet = new Tweet($text);
        $this->assertEquals($expectedShoutedText, $tweet->shout());
    }
}