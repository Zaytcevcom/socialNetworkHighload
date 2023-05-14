<?php

declare(strict_types=1);

namespace App\Modules\Post\Query\GetById\Cached;

use App\Modules\Post\Query\GetByIds\Cached\PostGetByIdsCachedFetcher;
use App\Modules\Post\Query\GetByIds\Cached\PostGetByIdsCachedQuery;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

final class PostGetByIdCachedFetcher
{
    public function __construct(
        private readonly PostGetByIdsCachedFetcher $postGetByIdsCachedFetcher,
    ) {
    }

    public function fetch(PostGetByIdCachedQuery $query): array
    {
        $result = $this->postGetByIdsCachedFetcher->fetch(
            new PostGetByIdsCachedQuery([$query->id])
        );

        if (empty($result)) {
            throw new DomainExceptionModule(
                module: 'post',
                message: 'error.post.post_not_found',
                code: 1
            );
        }

        return (array)$result[0];
    }
}
