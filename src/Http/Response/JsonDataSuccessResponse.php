<?php

declare(strict_types=1);

namespace App\Http\Response;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

final class JsonDataSuccessResponse extends Response
{
    public function __construct(int $success = 1, int $status = 201)
    {
        parent::__construct(
            $status,
            new Headers(['Content-Type' => 'application/json']),
            (new StreamFactory())->createStream(json_encode([
                'data' => [
                    'success' => $success,
                ],
            ]))
        );
    }
}
