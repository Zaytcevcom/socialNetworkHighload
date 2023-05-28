<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Messages;

use App\Components\RestServiceClient;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonResponse;

use function App\Components\env;

#[OA\Get(
    path: '/conversations/{id}/messages',
    description: 'Получение списка сообщений беседы',
    summary: 'Получение списка сообщений беседы',
    security: [['bearerAuth' => '{}']],
    tags: ['Messenger'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'id',
    description: 'Идентификатор беседы',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 1
)]
#[OA\Parameter(
    name: 'count',
    description: 'Кол-во которое необходимо получить',
    in: 'query',
    required: false,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 100
)]
#[OA\Parameter(
    name: 'offset',
    description: 'Смещение',
    in: 'query',
    required: false,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 0
)]
final class GetByConversationIdAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly RestServiceClient $restServiceClient,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $conversationId = Route::getArgumentToInt($request, 'id');

        $response = $this->restServiceClient->get(
            url: env('SERVICE_MESSENGER_URL') . '/v1/conversations/' . $conversationId . '/messages',
            query: $request->getQueryParams(),
            accessToken: Authenticate::getAccessToken($request)
        );

        return new JsonResponse($response);
    }
}
