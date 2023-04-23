<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\Cached\CachedGetById;

use Symfony\Component\Validator\Constraints as Assert;

final class PostCachedGetByIdQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $id
    ) {
    }
}
