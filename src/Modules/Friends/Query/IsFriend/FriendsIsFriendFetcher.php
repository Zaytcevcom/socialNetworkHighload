<?php

declare(strict_types=1);

namespace App\Modules\Friends\Query\IsFriend;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class FriendsIsFriendFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /**
     * @throws Exception
     */
    public function fetch(FriendsIsFriendQuery $query): bool
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['count(id) as count'])
            ->from('friendships')
            ->where('user_id = :userId')
            ->andWhere('friend_id = :friendId')
            ->setParameter('userId', $query->userId)
            ->setParameter('friendId', $query->friendId)
            ->setFirstResult(0)
            ->fetchAssociative();

        return (bool)($result['count'] ?? 0);
    }
}
