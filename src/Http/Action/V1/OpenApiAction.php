<?php

declare(strict_types=1);

namespace App\Http\Action\V1;

use App\Http\Response\JsonResponse;
use OpenApi\Attributes as OA;
use OpenApi\Generator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Info(
    version: '1.0',
    title: 'API'
)]
#[OA\Server(
    url: '/v1/'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    name: 'bearerAuth',
    in: 'header',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
#[OA\Tag(
    name: 'Identity',
    description: 'ID'
)]
#[OA\Tag(
    name: 'Users',
    description: 'Пользователи'
)]
final class OpenApiAction implements RequestHandlerInterface
{
    public function handle(Request $request): Response
    {
        $openapi = Generator::scan(['/app/src/Http/Action/V1']);

        return new JsonResponse($openapi);
    }
}
