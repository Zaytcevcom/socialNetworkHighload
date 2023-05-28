<?php

declare(strict_types=1);

namespace App\Modules\Post\Fixture;

use App\Modules\Post\Entity\Post\Post;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class PostFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 2_000; ++$i) {
            $post = Post::create(
                userId: rand(1, 100),
                text: Factory::create('ru_RU')->text,
            );

            $manager->persist($post);

            if ($i % 10_000 === 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }
}
