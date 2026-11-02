<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RequestTest extends TestCase
{
    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];

        parent::tearDown();
    }

    public function testConstructorStoresRequestData(): void
    {
        $request = new \Request('GET', 'room/42', ['id' => '42']);

        self::assertSame('GET', $request->method);
        self::assertSame('room/42', $request->path);
        self::assertSame('42', $request->params['id']);
    }

    public function testAttributeReturnsValueOrDefault(): void
    {
        $request = new \Request('GET', '/');
        $request->attributes['user'] = 'admin';

        self::assertSame('admin', $request->attribute('user'));
        self::assertSame('fallback', $request->attribute('missing', 'fallback'));
    }

    public function testRequestInputPrefersPostThenGet(): void
    {
        $_POST['name'] = '  from post  ';
        $_GET['name'] = 'from get';

        self::assertSame('from post', \request_input('name'));
    }

    public function testRequestInputFallsBackToGet(): void
    {
        $_GET['name'] = '  from get  ';

        self::assertSame('from get', \request_input('name'));
    }

    public function testRequestInputReturnsDefaultForMissingKey(): void
    {
        self::assertSame('default', \request_input('missing', 'default'));
    }
}
