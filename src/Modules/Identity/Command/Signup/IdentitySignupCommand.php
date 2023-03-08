<?php

declare(strict_types=1);

namespace App\Modules\Identity\Command\Signup;

use App\Components\Validator\Regex;
use Symfony\Component\Validator\Constraints as Assert;

final class IdentitySignupCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $username,
        #[Assert\Regex(pattern: Regex::FIRST_NAME)]
        public readonly string $firstName,
        #[Assert\Regex(pattern: Regex::SECOND_NAME)]
        public readonly string $secondName,
        public readonly ?int $sex,
        #[Assert\NotBlank]
        public readonly string $birthdate,
        #[Assert\NotBlank]
        public readonly string $biography,
        #[Assert\NotBlank]
        public readonly string $city,
        #[Assert\Length(min: 8)]
        #[Assert\NotBlank]
        public readonly string $password,
    ) {
    }
}
