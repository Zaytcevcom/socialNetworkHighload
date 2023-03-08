<?php

declare(strict_types=1);

namespace App\Modules\Identity\Service;

class UserSerializer
{
    public function serialize(array $user): array
    {
        return [
            'id'            => $user['id'],
            'username'      => $user['username'] ?? '',
            'firstName'     => $user['first_name'] ?? '',
            'secondName'    => $user['second_name'] ?? '',
            'sex'           => $user['sex'] ?? 0,
            'birthdate'     => $user['birthdate'] ?? '',
            'biography'     => $user['biography'] ?? '',
            'city'          => $user['city'] ?? '',
        ];
    }

    public function serializeItems(array $items): array
    {
        $result = [];

        /** @var array $item */
        foreach ($items as $item) {
            $result[] = $this->serialize($item);
        }

        return $result;
    }
}
