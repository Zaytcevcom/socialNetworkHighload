<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\Cached\CachedGetById;

use App\Components\Cacher\Cacher;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Query\GetById\PostGetByIdFetcher;
use App\Modules\Post\Query\GetById\PostGetByIdQuery;
use Doctrine\DBAL\Exception;

final class PostCachedGetByIdFetcher
{
    public function __construct(
        private readonly Cacher $cacher,
        private readonly PostGetByIdFetcher $postGetByIdFetcher,
    ) {
    }

    /** @throws Exception */
    public function fetch(PostCachedGetByIdQuery $query): array
    {
        $result = $this->cacher->get(
            key: PostHelper::getCacheKeyPost($query->id)
        );

        if ($result !== null) {
            return (array)json_decode($result, true);
        }

        return $this->getFromDataBaseAndSaveToCache($query->id);
    }

    /** @throws Exception */
    private function getFromDataBaseAndSaveToCache(int $postId): array
    {
        $result = $this->postGetByIdFetcher->fetch(
            new PostGetByIdQuery(
                id: $postId
            )
        );

        $this->cacher->set(
            key: PostHelper::getCacheKeyPost($postId),
            value: json_encode($result),
            ttl: PostHelper::getCacheTTLPost()
        );

        return $result;
    }
}
