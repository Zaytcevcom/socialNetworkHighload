<?php

declare(strict_types=1);

namespace App\Modules;

final class ResultCursorItems
{
    public function __construct(
        public readonly array $items,
        public readonly string $cursor,
    ) {
    }
}
