<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\GetByIds;

use Doctrine\DBAL\Connection;

use function ZayMedia\Shared\Components\Functions\toArrayString;

final class IdentityGetByIdsFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function fetch(IdentityGetByIdsQuery $query): array
    {
        $ids = toArrayString($query->ids);

        if (\count($ids) === 0) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        try {
            return $queryBuilder
                ->select(['*'])
                ->from('users')
                ->where($queryBuilder->expr()->in('id', $ids))
                ->setMaxResults(1000)
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (\Exception) {
            return [];
        }
    }
}
