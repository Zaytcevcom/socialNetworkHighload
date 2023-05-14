<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Http\Action\Unifier\Post\PostUnifier;
use App\Modules\Post\Query\GetById\Cached\PostGetByIdCachedFetcher;
use App\Modules\Post\Query\GetById\Cached\PostGetByIdCachedQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataResponse;

#[OA\Get(
    path: '/posts/{id}',
    description: 'Получение информации о посте по его идентификатору',
    summary: 'Получение информации о посте по его идентификатору',
    security: [Security::BEARER_AUTH],
    tags: ['Posts'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'id',
    description: 'Идентификатор поста',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 1
)]
final class GetByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly PostGetByIdCachedFetcher $fetcher,
        private readonly PostUnifier $unifier,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::findIdentity($request);

        $query = new PostGetByIdCachedQuery(
            id: Route::getArgumentToInt($request, 'id')
        );

        $this->validator->validate($query);

        $post = $this->fetcher->fetch($query);

        return new JsonDataResponse(
            $this->unifier->unifyOne($identity?->id, $post)
        );
    }
}
