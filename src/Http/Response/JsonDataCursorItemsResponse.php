<?php

declare(strict_types=1);

namespace App\Http\Response;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

final class JsonDataCursorItemsResponse extends Response
{
    public function __construct(array $items, string $cursor, int $status = 200)
    {
        parent::__construct(
            $status,
            new Headers(['Content-Type' => 'application/json']),
            (new StreamFactory())->createStream(json_encode([
                'data' => [
                    'items'  => $items,
                    'cursor' => $cursor,
                ],
            ]))
        );
    }
}
