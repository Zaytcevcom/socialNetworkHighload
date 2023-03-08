<?php

declare(strict_types=1);

namespace App\Modules\Identity\Entity\User;

use App\Http\Exception\DomainExceptionModule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    /**
     * @var EntityRepository<User>
     */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(User::class);
        $this->em = $em;
    }

    public function getById(int $id): User
    {
        if (!$user = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'identity',
                message: 'error.user.user_not_found',
                code: 1
            );
        }

        return $user;
    }

    public function findById(int $id): ?User
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->repo->findOneBy(['username' => $username]);
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }
}
