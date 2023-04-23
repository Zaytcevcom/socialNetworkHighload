<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Components\Router\Route;
use App\Components\Validator\Validator;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataSuccessResponse;
use App\Modules\Post\Command\Delete\PostDeleteCommand;
use App\Modules\Post\Command\Delete\PostDeleteHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Delete(
    path: '/posts/{id}',
    description: 'Удаляет пост
    **Коды ошибок**:<br><br>
    **1** - Доступ запрещен<br>',
    summary: 'Удаляет пост',
    security: [['bearerAuth' => '{}']],
    tags: ['Posts']
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
#[OA\Response(
    response: '200',
    description: 'Successful operation'
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
