<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetByIds;

use App\Modules\ResultCursorItems;
use Doctrine\DBAL\Connection;

use function App\Components\Functions\toArrayString;

final class PostGetByIdsFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function fetch(PostGetByIdsQuery $query): ResultCursorItems
    {
        $ids = toArrayString($query->ids);

        if (\count($ids) === 0) {
            return new ResultCursorItems([], '');
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(['*'])
            ->from('posts')
            ->where($queryBuilder->expr()->in('id', $ids))
            ->andWhere($queryBuilder->expr()->isNull('deleted_at'));

        $result = $queryBuilder
            ->setMaxResults(1000)
            ->executeQuery();

        $rows = $result->fetchAllAssociative();

        return new ResultCursorItems($rows, (string)$result->rowCount());
    }
}
