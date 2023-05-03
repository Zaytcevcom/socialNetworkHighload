<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Http\Action\Unifier\Post\PostUnifier;
use App\Modules\Post\Query\Cached\CachedGetFeed\PostCachedGetFeedFetcher;
use App\Modules\Post\Query\Cached\CachedGetFeed\PostCachedGetFeedQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Serializer\Denormalizer;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\ParameterCount;
use ZayMedia\Shared\Helpers\OpenApi\ParameterCursor;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataCursorItemsResponse;

#[OA\Get(
    path: '/posts/feed',
    description: 'Получение списка новостей',
    summary: 'Получение списка новостей',
    security: [Security::BEARER_AUTH],
    tags: ['Posts'],
    parameters: [new ParameterCursor(), new ParameterCount()],
    responses: [new ResponseSuccessful()]
)]
final class GetFeedAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly PostCachedGetFeedFetcher $fetcher,
        private readonly Validator $validator,
        private readonly PostUnifier $unifier
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $query = $this->denormalizer->denormalizeQuery(
            array_merge(
                $request->getQueryParams(),
                ['userId' => $identity->id]
            ),
            PostCachedGetFeedQuery::class
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataCursorItemsResponse(
            count: $result->count,
            items: $this->unifier->unify($identity->id, $result->items),
            cursor: $result->cursor
        );
    }
}
