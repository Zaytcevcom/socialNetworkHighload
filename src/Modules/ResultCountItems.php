<?php

declare(strict_types=1);

namespace App\Modules;

final class ResultCountItems
{
    public function __construct(
        public readonly int $count,
        public readonly array $items
    ) {
    }
}
