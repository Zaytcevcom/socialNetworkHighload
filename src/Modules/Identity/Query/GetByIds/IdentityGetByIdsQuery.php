<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\GetByIds;

use Symfony\Component\Validator\Constraints as Assert;

final class IdentityGetByIdsQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly array $ids,
    ) {
    }
}
