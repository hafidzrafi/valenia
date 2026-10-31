<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class SupportHelpersTest extends TestCase
{
    public function testAppPathReturnsAbsoluteApplicationPath(): void
    {
        $path = \app_path();

        self::assertSame('apps', basename($path));
        self::assertDirectoryExists($path);
    }

    public function testEnvReturnsDefaultForMissingKey(): void
    {
        self::assertSame('fallback', \env('VALENIA_TEST_MISSING_KEY', 'fallback'));
    }
}
