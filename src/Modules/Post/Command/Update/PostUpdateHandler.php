<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\Update;

use App\Modules\Post\Entity\Post\PostRepository;
use App\Modules\Post\Helpers\PostHelper;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use ZayMedia\Shared\Components\Cacher\Cacher;
use ZayMedia\Shared\Components\Flusher;

final class PostUpdateHandler
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly Flusher $flusher,
        private readonly Cacher $cacher,
    ) {
    }

    public function handle(PostUpdateCommand $command): void
    {
        $post = $this->postRepository->getById($command->postId);

        if ($post->getUserId() !== $command->userId) {
            throw new AccessDeniedException();
        }

        $post->edit(
            text: $command->text,
        );

        $this->postRepository->add($post);

        $this->flusher->flush();

        $this->cacher->set(
            key: PostHelper::getCacheKeyPost($post->getId()),
            value: json_encode($post->toArray()),
            ttl: PostHelper::getCacheTTLPost()
        );
    }
}
