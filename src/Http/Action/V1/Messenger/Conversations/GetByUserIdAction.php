<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Conversations;

use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\RestServiceClient;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonResponse;

use function App\Components\env;

#[OA\Get(
    path: '/conversations',
    description: 'Получение списка бесед пользователя',
    summary: 'Получение списка бесед пользователя',
    security: [Security::BEARER_AUTH],
    tags: ['Messenger'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'search',
    description: 'Поисковый запрос',
    in: 'query',
    required: false,
    schema: new OA\Schema(
        type: 'string'
    ),
)]
#[OA\Parameter(
    name: 'sort',
    description: 'Сортировка (0 - по убыванию, 1 - по возрастания)',
    in: 'query',
    required: false,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 0
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
final class GetByUserIdAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly RestServiceClient $restServiceClient,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->restServiceClient->get(
            url: env('SERVICE_MESSENGER_URL') . '/v1/conversations',
            query: $request->getQueryParams(),
            accessToken: Authenticate::getAccessToken($request)
        );

        return new JsonResponse($response);
    }
}
