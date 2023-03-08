<?php

declare(strict_types=1);

use App\Components\FeatureToggle\FeatureFlag;
use App\Components\FeatureToggle\Features;
use App\Components\FeatureToggle\FeaturesContext;
use App\Components\FeatureToggle\FeatureSwitch;
use Psr\Container\ContainerInterface;

return [
    FeatureFlag::class => DI\get(Features::class),
    FeatureSwitch::class => DI\get(Features::class),
    FeaturesContext::class => DI\get(Features::class),

    Features::class => static function (ContainerInterface $container): Features {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{features: array<string, bool>} $config
         */
        $config = $container->get('config')['feature-toggle'];

        return new Features($config['features']);
    },

    'config' => [
        'feature-toggle' => [
            'features' => [
                'IS_DEV' => false,
            ],
        ],
    ],
];
