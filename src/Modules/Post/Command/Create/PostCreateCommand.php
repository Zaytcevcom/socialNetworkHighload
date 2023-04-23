<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\Create;

use Symfony\Component\Validator\Constraints as Assert;

final class PostCreateCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        public readonly string $text,
    ) {
    }
}
