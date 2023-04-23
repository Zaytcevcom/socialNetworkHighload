<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final class PostDeleteCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly int $postId,
    ) {
    }
}
