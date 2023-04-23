<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\Cached\CachedGetByIds;

use Symfony\Component\Validator\Constraints as Assert;

final class PostCachedGetByIdsQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly array $ids
    ) {
    }
}
