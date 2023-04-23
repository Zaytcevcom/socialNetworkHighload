<?php

declare(strict_types=1);

namespace App\Modules\Friends\Command\Add;

use Symfony\Component\Validator\Constraints as Assert;

final class FriendshipAddCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly int $friendId
    ) {
    }
}
