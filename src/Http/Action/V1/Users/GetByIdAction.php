<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Users;

use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Action\Unifier\User\UserUnifier;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataResponse;
use App\Modules\Identity\Query\GetById\IdentityGetByIdFetcher;
use App\Modules\Identity\Query\GetById\IdentityGetByIdQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Get(
    path: '/users/{id}',
    description: 'Получение информации о пользователе по его идентификатору',
    summary: 'Получение информации о пользователе по его идентификатору',
    security: [['bearerAuth' => '{}']],
    tags: ['Users']
)]
#[OA\Parameter(
    name: 'id',
    description: 'Идентификатор пользователя',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer'
    ),
    example: 1
)]
#[OA\Response(
    response: '200',
    description: 'Successful operation'
)]
final class GetByIdAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly IdentityGetByIdFetcher $fetcher,
        private readonly Validator $validator,
        private readonly UserUnifier $unifier
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::findIdentity($request);

        $query = $this->denormalizer->denormalizeQuery(
            array_merge(
                $request->getQueryParams(),
                ['id' => Route::getArgumentToInt($request, 'id')]
            ),
            IdentityGetByIdQuery::class
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataResponse(
            $this->unifier->unifyOne($identity?->id, $result)
        );
    }
}
