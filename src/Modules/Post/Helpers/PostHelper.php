<?php

declare(strict_types=1);

namespace App\Modules\Post\Helpers;

use function App\Components\env;

final class PostHelper
{
    public static function getQueueName(PostQueue $queue): string
    {
        return ((env('APP_ENV') !== 'production') ? 'dev-' : '') . $queue->value;
    }

    public static function getCacheKeyPost(int $postId): string
    {
        return 'post:' . $postId;
    }

    public static function getCacheKeyFeed(int $userId): string
    {
        return 'feed:' . $userId;
    }

    public static function getCacheTTLPost(): int
    {
        return 2 * 24 * 3600;
    }

    public static function getCacheTTLFeed(): int
    {
        return 7 * 24 * 3600;
    }
}
