<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetById\Cached;

use Symfony\Component\Validator\Constraints as Assert;

final class PostGetByIdCachedQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $id
    ) {
    }
}
