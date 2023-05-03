<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetByIds;

use Doctrine\DBAL\Connection;

use function ZayMedia\Shared\Components\Functions\toArrayString;

final class PostGetByIdsFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function fetch(PostGetByIdsQuery $query): array
    {
        $ids = toArrayString($query->ids);

        if (\count($ids) === 0) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        try {
            return $queryBuilder
                ->select(['*'])
                ->from('posts')
                ->where($queryBuilder->expr()->in('id', $ids))
                ->andWhere($queryBuilder->expr()->isNull('deleted_at'))
                ->setMaxResults(1000)
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (\Exception) {
            return [];
        }
    }
}
