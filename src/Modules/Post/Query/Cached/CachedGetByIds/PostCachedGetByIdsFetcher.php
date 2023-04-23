<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\Cached\CachedGetByIds;

use App\Components\Cacher\Cacher;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Query\GetByIds\PostGetByIdsFetcher;
use App\Modules\Post\Query\GetByIds\PostGetByIdsQuery;
use App\Modules\ResultCursorItems;

final class PostCachedGetByIdsFetcher
{
    public function __construct(
        private readonly Cacher $cacher,
        private readonly PostGetByIdsFetcher $postGetByIdsFetcher,
    ) {
    }

    public function fetch(PostCachedGetByIdsQuery $query): ResultCursorItems
    {
        $result = $this->cacher->mGet(
            array_map(
                fn (int $v): string => PostHelper::getCacheKeyPost($v),
                $query->ids
            )
        );

        /** @var array{array{id: int}} $posts */
        $posts = array_map(
            fn (string $v): array => (array)json_decode($v, true),
            $result
        );

        $notExistsPostIds = $this->getNotExistsPostIds($query->ids, $posts);

        if (\count($notExistsPostIds) !== 0) {
            $posts = array_merge($posts, $this->getFromDataBaseAndSaveToCache($notExistsPostIds));
        }

        return new ResultCursorItems(
            items: $posts,
            cursor: ''
        );
    }

    /** @param array{array{id: int}} $posts */
    private function getNotExistsPostIds(array $ids, array $posts): array
    {
        return array_diff(
            $ids,
            array_map(
                fn (array $post): int => $post['id'],
                $posts
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
        foreach ($result->items as $post) {
            $this->cacher->set(
                key: PostHelper::getCacheKeyPost($post['id']),
                value: json_encode($post),
                ttl: PostHelper::getCacheTTLPost()
            );
        }

        return $result->items;
    }
}
