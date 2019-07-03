<?php


namespace App\LetShout\Infrastructure\ApiClient\Tweet;


use App\LetShout\Domain\Model\Tweet\Tweet;
use App\LetShout\Domain\Model\Tweet\TweetEmptyException;
use App\LetShout\Domain\Model\Tweet\TweetRepository;
use App\LetShout\Domain\Model\Tweet\UserNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

final class ApiTweetRepository implements TweetRepository
{
    const TIMEOUT = 5;

    private $key;
    private $secret;
    private $client;
    private $token;

    public function __construct(string $key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->client = new Client([
            'base_uri' => 'https://api.twitter.com',
            'timeout' => 2.0,
            'http_errors' => true,
            ''
        ]);
    }

    private function getToken(): string
    {
        if (!$this->token) {
            $basic_credentials = base64_encode($this->key . ':' . $this->secret);
            $response = $this->client->post('oauth2/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . $basic_credentials,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'charset=UTF-8',
                    'connect_timeout' => self::TIMEOUT,
                    'timeout' => self::TIMEOUT
                ],
                'form_params' => ['grant_type' => 'client_credentials']
            ]);

            $data = json_decode($response->getBody()->getContents());
            $this->token = $data->access_token;
        }

        return $this->token;
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
        try {
            $uri = "1.1/statuses/user_timeline.json?screen_name=$username&count=$limit";
            $tweets = [];

            $response = $this->client->get($uri, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getToken(),
                    'connect_timeout' => self::TIMEOUT,
                    'timeout' => self::TIMEOUT
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());
            foreach ($data as $message) {
                $tweets[] = new Tweet($message->text);
            }

            return $tweets;
        } catch (ClientException $exception) {
            if ($exception->getCode() == 404) {
                throw new UserNotFoundException('USER NOT FOUND FOR USERNAME: ' . $username);
            }
            throw $exception;
        }
    }
}