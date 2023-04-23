<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetFeed;

use App\Modules\Friends\Query\GetUserFriendIds\GetUserFriendIdsFetcher;
use App\Modules\Friends\Query\GetUserFriendIds\GetUserFriendIdsQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class PostGetFeedFetcher
{
    public function __construct(
        private readonly Connection $connection,
        private readonly GetUserFriendIdsFetcher $getUserFriendIdsFetcher,
    ) {
    }

    /** @throws Exception */
    public function fetch(PostGetFeedQuery $query): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $sqlQuery = $queryBuilder
            ->select('p.*')
            ->from('posts', 'p')
            ->andWhere('p.deleted_at IS NULL')
            ->andWhere(
                $queryBuilder->expr()->in('user_id', $this->getFriendIds($query->userId))
            );

        $result = $sqlQuery
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults($query->count)
            ->setFirstResult($query->offset)
            ->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * @return string[]
     * @throws Exception
     */
    private function getFriendIds(int $userId): array
    {
        $friendIds = $this->getUserFriendIdsFetcher->fetch(
            new GetUserFriendIdsQuery($userId)
        );

        return array_map(fn (int $v) => (string)$v, $friendIds);
    }
}
