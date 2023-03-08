<?php

declare(strict_types=1);

namespace App\Modules\OAuth\Generator;

use DateTimeImmutable;

final class AccessTokenParams
{
    public function __construct(
        public readonly string $userId,
        public readonly string $role,
        public readonly DateTimeImmutable $expires,
    ) {
    }
}
