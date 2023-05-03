<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Users;

use App\Http\Action\Unifier\User\UserUnifier;
use App\Modules\Identity\Query\Search\IdentitySearchFetcher;
use App\Modules\Identity\Query\Search\IdentitySearchQuery;
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
    path: '/users/search',
    description: 'Глобальный поиск по пользователям',
    summary: 'Глобальный поиск по пользователям',
    security: [Security::BEARER_AUTH],
    tags: ['Users'],
    parameters: [new ParameterCursor(), new ParameterCount()],
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
        $identity = Authenticate::findIdentity($request);

        $query = $this->denormalizer->denormalize($request->getQueryParams(), IdentitySearchQuery::class);

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataCursorItemsResponse(
            count: $result->count,
            items: $this->unifier->unify($identity?->id, $result->items),
            cursor: $result->cursor
        );
    }
}
