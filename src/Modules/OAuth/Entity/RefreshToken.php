<?php

declare(strict_types=1);

namespace App\Modules\OAuth\Entity;

use Doctrine\ORM\Mapping as ORM;
use DomainException;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

#[ORM\Entity]
#[ORM\Table(name: 'oauth_refresh_tokens')]
#[ORM\Index(fields: ['identifier'], name: 'IDX_SEARCH')]
#[ORM\Index(fields: ['userIdentifier'], name: 'IDX_USER_ID')]
final class RefreshToken implements RefreshTokenEntityInterface
{
    use EntityTrait;
    use RefreshTokenTrait;

    /** @psalm-suppress MissingPropertyType */
    #[ORM\Column(type: 'string', length: 80)]
    #[ORM\Id]
    protected $identifier;

    /** @psalm-suppress MissingPropertyType */
    #[ORM\Column(type: 'datetime_immutable')]
    protected $expiryDateTime;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?string $userIdentifier = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $locale = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $createdAt = 0;

    public function setAccessToken(AccessTokenEntityInterface $accessToken): void
    {
        $this->accessToken = $accessToken;
        $this->userIdentifier = (string)$accessToken->getUserIdentifier();
    }

    public function getUserIdentifier(): ?string
    {
        if (null === $this->userIdentifier) {
            throw new DomainException('Id not set');
        }

        return $this->userIdentifier;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
