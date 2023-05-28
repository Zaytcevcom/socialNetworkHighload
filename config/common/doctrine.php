<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use ZayMedia\Shared\Components\ReplicaEntityManager\ReplicaEntityManager;
use ZayMedia\Shared\Components\ReplicaEntityManager\ReplicaEntityManagerInterface;

use function App\Components\env;

return [
    EntityManagerInterface::class => static function (ContainerInterface $container): EntityManager {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *     metadata_dirs:string[],
         *     dev_mode:bool,
         *     proxy_dir:string,
         *     'cache_dir':?string,
         *     types:array<string,class-string<\Doctrine\DBAL\Types\Type>>,
         *     subscribers:string[],
         *     connections:array{source:array<string, mixed>, replicas: array{array<string, mixed>}|empty[]}
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = ORMSetup::createAttributeMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ? new FilesystemAdapter('', 0, $settings['cache_dir']) : new ArrayAdapter()
        );
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $connection = DriverManager::getConnection(
            $settings['connections']['source'],
            $config
        );

        return new EntityManager($connection, $config);
    },

    Connection::class => static function (ContainerInterface $container): Connection {
        $em = $container->get(EntityManagerInterface::class);
        return $em->getConnection();
    },

    ReplicaEntityManagerInterface::class => static function (ContainerInterface $container): ReplicaEntityManager {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *     metadata_dirs:string[],
         *     dev_mode:bool,
         *     proxy_dir:string,
         *     'cache_dir':?string,
         *     types:array<string,class-string<\Doctrine\DBAL\Types\Type>>,
         *     subscribers:string[],
         *     connections:array{source:array<string, mixed>, replicas: array{array<string, mixed>}|empty[]}
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = ORMSetup::createAttributeMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ? new FilesystemAdapter('', 0, $settings['cache_dir']) : new ArrayAdapter()
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        if (count($settings['connections']['replicas'])) {
            $slaveId = rand(0, count($settings['connections']['replicas']) - 1);
            $params = $settings['connections']['replicas'][$slaveId];
        } else {
            $params = $settings['connections']['source'];
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $connection = DriverManager::getConnection(
            $params,
            $config
        );

        return new ReplicaEntityManager($connection, $config);
    },

    'config' => [
        'doctrine' => [
            'connections' => [
                'source' => [
                    'driver' => env('DB_DRIVER'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USER'),
                    'password' => env('DB_PASSWORD'),
                    'dbname' => env('DB_NAME'),
                    'charset' => env('DB_CHARSET'),
                    'port' => env('DB_PORT'),
                ],
                'replicas' => [
                    //                    [
                    //                        'driver' => env('DB_DRIVER'),
                    //                        'host' => env('DB_REPLICA_HOST_1'),
                    //                        'user' => env('DB_REPLICA_USER'),
                    //                        'password' => env('DB_REPLICA_PASSWORD'),
                    //                        'dbname' => env('DB_REPLICA_NAME'),
                    //                        'charset' => env('DB_CHARSET'),
                    //                    ],
                    //                    [
                    //                        'driver' => env('DB_DRIVER'),
                    //                        'host' => env('DB_REPLICA_HOST_2'),
                    //                        'user' => env('DB_REPLICA_USER'),
                    //                        'password' => env('DB_REPLICA_PASSWORD'),
                    //                        'dbname' => env('DB_REPLICA_NAME'),
                    //                        'charset' => env('DB_CHARSET'),
                    //                    ],
                ],
            ],
            'dev_mode' => env('APP_ENV') !== 'dev',
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'metadata_dirs' => [
                __DIR__ . '/../../src/Modules/OAuth/Entity',
                __DIR__ . '/../../src/Modules/Identity/Entity',
                __DIR__ . '/../../src/Modules/Friends/Entity',
                __DIR__ . '/../../src/Modules/Post/Entity',
            ],
            'types' => [
            ],
        ],
    ],
];
