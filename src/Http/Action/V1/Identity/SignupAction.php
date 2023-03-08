<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Identity;

use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Http\Response\JsonDataResponse;
use App\Modules\Identity\Command\Signup\IdentitySignupCommand;
use App\Modules\Identity\Command\Signup\IdentitySignupHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Post(
    path: '/identity/signup',
    description: 'Упрощенная регистрация пользователя.<br><br>
        В ответе будут получены:<br>
        - **id** - идентификатор созданного пользователя<br><br>
        **Коды ошибок**<br>
        **1** - Пользователь с данным username уже существует<br>
    ',
    summary: 'Упрощенная регистрация пользователя',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'username',
                    type: 'string',
                    example: 'zaytcevcom'
                ),
                new OA\Property(
                    property: 'firstName',
                    type: 'string',
                    example: 'Konstantin'
                ),
                new OA\Property(
                    property: 'secondName',
                    type: 'string',
                    example: 'Zaytcev'
                ),
                new OA\Property(
                    property: 'sex',
                    type: 'integer',
                    example: 1,
                ),
                new OA\Property(
                    property: 'birthdate',
                    type: 'string',
                    example: '1996-03-18'
                ),
                new OA\Property(
                    property: 'biography',
                    type: 'string',
                    example: 'web-developer'
                ),
                new OA\Property(
                    property: 'city',
                    type: 'string',
                    example: 'Moscow'
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                    example: '1234567890'
                ),
            ]
        )
    ),
    tags: ['Identity']
)]
#[OA\Response(
    response: '200',
    description: 'Successful operation'
)]
final class SignupAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly IdentitySignupHandler $handler,
        private readonly Validator $validator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->denormalizer->denormalize($request->getParsedBody(), IdentitySignupCommand::class);

        $this->validator->validate($command);

        $result = $this->handler->handle($command);

        return new JsonDataResponse(
            data: $result
        );
    }
}
