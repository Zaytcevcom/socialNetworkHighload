<?php

declare(strict_types=1);

namespace App\Components\FeatureToggle\Test;

use App\Components\FeatureToggle\FeatureFlag;
use App\Components\FeatureToggle\FeatureFlagTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @covers \App\Components\FeatureToggle\FeatureFlagTwigExtension
 *
 * @internal
 */
final class FeatureFlagTwigExtensionTest extends TestCase
{
    public function testActive(): void
    {
        $flag = $this->createMock(FeatureFlag::class);
        $flag->expects(self::once())->method('isEnabled')->with('ONE')->willReturn(true);

        $twig = new Environment(new ArrayLoader([
            'page.html.twig' => '<p>{{ is_feature_enabled(\'ONE\') ? \'true\' : \'false\' }}</p>',
        ]));

        $twig->addExtension(new FeatureFlagTwigExtension($flag));

        self::assertEquals('<p>true</p>', $twig->render('page.html.twig'));
    }

    public function testNotActive(): void
    {
        $flag = $this->createMock(FeatureFlag::class);
        $flag->expects(self::once())->method('isEnabled')->with('ONE')->willReturn(false);

        $twig = new Environment(new ArrayLoader([
            'page.html.twig' => '<p>{{ is_feature_enabled(\'ONE\') ? \'true\' : \'false\' }}</p>',
        ]));

        $twig->addExtension(new FeatureFlagTwigExtension($flag));

        self::assertEquals('<p>false</p>', $twig->render('page.html.twig'));
    }
}
