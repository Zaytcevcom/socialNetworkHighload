<?php

declare(strict_types=1);

namespace App\Components\Cacher;

use Redis;

class RedisCacher implements Cacher
{
    private Redis $redis;

    public function __construct(
        string $host,
        int $port,
        string $password
    ) {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
        $this->redis->auth($password);
    }

    public function get(string $key): ?string
    {
        $value = $this->redis->get($key);

        if (!\is_string($value)) {
            return null;
        }

        return $value;
    }

    public function set(string $key, string $value, ?int $ttl = null): bool
    {
        return (bool)$this->redis->set($key, $value, $ttl);
    }

    public function delete(string $key): void
    {
        $this->redis->del($key);
    }

    public function expire(string $key, int $ttl): void
    {
        $this->redis->expire($key, $ttl);
    }

    public function mGet(array $keys): array
    {
        $result = $this->redis->mGet($keys);

        if (!\is_array($result)) {
            return [];
        }

        return $result;
    }

    public function zAdd(string $key, float $score, string|float|int $value): void
    {
        $this->redis->zAdd($key, $score, $value);
    }

    public function zRangeByScore(
        string $key,
        int $start,
        int $end,
        ?int $offset = null,
        ?int $count = null
    ): array {
        $options = [];

        if (null !== $offset && null !== $count) {
            $options = [
                'limit' => [$offset, $count],
            ];
        }

        $result = $this->redis->zRangeByScore(
            key: $key,
            start: (string)$start,
            end: (string)$end,
            options: $options
        );

        if (!\is_array($result)) {
            return [];
        }

        return $result;
    }

    public function zRevRangeByScore(
        string $key,
        int $start,
        int $end,
        ?int $offset = null,
        ?int $count = null
    ): array {
        $options = [];

        if (null !== $offset && null !== $count) {
            $options = [
                'limit' => [$offset, $count],
            ];
        }

        /** @var array|Redis $result */
        $result = $this->redis->zRevRangeByScore(
            key: $key,
            start: (string)$start,
            end: (string)$end,
            options: $options
        );

        if (!\is_array($result)) {
            return [];
        }

        return $result;
    }
}
