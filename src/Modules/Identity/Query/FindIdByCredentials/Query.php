<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\FindIdByCredentials;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $username,
        #[Assert\NotBlank]
        public readonly string $password
    ) {
    }
}
