<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\RefreshFeed;

use Symfony\Component\Validator\Constraints as Assert;

final class RefreshFeedCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
    ) {
    }
}
