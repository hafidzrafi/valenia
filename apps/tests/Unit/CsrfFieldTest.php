<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class CsrfFieldTest extends TestCase
{
    protected function tearDown(): void
    {
        \Csrf::$token = '';

        parent::tearDown();
    }

    public function testCsrfFieldEscapesToken(): void
    {
        \Csrf::$token = 'abc"def';

        self::assertSame(
            '<input type="hidden" name="_token" value="abc&quot;def">',
            \csrf_field(),
        );
    }
}
