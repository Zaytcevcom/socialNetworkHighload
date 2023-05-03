<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Posts;

use App\Modules\Post\Command\Update\PostUpdateCommand;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Components\Serializer\Denormalizer;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataSuccessResponse;

#[OA\Put(
    path: '/posts/{id}',
    description: 'Редактирование записи',
    summary: 'Редактирование записи',
    security: [Security::BEARER_AUTH],
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
