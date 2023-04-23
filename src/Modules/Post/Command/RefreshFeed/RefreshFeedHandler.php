<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\RefreshFeed;

use App\Components\Cacher\Cacher;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Query\GetFeed\PostGetFeedFetcher;
use App\Modules\Post\Query\GetFeed\PostGetFeedQuery;
use Doctrine\DBAL\Exception;

final class RefreshFeedHandler
{
    public function __construct(
        private readonly PostGetFeedFetcher $postGetFeedFetcher,
        private readonly Cacher $cacher,
    ) {
    }

    /** @throws Exception */
    public function handle(RefreshFeedCommand $command): void
    {
        /** @var array{array{id:int, created_at: int}} $posts */
        $posts = $this->postGetFeedFetcher->fetch(
            new PostGetFeedQuery(
                userId: $command->userId,
                count: 1000,
                offset: 0
            )
        );

        foreach ($posts as $post) {
            $key = PostHelper::getCacheKeyPost($post['id']);

            if (!$this->cacher->get($key)) {
                $this->cacher->set(
                    key: $key,
                    value: json_encode($post),
                    ttl: PostHelper::getCacheTTLPost()
                );
            }

            $this->cacher->zAdd(
                key: PostHelper::getCacheKeyFeed($command->userId),
                score: $post['created_at'],
                value: $post['id']
            );
        }

        $this->cacher->expire(
            key: PostHelper::getCacheKeyFeed($command->userId),
            ttl: PostHelper::getCacheTTLFeed()
        );
    }
}
