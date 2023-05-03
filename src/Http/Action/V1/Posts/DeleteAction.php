<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Modules\Post\Command\Delete\PostDeleteCommand;
use App\Modules\Post\Command\Delete\PostDeleteHandler;
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
    path: '/posts/{id}',
    description: 'Удаляет пост
    **Коды ошибок**:<br><br>
    **1** - Доступ запрещен<br>',
    summary: 'Удаляет пост',
    security: [Security::BEARER_AUTH],
    tags: ['Posts'],
    responses: [new ResponseSuccessful()]
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
final class DeleteAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly PostDeleteHandler $handler,
        private readonly Validator $validator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $command = new PostDeleteCommand(
            userId: $identity->id,
            postId: Route::getArgumentToInt($request, 'id')
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
