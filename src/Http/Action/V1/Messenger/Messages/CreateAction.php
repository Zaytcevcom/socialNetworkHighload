<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Messages;

use App\Components\RestServiceClient;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataSuccessResponse;

use function App\Components\env;

#[OA\Post(
    path: '/conversations/{id}/messages',
    description: 'Отправка сообщения<br><br>
    **Коды ошибок**:<br>
    **1** - Доступ запрещен<br>',
    summary: 'Отправка сообщения',
    security: [['bearerAuth' => '{}']],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'text',
                    type: 'string',
                    example: 'Новое сообщение!'
                ),
            ]
        )
    ),
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
#[OA\Response(
    response: '200',
    description: 'Successful operation'
)]
final class CreateAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly RestServiceClient $restServiceClient,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $conversationId = Route::getArgumentToInt($request, 'id');

        $this->restServiceClient->post(
            url: env('SERVICE_MESSENGER_URL') . '/v1/conversations/' . $conversationId . '/messages',
            body: (array)$request->getParsedBody(),
            accessToken: Authenticate::getAccessToken($request)
        );

        return new JsonDataSuccessResponse();
    }
}
