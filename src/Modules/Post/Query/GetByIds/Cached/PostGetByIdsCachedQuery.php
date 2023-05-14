<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetByIds\Cached;

use Symfony\Component\Validator\Constraints as Assert;

final class PostGetByIdsCachedQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly array $ids
    ) {
    }
}
