<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\GetById;

use Symfony\Component\Validator\Constraints as Assert;

final class IdentityGetByIdQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly ?int $id = null,
        public readonly string|array $fields = '',
    ) {
    }
}
