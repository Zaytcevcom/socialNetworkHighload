<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Components\Router\Route;
use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonDataSuccessResponse;
use App\Modules\Post\Command\Update\PostUpdateCommand;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Put(
    path: '/posts/{id}',
    description: 'Редактирование записи',
    summary: 'Редактирование записи',
    security: [['bearerAuth' => '{}']],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'text',
                    type: 'string',
                    example: null
                ),
            ]
        )
    ),
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
final class UpdateAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $postId = Route::getArgumentToInt($request, 'id');

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'userId' => $identity->id,
                'postId' => $postId,
            ]),
            PostUpdateCommand::class
        );

        $this->validator->validate($command);

        return new JsonDataSuccessResponse();
    }
}
