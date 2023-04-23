<?php

declare(strict_types=1);

namespace App\Modules\Friends\Query\IsSubscriber;

use Symfony\Component\Validator\Constraints as Assert;

final class FriendsIsSubscriberQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly int $friendId
    ) {
    }
}
