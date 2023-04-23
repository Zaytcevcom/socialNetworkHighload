<?php

declare(strict_types=1);

namespace App\Components\Cacher;

interface Cacher
{
    public function get(string $key): ?string;

    public function set(string $key, string $value, ?int $ttl = null): bool;

    public function delete(string $key): void;

    public function expire(string $key, int $ttl): void;

    public function mGet(array $keys): array;

    public function zAdd(string $key, float $score, string|float|int $value): void;

    public function zRangeByScore(
        string $key,
        int $start,
        int $end,
        ?int $offset = null,
        ?int $count = null
    ): array;

    public function zRevRangeByScore(
        string $key,
        int $start,
        int $end,
        ?int $offset = null,
        ?int $count = null
    ): array;
}
