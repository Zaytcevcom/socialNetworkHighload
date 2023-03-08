<?php

declare(strict_types=1);

namespace App\Components\Serializer\Test;

use App\Components\Serializer\Normalizer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \App\Components\Serializer\Normalizer
 *
 * @internal
 */
final class NormalizerTest extends TestCase
{
    public function testValid(): void
    {
        $object = new stdClass();

        $origin = $this->createMock(NormalizerInterface::class);
        $origin->expects(self::once())->method('normalize')
            ->with($object)
            ->willReturn(['name' => 'John']);

        $normalizer = new Normalizer($origin);

        $result = $normalizer->normalize($object);

        self::assertSame(['name' => 'John'], $result);
    }
}
