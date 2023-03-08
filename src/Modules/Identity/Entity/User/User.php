<?php

declare(strict_types=1);

namespace App\Modules\Identity\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $username;

    #[ORM\Column(type: 'string', length: 50)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 50)]
    private string $secondName;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sex = null;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $birthdate;

    #[ORM\Column(type: 'string')]
    private string $biography;

    #[ORM\Column(type: 'string')]
    private string $city;

    #[ORM\Column(type: 'string')]
    private string $password;

    private function __construct(
        string $username,
        string $firstName,
        string $secondName,
        ?int $sex,
        DateTimeImmutable $birthdate,
        string $biography,
        string $city,
        string $password,
    ) {
        $this->username = $username;
        $this->firstName = $firstName;
        $this->secondName = $secondName;
        $this->sex = $sex;
        $this->birthdate = $birthdate;
        $this->biography = $biography;
        $this->city = $city;
        $this->password = $password;
    }

    public static function signup(
        string $username,
        string $firstName,
        string $secondName,
        ?int $sex,
        DateTimeImmutable $birthdate,
        string $biography,
        string $city,
        string $password,
    ): self {
        return new self(
            username: $username,
            firstName: $firstName,
            secondName: $secondName,
            sex: $sex,
            birthdate: $birthdate,
            biography: $biography,
            city: $city,
            password: $password,
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

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getSecondName(): string
    {
        return $this->secondName;
    }

    public function setSecondName(string $secondName): void
    {
        $this->secondName = $secondName;
    }

    public function getSex(): ?int
    {
        return $this->sex;
    }

    public function setSex(?int $sex): void
    {
        $this->sex = $sex;
    }

    public function getBirthdate(): string
    {
        return $this->birthdate;
    }

    public function setBirthdate(string $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    public function getBiography(): string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): void
    {
        $this->biography = $biography;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->getId(),
            'username'      => $this->getUsername(),
            'first_name'    => $this->getFirstName(),
            'second_name'   => $this->getSecondName(),
            'sex'           => $this->getSex(),
            'birthdate'     => $this->getBirthdate(),
            'biography'     => $this->getBiography(),
            'city'          => $this->getCity(),
        ];
    }
}
