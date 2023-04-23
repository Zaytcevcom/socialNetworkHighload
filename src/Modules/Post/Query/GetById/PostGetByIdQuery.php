<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetById;

use Symfony\Component\Validator\Constraints as Assert;

final class PostGetByIdQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $id
    ) {
    }
}
