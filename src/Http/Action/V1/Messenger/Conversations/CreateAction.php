<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Conversations;

use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\RestServiceClient;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonResponse;

use function App\Components\env;

#[OA\Post(
    path: '/conversations/dialog/{userId}',
    description: 'Создание диалога с пользователем',
    summary: 'Создание диалога с пользователем',
    security: [Security::BEARER_AUTH],
    tags: ['Messenger'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'userId',
    description: 'Идентификатор пользователя',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 1
)]
final class CreateAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly RestServiceClient $restServiceClient,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = Route::getArgumentToInt($request, 'userId');

        $response = $this->restServiceClient->post(
            url: env('SERVICE_MESSENGER_URL') . '/v1/conversations/dialog/' . $userId,
            body: [],
            accessToken: Authenticate::getAccessToken($request)
        );

        return new JsonResponse($response);
    }
}
