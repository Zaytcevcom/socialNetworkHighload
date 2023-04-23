<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Action\Unifier\Post\PostUnifier;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataCursorItemsResponse;
use App\Modules\Post\Query\Cached\CachedGetFeed\PostCachedGetFeedFetcher;
use App\Modules\Post\Query\Cached\CachedGetFeed\PostCachedGetFeedQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/posts/feed',
    description: 'Получение списка новостей',
    summary: 'Получение списка новостей',
    security: [['bearerAuth' => '{}']],
    tags: ['Posts']
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
#[OA\Response(
    response: 200,
    description: 'Successful operation'
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
                [
                    'userId' => $identity->id,
                    'startedAt' => time(),
                ]
            ),
            PostCachedGetFeedQuery::class
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        $items = $this->unifier->unify($identity->id, $result->items);

        return new JsonDataCursorItemsResponse(
            items: $items,
            cursor: $result->cursor
        );
    }
}
