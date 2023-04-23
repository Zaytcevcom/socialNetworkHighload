<?php

declare(strict_types=1);

namespace App\Modules\Friends\Command\Remove;

use App\Components\Flusher;
use App\Components\Queue\Queue;
use App\Http\Exception\DomainExceptionModule;
use App\Modules\Friends\Entity\Friendship\Friendship;
use App\Modules\Friends\Entity\Friendship\FriendshipRepository;
use App\Modules\Friends\Entity\FriendshipRequest\FriendshipRequest;
use App\Modules\Friends\Entity\FriendshipRequest\FriendshipRequestRepository;
use App\Modules\Identity\Entity\User\UserRepository;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;

final class FriendshipRemoveHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FriendshipRepository $friendshipRepository,
        private readonly FriendshipRequestRepository $friendshipRequestRepository,
        private readonly Flusher $flusher,
        private readonly Queue $queue,
    ) {
    }

    public function handle(FriendshipRemoveCommand $command): void
    {
        if ($command->userId === $command->friendId) {
            throw new DomainExceptionModule(
                module: 'friends',
                message: 'error.friends.ids_must_not_match',
                code: 1
            );
        }

        $user   = $this->userRepository->getById($command->userId);
        $friend = $this->userRepository->getById($command->friendId);

        // is contact
        if ($friendship = $this->friendshipRepository->findByUserAndFriendIds($user->getId(), $friend->getId())) {
            $this->removeContact($friend->getId(), $user->getId(), $friendship);

            $this->sendToQueueRefreshFeedByUser($friend->getId());
            $this->sendToQueueRefreshFeedByUser($user->getId());

            return;
        }

        // is request out
        if ($friendshipRequestOut = $this->friendshipRequestRepository->findByUserAndFriendIds($user->getId(), $friend->getId())) {
            $this->removeRequestOut($friendshipRequestOut);
            return;
        }

        // is request in
        if ($friendshipRequestIn = $this->friendshipRequestRepository->findByUserAndFriendIds($friend->getId(), $user->getId())) {
            $this->removeRequestIn($friendshipRequestIn);
        }
    }

    private function removeContact(int $friendId, int $userId, Friendship $friendship): void
    {
        $friendshipReverse = $this->friendshipRepository->findByUserAndFriendIds(
            userId: $friendId,
            friendId: $userId
        );

        $this->friendshipRepository->remove($friendship);

        if ($friendshipReverse) {
            $this->friendshipRepository->remove($friendshipReverse);
        }

        $friendshipRequest = FriendshipRequest::create(
            userId: $friendId,
            friendId: $userId
        );
        $friendshipRequest->refused();

        $this->friendshipRequestRepository->add($friendshipRequest);

        $this->flusher->flush();
    }

    private function removeRequestOut(FriendshipRequest $friendshipRequest): void
    {
        $this->friendshipRequestRepository->remove($friendshipRequest);
        $this->flusher->flush();
    }

    private function removeRequestIn(FriendshipRequest $friendshipRequest): void
    {
        $friendshipRequest->refused();
        $this->flusher->flush();
    }

    private function sendToQueueRefreshFeedByUser(int $userId): void
    {
        $this->queue->send(
            queue: PostHelper::getQueueName(PostQueue::REFRESH_FEED_BY_USER),
            message: ['userId' => $userId]
        );
    }
}
