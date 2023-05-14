<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\Search;

use ZayMedia\Shared\Components\ReplicaEntityManager\ReplicaEntityManagerInterface;
use ZayMedia\Shared\Helpers\CursorPagination\CursorPagination;
use ZayMedia\Shared\Helpers\CursorPagination\CursorPaginationResult;

final class IdentitySearchFetcher
{
    public function __construct(
        private readonly ReplicaEntityManagerInterface $connection
    ) {
    }

    public function fetch(IdentitySearchQuery $query): CursorPaginationResult
    {
        $sqlQuery = $this->connection->getConnection()->createQueryBuilder()
            ->select(['*'])
            ->from('users');

        if (!empty($query->search)) {
            $sqlQuery
                ->andWhere('first_name LIKE :search && second_name LIKE :search')
                ->setParameter('search', $query->search . '%');
        }

        return CursorPagination::generateResult(
            query: $sqlQuery,
            cursor: $query->cursor,
            count: $query->count,
            isSortDescending: true,
            orderingBy: [
                'id' => 'DESC',
            ],
            field: 'id',
        );
    }
}
