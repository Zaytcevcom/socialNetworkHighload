<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetByIds\Cached;

use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Query\GetByIds\PostGetByIdsFetcher;
use App\Modules\Post\Query\GetByIds\PostGetByIdsQuery;
use ZayMedia\Shared\Components\Cacher\Cacher;

final class PostGetByIdsCachedFetcher
{
    public function __construct(
        private readonly Cacher $cacher,
        private readonly PostGetByIdsFetcher $postGetByIdsFetcher,
    ) {
    }

    public function fetch(PostGetByIdsCachedQuery $query): array
    {
        $result = $this->cacher->mGet(
            array_map(
                fn (int $v): string => PostHelper::getCacheKeyPost($v),
                $query->ids
            )
        );

        /** @var array{array{id: int}} $items */
        $items = array_map(
            fn (string $v): array => (array)json_decode($v, true),
            $result
        );

        $notExistsIds = $this->getNotExistsIds($query->ids, $items);

        if (\count($notExistsIds) !== 0) {
            $items = array_merge($items, $this->getFromDataBaseAndSaveToCache($notExistsIds));
        }

        return $items;
    }

    /** @param array{array{id: int}} $items */
    private function getNotExistsIds(array $ids, array $items): array
    {
        return array_diff(
            $ids,
            array_map(
                fn (array $item): int => $item['id'],
                $items
            )
        );
    }

    private function getFromDataBaseAndSaveToCache(array $ids): array
    {
        $result = $this->postGetByIdsFetcher->fetch(
            new PostGetByIdsQuery(
                ids: $ids
            )
        );

        /** @var array{id: int} $post */
        foreach ($result as $post) {
            $this->cacher->set(
                key: PostHelper::getCacheKeyPost($post['id']),
                value: json_encode($post),
                ttl: PostHelper::getCacheTTLPost()
            );
        }

        return $result;
    }
}
