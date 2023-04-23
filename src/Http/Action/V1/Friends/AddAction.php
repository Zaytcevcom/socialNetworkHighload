<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Friends;

use App\Components\Router\Route;
use App\Components\Validator\Validator;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataSuccessResponse;
use App\Modules\Friends\Command\Add\FriendshipAddCommand;
use App\Modules\Friends\Command\Add\FriendshipAddHandler;
use Doctrine\DBAL\Exception;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Post(
    path: '/users/friends/{id}',
    description: 'Добавление пользователя в друзья<br><br>
    **Коды ошибок**:<br>
    **1** - Пользователь не найден<br>
    **2** - Пользователи не должны совпадать<br>
    ',
    summary: 'Добавление пользователя в друзья',
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
final class AddAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly FriendshipAddHandler $handler,
        private readonly Validator $validator
    ) {
    }

    /**
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $command = new FriendshipAddCommand(
            userId: $identity->id,
            friendId: Route::getArgumentToInt($request, 'id')
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
