<?php

declare(strict_types=1);

namespace App\Modules\Friends\Entity\Friendship;

use App\Http\Exception\DomainExceptionModule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class FriendshipRepository
{
    /**
     * @var EntityRepository<Friendship>
     */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Friendship::class);
        $this->em = $em;
    }

    public function getById(int $id): Friendship
    {
        if (!$friendship = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'friends',
                message: 'error.friends.friendship_not_found',
                code: 1
            );
        }

        return $friendship;
    }

    public function findById(int $id): ?Friendship
    {
        return $this->repo->findOneBy([
            'id' => $id,
        ]);
    }

    public function findByUserAndFriendIds(int $userId, int $friendId): ?Friendship
    {
        return $this->repo->findOneBy(['userId' => $userId, 'friendId' => $friendId]);
    }

    public function add(Friendship $friendship): void
    {
        $this->em->persist($friendship);
    }

    public function remove(Friendship $friendship): void
    {
        $this->em->remove($friendship);
    }
}
