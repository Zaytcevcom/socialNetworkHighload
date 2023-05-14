<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetFeed\Cached;

use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;
use App\Modules\Post\Query\GetByIds\Cached\PostGetByIdsCachedFetcher;
use App\Modules\Post\Query\GetByIds\Cached\PostGetByIdsCachedQuery;
use ZayMedia\Shared\Components\Cacher\Cacher;
use ZayMedia\Shared\Components\Queue\Queue;
use ZayMedia\Shared\Helpers\CursorPagination\CursorPagination;
use ZayMedia\Shared\Helpers\CursorPagination\CursorPaginationResult;

final class PostGetFeedCachedFetcher
{
    public function __construct(
        private readonly Cacher $cacher,
        private readonly PostGetByIdsCachedFetcher $postGetByIdsCachedFetcher,
        private readonly Queue $queue,
    ) {
    }

    public function fetch(PostGetFeedCachedQuery $query): CursorPaginationResult
    {
        $cursorScore = CursorPagination::decodeScore($query->cursor);

        $start = (null === $cursorScore) ? time() : $cursorScore->start;
        $offset = (null === $cursorScore) ? 0 : $cursorScore->offset;

        $postIds = $this->cacher->zRevRangeByScore(
            key: PostHelper::getCacheKeyFeed($query->userId),
            start: $start,
            end: 0,
            offset: $offset,
            count: $query->count
        );

        if (\count($postIds) === 0) {
            $this->sendToQueueRefreshFeedByUser($query->userId);
            return CursorPagination::generateEmptyResult();
        }

        $items = $this->postGetByIdsCachedFetcher->fetch(
            new PostGetByIdsCachedQuery(
                ids: $postIds
            )
        );

        $offset += \count($items);

        return new CursorPaginationResult(
            count: 0, // todo
            items: $items,
            cursor: CursorPagination::encodeScore($start, $offset)
        );
    }

    private function sendToQueueRefreshFeedByUser(int $userId): void
    {
        $this->queue->publish(
            queue: PostHelper::getQueueName(PostQueue::REFRESH_FEED_BY_USER),
            message: ['userId' => $userId]
        );
    }
}
