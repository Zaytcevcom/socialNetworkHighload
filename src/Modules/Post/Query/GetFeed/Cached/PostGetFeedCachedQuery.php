<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetFeed\Cached;

use Symfony\Component\Validator\Constraints as Assert;

final class PostGetFeedCachedQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        public readonly int $count = 100,
        public readonly ?string $cursor = null,
    ) {
    }
}
