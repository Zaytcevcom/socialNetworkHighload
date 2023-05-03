<?php

declare(strict_types=1);

namespace App\Modules\Identity\Fixture;

use App\Modules\Identity\Entity\User\User;
use App\Modules\Identity\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;

final class UserFixture extends AbstractFixture
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        $locale = 'ru_RU';
        $password = (new PasswordHasher())->hash('1234567890');

        $user = User::signup(
            username: 'demo',
            firstName: Factory::create($locale)->firstName,
            secondName: Factory::create($locale)->lastName,
            sex: rand(0, 1),
            birthdate: new DateTimeImmutable(Factory::create($locale)->date()),
            biography: Factory::create($locale)->text,
            city: Factory::create($locale)->city,
            password: $password
        );

        $manager->persist($user);

        for ($i = 0; $i < 10000; ++$i) {
            $user = User::signup(
                username: Factory::create($locale)->userName() . $i,
                firstName: Factory::create($locale)->firstName,
                secondName: Factory::create($locale)->lastName,
                sex: rand(0, 1),
                birthdate: new DateTimeImmutable(Factory::create($locale)->date()),
                biography: Factory::create($locale)->text,
                city: Factory::create($locale)->city,
                password: $password
            );

            $manager->persist($user);
        }

        $manager->flush();
    }
}
