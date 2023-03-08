<?php

declare(strict_types=1);

namespace App\Modules\OAuth\Command\ClearExpiredItems;

final class Command
{
    public function __construct(
        public readonly string $date
    ) {
    }
}
