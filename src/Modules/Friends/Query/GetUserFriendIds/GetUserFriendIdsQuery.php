<?php

declare(strict_types=1);

namespace App\Modules\Friends\Query\GetUserFriendIds;

use Symfony\Component\Validator\Constraints as Assert;

final class GetUserFriendIdsQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
    ) {
    }
}
