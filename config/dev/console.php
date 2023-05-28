<?php

declare(strict_types=1);

use App\Modules\Identity\Console\GenerateUsersCommand;
use App\Modules\OAuth\Console\E2ETokenCommand;
use App\Modules\Post\Console\ConsumerRefreshFeedByPostCommand;
use App\Modules\Post\Console\ConsumerRefreshFeedByUserCommand;
use App\Modules\Post\Console\RefreshFeedAllUsersCommand;
use Doctrine\Migrations;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\SchemaTool;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;
use ZayMedia\Shared\Console\FixturesLoadCommand;

return [
    FixturesLoadCommand::class => static function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{fixture_paths:string[]} $config
         */
        $config = $container->get('config')['console'];

        return new FixturesLoadCommand(
            $container->get(EntityManagerInterface::class),
            $config['fixture_paths'],
        );
    },

    DropCommand::class => static fn (ContainerInterface $container): DropCommand => new DropCommand($container->get(EntityManagerProvider::class)),

    'config' => [
        'console' => [
            'commands' => [
                FixturesLoadCommand::class,

                SchemaTool\DropCommand::class,

                Migrations\Tools\Console\Command\DiffCommand::class,
                Migrations\Tools\Console\Command\GenerateCommand::class,

                E2ETokenCommand::class,

                GenerateUsersCommand::class,
                RefreshFeedAllUsersCommand::class,

                ConsumerRefreshFeedByUserCommand::class,
                ConsumerRefreshFeedByPostCommand::class,
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Modules/Identity/Fixture',
                __DIR__ . '/../../src/Modules/Friends/Fixture',
                __DIR__ . '/../../src/Modules/Post/Fixture',
            ],
        ],
    ],
];
