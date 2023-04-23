<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\Update;

use Symfony\Component\Validator\Constraints as Assert;

final class PostUpdateCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly int $postId,
        #[Assert\NotBlank]
        public readonly string $text,
    ) {
    }
}
