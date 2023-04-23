<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Action\Unifier\Post\PostUnifier;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataResponse;
use App\Modules\Post\Command\Create\PostCreateCommand;
use App\Modules\Post\Command\Create\PostCreateHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Post(
    path: '/posts',
    description: 'Создание поста<br><br>
    **Коды ошибок**:<br>
    **1** - Доступ запрещен<br>',
    summary: 'Создание поста',
    security: [['bearerAuth' => '{}']],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'text',
                    type: 'string',
                    example: 'Новый пост!'
                ),
            ]
        )
    ),
    tags: ['Posts']
)]
#[OA\Response(
    response: '200',
    description: 'Successful operation'
)]
final class CreateAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly PostCreateHandler $handler,
        private readonly Validator $validator,
        private readonly PostUnifier $unifier
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'userId' => $identity->id,
            ]),
            PostCreateCommand::class
        );

        $this->validator->validate($command);

        $post = $this->handler->handle($command);

        return new JsonDataResponse($this->unifier->unifyOne($identity->id, $post->toArray()));
    }
}
