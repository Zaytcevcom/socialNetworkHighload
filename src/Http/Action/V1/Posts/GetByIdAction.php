<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Components\Router\Route;
use App\Components\Validator\Validator;
use App\Http\Action\Unifier\Post\PostUnifier;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataResponse;
use App\Modules\Post\Query\Cached\CachedGetById\PostCachedGetByIdFetcher;
use App\Modules\Post\Query\Cached\CachedGetById\PostCachedGetByIdQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/posts/{id}',
    description: 'Получение информации о посте по его идентификатору',
    summary: 'Получение информации о посте по его идентификатору',
    security: [['bearerAuth' => '{}']],
    tags: ['Posts']
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
#[OA\Response(
    response: 200,
    description: 'Successful operation'
)]
final class GetByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly PostCachedGetByIdFetcher $fetcher,
        private readonly PostUnifier $unifier,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::findIdentity($request);

        $query = new PostCachedGetByIdQuery(
            id: Route::getArgumentToInt($request, 'id')
        );

        $this->validator->validate($query);

        $post = $this->fetcher->fetch($query);

        return new JsonDataResponse(
            $this->unifier->unifyOne($identity?->id, $post)
        );
    }
}
