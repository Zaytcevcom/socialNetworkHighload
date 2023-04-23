<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Friends;

use App\Components\Router\Route;
use App\Components\Validator\Validator;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataSuccessResponse;
use App\Modules\Friends\Command\Remove\FriendshipRemoveCommand;
use App\Modules\Friends\Command\Remove\FriendshipRemoveHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Delete(
    path: '/users/friends/{id}',
    description: 'Удаление пользователя из друзей, отклонение входящей/исходящей заявки в друзья',
    summary: 'Удаление пользователя из друзей, отклонение входящей/исходящей заявки в друзья',
    security: [['bearerAuth' => '{}']],
    tags: ['Friends']
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
#[OA\Response(
    response: '200',
    description: 'Successful operation'
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
