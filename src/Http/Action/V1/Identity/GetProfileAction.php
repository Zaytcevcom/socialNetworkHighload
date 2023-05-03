<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Identity;

use App\Http\Action\Unifier\User\UserUnifier;
use App\Modules\Identity\Query\GetById\IdentityGetByIdFetcher;
use App\Modules\Identity\Query\GetById\IdentityGetByIdQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Exception\NotFoundExceptionModule;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataResponse;

#[OA\Get(
    path: '/identity/profile',
    description: 'Получение информации о профиле пользователя',
    summary: 'Получение информации о профиле пользователя',
    security: [Security::BEARER_AUTH],
    tags: ['Identity'],
    responses: [new ResponseSuccessful()]
)]
final class GetProfileAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly IdentityGetByIdFetcher $fetcher,
        private readonly UserUnifier $unifier
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $query = new IdentityGetByIdQuery(
            $identity->id,
            ['country', 'city', 'contacts', 'interests', 'position', 'counters', 'marital', 'career']
        );

        $result = $this->fetcher->fetch($query);

        if (!$result) {
            throw new NotFoundExceptionModule(
                module: 'identity',
                request: $request,
                message: 'error.identity_not_found',
            );
        }

        return new JsonDataResponse(
            $this->unifier->unifyOne($identity->id, $result),
        );
    }
}
