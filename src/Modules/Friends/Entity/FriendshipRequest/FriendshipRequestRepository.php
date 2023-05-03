<?php

declare(strict_types=1);

namespace App\Modules\Friends\Entity\FriendshipRequest;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

class FriendshipRequestRepository
{
    /**
     * @var EntityRepository<FriendshipRequest>
     */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(FriendshipRequest::class);
        $this->em = $em;
    }

    public function getById(int $id): FriendshipRequest
    {
        if (!$friendshipRequest = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'friends',
                message: 'error.friends.friendshipRequest_not_found',
                code: 1
            );
        }

        return $friendshipRequest;
    }

    public function findById(int $id): ?FriendshipRequest
    {
        return $this->repo->findOneBy([
            'id' => $id,
        ]);
    }

    public function findByUserAndFriendIds(int $userId, int $friendId): ?FriendshipRequest
    {
        return $this->repo->findOneBy(['userId' => $userId, 'friendId' => $friendId]);
    }

    public function add(FriendshipRequest $friendshipRequest): void
    {
        $this->em->persist($friendshipRequest);
    }

    public function remove(FriendshipRequest $friendshipRequest): void
    {
        $this->em->remove($friendshipRequest);
    }
}
