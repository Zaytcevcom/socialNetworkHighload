<?php

declare(strict_types=1);

namespace App\Http\Action\Unifier\Post;

use App\Http\Action\Unifier\UnifierInterface;
use App\Modules\Identity\Query\GetByIds\IdentityGetByIdsFetcher;
use App\Modules\Identity\Query\GetByIds\IdentityGetByIdsQuery;
use App\Modules\Identity\Service\UserSerializer;
use App\Modules\Post\Service\PostSerializer;

final class PostUnifier implements UnifierInterface
{
    public function __construct(
        private readonly IdentityGetByIdsFetcher $identityGetByIdsFetcher,
        private readonly PostSerializer $postSerializer,
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
        $items = $this->postSerializer->serializeItems($items);

        $entityIds = $this->getEntityIds($items);

        return $this->mapUsers($items, $this->getUsers($entityIds['userIds']));
    }

    private function getUsers(array $ids): array
    {
        $result = $this->identityGetByIdsFetcher->fetch(
            new IdentityGetByIdsQuery($ids)
        );

        return $this->userSerializer->serializeItems($result);
    }

    private function mapUsers(array $items, array $users): array
    {
        /** @var array{array{userId:int|null}} $items */
        foreach ($items as $key => $item) {
            $items[$key]['user'] = null;

            if (null !== ($item['userId'] ?? null)) {
                /** @var array{id:int} $user */
                foreach ($users as $user) {
                    if ($item['userId'] === $user['id']) {
                        $items[$key]['user'] = $user;
                        break;
                    }
                }
            }

            if (isset($items[$key]['userId'])) {
                unset($items[$key]['userId']);
            }
        }

        return $items;
    }

    /** @return array{userIds:int[]} */
    private function getEntityIds(array $items): array
    {
        $userIds    = [];

        /** @var array{id:int,userId:int} $item */
        foreach ($items as $item) {
            if (isset($item['userId'])) {
                $userIds[] = $item['userId'];
            }
        }

        return [
            'userIds' => array_unique($userIds),
        ];
    }
}
