<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetByIds;

use Symfony\Component\Validator\Constraints as Assert;

final class PostGetByIdsQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly array $ids
    ) {
    }
}
