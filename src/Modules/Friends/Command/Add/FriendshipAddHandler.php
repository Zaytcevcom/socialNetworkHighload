<?php

declare(strict_types=1);

namespace App\Modules\Friends\Command\Add;

use App\Components\Flusher;
use App\Components\Queue\Queue;
use App\Http\Exception\DomainExceptionModule;
use App\Modules\Friends\Entity\Friendship\Friendship;
use App\Modules\Friends\Entity\Friendship\FriendshipRepository;
use App\Modules\Friends\Entity\FriendshipRequest\FriendshipRequest;
use App\Modules\Friends\Entity\FriendshipRequest\FriendshipRequestRepository;
use App\Modules\Friends\Query\IsFriend\FriendsIsFriendFetcher;
use App\Modules\Friends\Query\IsFriend\FriendsIsFriendQuery;
use App\Modules\Friends\Query\IsSubscriber\FriendsIsSubscriberFetcher;
use App\Modules\Friends\Query\IsSubscriber\FriendsIsSubscriberQuery;
use App\Modules\Identity\Entity\User\UserRepository;
use App\Modules\Post\Helpers\PostHelper;
use App\Modules\Post\Helpers\PostQueue;
use Doctrine\DBAL\Exception;

final class FriendshipAddHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FriendshipRepository $friendshipRepository,
        private readonly FriendshipRequestRepository $friendshipRequestRepository,
        private readonly FriendsIsFriendFetcher $friendsIsFriendFetcher,
        private readonly FriendsIsSubscriberFetcher $friendsIsSubscriberFetcher,
        private readonly Flusher $flusher,
        private readonly Queue $queue,
    ) {
    }

    /** @throws Exception */
    public function handle(FriendshipAddCommand $command): void
    {
        if ($command->userId === $command->friendId) {
            throw new DomainExceptionModule(
                module: 'friends',
                message: 'error.friends.ids_must_not_match',
                code: 2
            );
        }

        $user   = $this->userRepository->getById($command->userId);
        $friend = $this->userRepository->getById($command->friendId);

        // Check is subscriber
        if ($this->friendsIsSubscriberFetcher->fetch(
            new FriendsIsSubscriberQuery($user->getId(), $friend->getId())
        )) {
            return;
        }

        // Check is friends
        if ($this->friendsIsFriendFetcher->fetch(
            new FriendsIsFriendQuery($user->getId(), $friend->getId())
        )) {
            return;
        }

        if ($friendshipRequest = $this->friendshipRequestRepository->findByUserAndFriendIds($friend->getId(), $user->getId())) {
            $this->acceptIncomingRequest($user->getId(), $friend->getId(), $friendshipRequest);

            $this->sendToQueueRefreshFeedByUser($user->getId());
            $this->sendToQueueRefreshFeedByUser($friend->getId());

            return;
        }

        $this->sendRequest($user->getId(), $friend->getId());

        $this->sendToQueueRefreshFeedByUser($user->getId());
    }

    private function acceptIncomingRequest(int $userId, int $friendId, FriendshipRequest $friendshipRequest): void
    {
        $this->friendshipRepository->add(
            Friendship::create(
                userId: $userId,
                friendId: $friendId
            )
        );

        $this->friendshipRepository->add(
            Friendship::create(
                userId: $friendId,
                friendId: $userId
            )
        );

        $this->friendshipRequestRepository->remove($friendshipRequest);

        $this->flusher->flush();
    }

    private function sendRequest(int $userId, int $friendId): void
    {
        $friendshipRequest = FriendshipRequest::create(
            userId: $userId,
            friendId: $friendId
        );

        $this->friendshipRequestRepository->add($friendshipRequest);
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
