<?php

declare(strict_types=1);

namespace App\Http\Action\Unifier;

interface UnifierInterface
{
    public function unifyOne(int $userId, array $item): array;

    public function unify(int $userId, array $items): array;
}
