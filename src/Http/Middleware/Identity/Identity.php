<?php

declare(strict_types=1);

namespace App\Http\Middleware\Identity;

final class Identity
{
    public function __construct(
        public readonly int $id,
        public readonly string $role,
    ) {
    }
}
