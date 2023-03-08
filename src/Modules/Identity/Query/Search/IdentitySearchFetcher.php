<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\Search;

use App\Components\Data\AllCount;
use App\Modules\ResultCountItems;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class IdentitySearchFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /**
     * @throws Exception
     */
    public function fetch(IdentitySearchQuery $query): ResultCountItems
    {
        $sqlQuery = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from('users');

        if (!empty($query->search)) {
            $sqlQuery
                ->andWhere('first_name LIKE :search OR second_name LIKE :search')
                ->setParameter('search', $query->search . '%');
        }

        $result = $sqlQuery
            ->orderBy('id', 'DESC')
            ->setMaxResults($query->count)
            ->setFirstResult($query->offset)
            ->executeQuery();

        $rows = $result->fetchAllAssociative();

        return new ResultCountItems(AllCount::get($sqlQuery, 'rate'), $rows);
    }
}
