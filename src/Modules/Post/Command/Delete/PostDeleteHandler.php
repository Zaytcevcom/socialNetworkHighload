<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\Delete;

use App\Modules\Post\Entity\Post\PostRepository;
use App\Modules\Post\Helpers\PostHelper;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use ZayMedia\Shared\Components\Cacher\Cacher;
use ZayMedia\Shared\Components\Flusher;

final class PostDeleteHandler
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly Flusher $flusher,
        private readonly Cacher $cacher,
    ) {
    }

    public function handle(PostDeleteCommand $command): void
    {
        $post = $this->postRepository->getById($command->postId);

        if ($post->getUserId() !== $command->userId) {
            throw new AccessDeniedException();
        }

        $post->markDeleted();

        $this->flusher->flush();

        $this->cacher->delete(
            key: PostHelper::getCacheKeyPost($post->getId())
        );
    }
}
