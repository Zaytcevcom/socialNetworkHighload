<?php

declare(strict_types=1);

namespace App\Modules\Friends\Entity\Friendship;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'friendships')]
#[ORM\Index(fields: ['userId'], name: 'IDX_USER_ID')]
#[ORM\Index(fields: ['friendId'], name: 'IDX_FRIEND_ID')]
class Friendship
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'integer')]
    private int $friendId;

    #[ORM\Column(type: 'integer')]
    private int $createdAt;

    private function __construct(
        int $userId,
        int $friendId,
    ) {
        $this->userId = $userId;
        $this->friendId = $friendId;

        $this->createdAt = time();
    }

    public static function create(
        int $userId,
        int $friendId,
    ): self {
        return new self(
            userId: $userId,
            friendId: $friendId,
        );
    }

    public function getId(): int
    {
        if (null === $this->id) {
            throw new DomainException('Id not set');
        }
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getFriendId(): int
    {
        return $this->friendId;
    }

    public function setFriendId(int $friendId): void
    {
        $this->friendId = $friendId;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->getId(),
            'user_id'   => $this->getUserId(),
            'friend_id' => $this->getFriendId(),
        ];
    }
}
