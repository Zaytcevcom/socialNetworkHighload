<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\FindIdByCredentials;

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly bool $isActive,
    ) {
    }
}
