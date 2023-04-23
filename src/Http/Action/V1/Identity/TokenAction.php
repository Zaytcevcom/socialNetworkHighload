<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Identity;

use App\Http\Exception\DomainExceptionModule;
use App\Http\Response\JsonDataResponse;
use Exception;
use League\OAuth2\Server\AuthorizationServer;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Post(
    path: '/identity/token',
    description: 'Получение access_token и refresh_token для работы с методами API.<br><br>
        Для обновления access_token через refresh_token,
        необходимо указать **grant_type** = ```refresh_token``` и передать параметры **refresh_token** и **client_id**
    ',
    summary: 'Авторизация',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'grant_type',
                    type: 'string',
                    example: 'password'
                ),
                new OA\Property(
                    property: 'client_id',
                    type: 'string',
                    example: '2'
                ),
                new OA\Property(
                    property: 'username',
                    type: 'string',
                    example: 'demo'
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                    example: '1234567890'
                ),
            ]
        )
    ),
    tags: ['Identity']
)]
#[OA\Response(
    response: '200',
    description: 'Successful operation'
)]
final class TokenAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly AuthorizationServer $server,
        private readonly ResponseFactoryInterface $response,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->server->respondToAccessTokenRequest($request, $this->response->createResponse());

            /** @var array{access_token: string, refresh_token:string} $data */
            $data = json_decode((string)$response->getBody(), true);
        } catch (Exception $exception) {
            throw new DomainExceptionModule(
                module: 'oauth',
                message: $exception->getMessage(),
                code: (int)$exception->getCode()
            );
        }

        return new JsonDataResponse($data);
    }
}
