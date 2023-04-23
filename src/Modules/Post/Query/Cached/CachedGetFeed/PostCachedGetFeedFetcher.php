<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\Cached\CachedGetFeed;

use App\Components\Cacher\Cacher;
use App\Components\Queue\Queue;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;
use App\Modules\Post\Query\Cached\CachedGetByIds\PostCachedGetByIdsFetcher;
use App\Modules\Post\Query\Cached\CachedGetByIds\PostCachedGetByIdsQuery;
use App\Modules\ResultCursorItems;

final class PostCachedGetFeedFetcher
{
    public function __construct(
        private readonly Cacher $cacher,
        private readonly PostCachedGetByIdsFetcher $postCachedGetByIdsFetcher,
        private readonly Queue $queue,
    ) {
    }

    public function fetch(PostCachedGetFeedQuery $query): ResultCursorItems
    {
        $postIds = $this->cacher->zRevRangeByScore(
            key: PostHelper::getCacheKeyFeed($query->userId),
            start: $query->startedAt,
            end: 0,
            offset: $query->offset,
            count: $query->count
        );

        if (\count($postIds) === 0) {
            $this->sendToQueueRefreshFeedByUser($query->userId);
            return new ResultCursorItems([], '');
        }

        return $this->postCachedGetByIdsFetcher->fetch(
            new PostCachedGetByIdsQuery(
                ids: $postIds
            )
        );
    }

    private function sendToQueueRefreshFeedByUser(int $userId): void
    {
        $this->queue->send(
            queue: PostHelper::getQueueName(PostQueue::REFRESH_FEED_BY_USER),
            message: ['userId' => $userId]
        );
    }
}
