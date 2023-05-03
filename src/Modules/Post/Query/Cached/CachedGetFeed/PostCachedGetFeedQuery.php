<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\Cached\CachedGetFeed;

use Symfony\Component\Validator\Constraints as Assert;

final class PostCachedGetFeedQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        public readonly int $count = 100,
        public readonly ?string $cursor = null,
    ) {
    }
}
