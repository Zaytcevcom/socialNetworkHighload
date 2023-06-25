<?php

declare(strict_types=1);

use App\Components\MetricsREDEnd;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Psr\Container\ContainerInterface;

use function App\Components\env;

return [
    MetricsREDEnd::class => static function (ContainerInterface $container): MetricsREDEnd {
        $registry = $container->get(CollectorRegistry::class);

        return new MetricsREDEnd(
            registry: $registry,
            namespace: 'app'
        );
    },

    CollectorRegistry::class => static function (ContainerInterface $container): CollectorRegistry {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *     redis_host:string,
         *     redis_port:string,
         *     redis_password:string
         * } $config
         */
        $config = $container->get('config')['prometheus'];

        return new CollectorRegistry(
            new Redis([
                'host' => $config['redis_host'],
                'port' => $config['redis_port'],
                'password' => $config['redis_password'],
            ])
        );
    },

    'config' => [
        'prometheus' => [
            'redis_host' => env('REDIS_HOST'),
            'redis_port' => env('REDIS_PORT'),
            'redis_password' => env('REDIS_PASSWORD'),
        ],
    ],
];
