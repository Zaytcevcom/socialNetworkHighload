<?php

declare(strict_types=1);

namespace App\Modules\Friends\Query\GetUserFriendIds;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class GetUserFriendIdsFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /**
     * @throws Exception
     */
    public function fetch(GetUserFriendIdsQuery $query): array
    {
        $sqlQuery = $this->connection->createQueryBuilder()
            ->select(['f.friend_id'])
            ->from('friendships', 'f')
            ->where('f.user_id = :userId')
            ->setParameter('userId', $query->userId);

        $result = $sqlQuery
            ->executeQuery();

        $items = [];

        /** @var array{friend_id: int} $row */
        foreach ($result->fetchAllAssociative() as $row) {
            $items[] = $row['friend_id'];
        }

        return $items;
    }
}
