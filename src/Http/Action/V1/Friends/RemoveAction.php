<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Friends;

use App\Modules\Friends\Command\Remove\FriendshipRemoveCommand;
use App\Modules\Friends\Command\Remove\FriendshipRemoveHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataSuccessResponse;

#[OA\Delete(
    path: '/users/friends/{id}',
    description: 'Удаление пользователя из друзей, отклонение входящей/исходящей заявки в друзья',
    summary: 'Удаление пользователя из друзей, отклонение входящей/исходящей заявки в друзья',
    security: [Security::BEARER_AUTH],
    tags: ['Friends'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'id',
    description: 'Идентификатор пользователя',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
)]
final class RemoveAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly FriendshipRemoveHandler $handler,
        private readonly Validator $validator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $command = new FriendshipRemoveCommand(
            userId: $identity->id,
            friendId: Route::getArgumentToInt($request, 'id')
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
