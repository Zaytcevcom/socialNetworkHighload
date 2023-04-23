<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetFeed;

use Symfony\Component\Validator\Constraints as Assert;

final class PostGetFeedQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        public readonly int $count = 100,
        public readonly int $offset = 0,
    ) {
    }
}
