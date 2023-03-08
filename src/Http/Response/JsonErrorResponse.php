<?php

declare(strict_types=1);

namespace App\Http\Response;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

final class JsonErrorResponse extends Response
{
    public function __construct(int $code, string $message, ?array $payload = null, int $status = 409)
    {
        $payload = (null !== $payload) ? ['payload' => $payload] : [];

        parent::__construct(
            $status,
            new Headers(['Content-Type' => 'application/json']),
            (new StreamFactory())->createStream(json_encode(array_merge([
                'error' => [
                    'code' => $code,
                    'message' => $message,
                ],
            ], $payload)))
        );
    }
}
