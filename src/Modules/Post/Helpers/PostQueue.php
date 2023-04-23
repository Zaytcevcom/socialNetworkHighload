<?php

declare(strict_types=1);

namespace App\Modules\Post\Helpers;

enum PostQueue: string
{
    case REFRESH_FEED_BY_USER = 'refresh-feed-by-user';
    case REFRESH_FEED_BY_POST = 'refresh-feed-by-post';
}
