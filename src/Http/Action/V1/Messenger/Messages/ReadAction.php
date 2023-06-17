<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Messages;

use App\Components\RestServiceClient;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataSuccessResponse;

use function App\Components\env;

#[OA\Post(
    path: '/conversations/{id}/messages/{messageId}/read',
    description: 'Пометить сообщение как прочитанное',
    summary: 'Пометить сообщение как прочитанное',
    security: [Security::BEARER_AUTH],
    tags: ['Messenger']
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
    name: 'messageId',
    description: 'Идентификатор сообщения',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 1
)]
#[OA\Response(
    response: '200',
    description: 'Successful operation'
)]
final class ReadAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly RestServiceClient $restServiceClient,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $conversationId = Route::getArgumentToInt($request, 'id');
        $messageId = Route::getArgumentToInt($request, 'messageId');

        $this->restServiceClient->post(
            url: env('SERVICE_MESSENGER_URL') . '/v1/conversations/' . $conversationId . '/messages/' . $messageId . '/read',
            body: (array)$request->getParsedBody(),
            accessToken: Authenticate::getAccessToken($request)
        );

        return new JsonDataSuccessResponse();
    }
}
