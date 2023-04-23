<?php

declare(strict_types=1);

namespace App\Modules\Post\Entity\Post;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'posts')]
#[ORM\Index(fields: ['userId'], name: 'IDX_USER')]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', unique: true, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $userId;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $createdAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $updatedAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $deletedAt;

    private function __construct(
        int $userId,
        string $text,
    ) {
        $this->userId = $userId;
        $this->text = $text;

        $this->createdAt = time();
        $this->updatedAt = null;
        $this->deletedAt = null;
    }

    public static function create(
        int $userId,
        string $text,
    ): self {
        return new self(
            userId: $userId,
            text: $text,
        );
    }

    public function edit(
        string $text
    ): void {
        $this->text = $text;

        $this->updatedAt = time();
    }

    public function markDeleted(): void
    {
        $this->deletedAt = time();
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

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?int $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getDeletedAt(): ?int
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?int $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function toArray(): array
    {
        return [
            'id'      => $this->getId(),
            'user_id' => $this->getUserId(),
            'text'    => $this->getText(),
        ];
    }
}
