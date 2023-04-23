<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\GetByIds;

use App\Modules\ResultCountItems;
use Doctrine\DBAL\Connection;

use function App\Components\Functions\toArrayString;

final class IdentityGetByIdsFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function fetch(IdentityGetByIdsQuery $query): ResultCountItems
    {
        $ids = toArrayString($query->ids);

        if (\count($ids) === 0) {
            return new ResultCountItems(0, []);
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(['*'])
            ->from('users')
            ->where($queryBuilder->expr()->in('id', $ids));

        $result = $queryBuilder
            ->setMaxResults(1000)
            ->executeQuery();

        $count = $result->rowCount();

        $rows = $result->fetchAllAssociative();

        return new ResultCountItems($count, $rows);
    }
}
