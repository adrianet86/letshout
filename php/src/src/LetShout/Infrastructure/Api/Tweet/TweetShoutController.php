<?php


namespace App\LetShout\Infrastructure\Api\Tweet;


use App\LetShout\Application\Service\Tweet\EmptyTweetsException;
use App\LetShout\Application\Service\Tweet\InvalidLimitException;
use App\LetShout\Application\Service\Tweet\ShoutTweetsRequest;
use App\LetShout\Application\Service\Tweet\ShoutTweetsService;
use App\LetShout\Domain\Model\Tweet\UserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TweetShoutController extends AbstractController
{
    /**
     * @var ShoutTweetsService
     */
    private $shoutTweetsService;

    public function __construct(ShoutTweetsService $shoutTweetsService)
    {
        $this->shoutTweetsService = $shoutTweetsService;
    }

    public function index(Request $request, string $username)
    {
        try {
            $httpCode = Response::HTTP_OK;
            $cache = (bool)$request->get('cache', 1);
            $request = new ShoutTweetsRequest(
                $username,
                (int)$request->get('limit', $this->shoutTweetsService::MAX_LIMIT),
                $cache
            );
            $shoutResponse = $this->shoutTweetsService->execute($request);
            $response = $shoutResponse->shoutedTweets();
        } catch (EmptyTweetsException $exception) {
            $response = [];
            $httpCode = Response::HTTP_NO_CONTENT;
        } catch (UserNotFoundException $exception) {
            $response = $exception->getMessage();
            $httpCode = Response::HTTP_NOT_FOUND;
        } catch (InvalidLimitException $exception) {
            $response = $exception->getMessage();
            $httpCode = Response::HTTP_BAD_REQUEST;
        } catch (\Exception $exception) {
            $response = $exception->getMessage();
            $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return JsonResponse::create($response, $httpCode);
    }
}