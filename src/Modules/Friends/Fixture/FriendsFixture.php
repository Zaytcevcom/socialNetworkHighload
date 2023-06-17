<?php

declare(strict_types=1);

namespace App\Modules\Friends\Fixture;

use App\Modules\Friends\Entity\Friendship\Friendship;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

/** @noinspection PhpUnused */
final class FriendsFixture extends AbstractFixture
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($userId = 1; $userId <= 10; ++$userId) {
            for ($friendId = 20; $friendId <= 50; ++$friendId) {
                $friendship = Friendship::create(
                    userId: $userId,
                    friendId: $friendId
                );

                $manager->persist($friendship);

                $friendship = Friendship::create(
                    userId: $friendId,
                    friendId: $userId
                );

                $manager->persist($friendship);
            }
        }

        $manager->flush();
    }
}
