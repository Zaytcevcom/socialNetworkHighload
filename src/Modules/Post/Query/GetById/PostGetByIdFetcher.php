<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetById;

use App\Http\Exception\DomainExceptionModule;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class PostGetByIdFetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /** @throws Exception */
    public function fetch(PostGetByIdQuery $query): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select('p.*')
            ->from('posts', 'p')
            ->where('p.id = :id')
            ->andWhere('p.deleted_at IS NULL')
            ->setParameter('id', $query->id)
            ->executeQuery()
            ->fetchAssociative();

        if ($result === false) {
            throw new DomainExceptionModule(
                module: 'post',
                message: 'error.post.post_not_found',
                code: 1
            );
        }

        return $result;
    }
}
