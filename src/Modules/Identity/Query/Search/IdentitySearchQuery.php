<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\Search;

final class IdentitySearchQuery
{
    public function __construct(
        public readonly ?string $search = '',
        public readonly int $count = 100,
        public readonly int $offset = 0,
    ) {
    }
}
