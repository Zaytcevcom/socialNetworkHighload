<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Users;

use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Action\Unifier\User\UserUnifier;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataItemsResponse;
use App\Modules\Identity\Query\Search\IdentitySearchFetcher;
use App\Modules\Identity\Query\Search\IdentitySearchQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/users/search',
    description: 'Глобальный поиск по пользователям',
    summary: 'Глобальный поиск по пользователям',
    security: [['bearerAuth' => '{}']],
    tags: ['Users']
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
    response: '200',
    description: 'Successful operation'
)]
final class SearchAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly IdentitySearchFetcher $fetcher,
        private readonly Validator $validator,
        private readonly UserUnifier $unifier
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $query = $this->denormalizer->denormalize($request->getQueryParams(), IdentitySearchQuery::class);

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataItemsResponse(
            count: $result->count,
            items: $this->unifier->unify($identity->id, $result->items)
        );
    }
}
