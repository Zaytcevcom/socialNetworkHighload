<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\RefreshFeed;

use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;
use App\Modules\Post\Query\GetFeed\PostGetFeedFetcher;
use App\Modules\Post\Query\GetFeed\PostGetFeedQuery;
use Doctrine\DBAL\Exception;
use ZayMedia\Shared\Components\Cacher\Cacher;
use ZayMedia\Shared\Components\Queue\Queue;
use ZayMedia\Shared\Helpers\Helper;

final class RefreshFeedHandler
{
    public function __construct(
        private readonly PostGetFeedFetcher $postGetFeedFetcher,
        private readonly Cacher $cacher,
        private readonly Queue $queue,
    ) {
    }

    /** @throws Exception */
    public function handle(RefreshFeedCommand $command): void
    {
        $oldPostIds = $this->getOldPostIds($command->userId);
        $newPosts = [];

        $result = $this->postGetFeedFetcher->fetch(
            new PostGetFeedQuery(
                userId: $command->userId,
                count: 1000,
            )
        );

        /** @var array{id:int, created_at: int} $post */
        foreach ($result->items as $post) {
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

            if (!\in_array($post['id'], $oldPostIds, true)) {
                $newPosts[] = $post;
            }
        }

        $this->cacher->expire(
            key: PostHelper::getCacheKeyFeed($command->userId),
            ttl: PostHelper::getCacheTTLFeed()
        );

        $this->sendToRealtime($command->userId, $newPosts);
    }

    private function getOldPostIds(int $userId): array
    {
        return Helper::toArrayInt(
            $this->cacher->zRevRangeByScore(
                key: PostHelper::getCacheKeyFeed($userId),
                start: time(),
                end: 0,
                offset: 0,
                count: 1000
            )
        );
    }

    /** @param array<int, array> $posts */
    private function sendToRealtime(int $userId, array $posts): void
    {
        foreach ($posts as $post) {
            $this->queue->publish(
                queue: PostHelper::getQueueName(PostQueue::UPDATE_FEED_PREFIX) . $userId,
                message: $post
            );
        }
    }
}
