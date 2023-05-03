<?php

declare(strict_types=1);

namespace App\Modules\Post\Entity\Post;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

class PostRepository
{
    /**
     * @var EntityRepository<Post>
     */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Post::class);
        $this->em = $em;
    }

    public function getById(int $id): Post
    {
        if (!$post = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'post',
                message: 'error.post.post_not_found',
                code: 1
            );
        }

        return $post;
    }

    public function findById(int $id): ?Post
    {
        return $this->repo->findOneBy([
            'id' => $id,
            'deletedAt' => null,
        ]);
    }

    public function add(Post $post): void
    {
        $this->em->persist($post);
    }

    public function remove(Post $post): void
    {
        $this->em->remove($post);
    }
}
