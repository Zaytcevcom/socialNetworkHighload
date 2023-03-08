<?php

declare(strict_types=1);

namespace App\Components\FeatureToggle\Test;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class FeatureMonologProcessorTest extends TestCase
{
    public function testProcess(): void
    {
        self::markTestIncomplete(
            'This test has not been implemented yet.'
        );
        //     $context = $this->createStub(FeaturesContext::class);

    //     $context->method('getAllEnabled')->willReturn($source = ['ONE', 'TWO']);

    //     $processor = new FeaturesMonologProcessor($context);

    //     $date = new DateTimeImmutable();

    //     $result = $processor([
    //         'message' => 'Message',
    //         'context' => ['name' => 'value'],
    //         'level' => Level::Warning,
    //         'level_name' => 'WARNING',
    //         'channel' => 'channel',
    //         'datetime' => $date,
    //         'extra' => ['param' => 'value'],
    //     ]);

    //     self::assertEquals([
    //         'message' => 'Message',
    //         'context' => ['name' => 'value'],
    //         'level' => Level::Warning,
    //         'level_name' => 'WARNING',
    //         'channel' => 'channel',
    //         'datetime' => $date,
    //         'extra' => [
    //             'param' => 'value',
    //             'features' => $source,
    //         ],
    //     ], $result);
    }
}
