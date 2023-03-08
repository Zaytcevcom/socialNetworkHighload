<?php

declare(strict_types=1);

namespace App\Http\Action\Unifier\User;

use App\Http\Action\Unifier\UnifierInterface;
use App\Modules\Identity\Service\UserSerializer;

final class UserUnifier implements UnifierInterface
{
    public function __construct(
        private readonly UserSerializer $userSerializer,
    ) {
    }

    public function unifyOne(?int $userId, ?array $item): array
    {
        /** @var array{array} */
        $result = $this->unify($userId, (null !== $item) ? [$item] : []);
        return (isset($result[0])) ? $result[0] : [];
    }

    public function unify(?int $userId, array $items): array
    {
        return $this->userSerializer->serializeItems($items);
    }
}
