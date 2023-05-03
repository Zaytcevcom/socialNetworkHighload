<?php

declare(strict_types=1);

namespace App\Modules\Post\Command\Create;

use App\Modules\Identity\Entity\User\UserRepository;
use App\Modules\Post\Entity\Post\Post;
use App\Modules\Post\Entity\Post\PostRepository;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;
use ZayMedia\Shared\Components\Flusher;
use ZayMedia\Shared\Components\Queue\Queue;

final class PostCreateHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PostRepository $postRepository,
        private readonly Flusher $flusher,
        private readonly Queue $queue,
    ) {
    }

    public function handle(PostCreateCommand $command): Post
    {
        $user = $this->userRepository->getById($command->userId);

        $post = Post::create(
            userId: $user->getId(),
            text: $command->text,
        );

        $this->postRepository->add($post);

        $this->flusher->flush();

        $this->sendToQueueRefreshFeedByPost($post->getId());

        return $post;
    }

    private function sendToQueueRefreshFeedByPost(int $postId): void
    {
        $this->queue->publish(
            queue: PostHelper::getQueueName(PostQueue::REFRESH_FEED_BY_POST),
            message: ['postId' => $postId]
        );
    }
}
