<?php

declare(strict_types=1);

namespace App\Modules\Identity\Fixture;

use App\Modules\Identity\Entity\User\User;
use App\Modules\Identity\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $user = User::signup(
            username: 'zaytcevcom',
            firstName: 'Konstantin',
            secondName: 'Zaytcev',
            sex: 1,
            birthdate: new DateTimeImmutable('1996-03-18'),
            biography: 'Web developer',
            city: 'Moscow',
            password: (new PasswordHasher())->hash('1234567890')
        );

        $manager->persist($user);

        $manager->flush();
    }
}
