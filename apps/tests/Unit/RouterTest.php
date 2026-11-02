<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RouterTest extends TestCase
{
    private \Router $router;

    protected function setUp(): void
    {
        $this->router = new \Router(dirname(__DIR__).'/Fixtures/routes');
    }

    public function testIndexRouteMatchesRoot(): void
    {
        $response = $this->router->dispatch('GET', '/');

        self::assertSame(200, $response->status);
        self::assertSame('home', $response->body);
    }

    public function testStaticRouteMatches(): void
    {
        $response = $this->router->dispatch('GET', '/about');

        self::assertSame('about', $response->body);
    }

    public function testDynamicRouteCapturesParameter(): void
    {
        $response = $this->router->dispatch('GET', '/room/42');

        self::assertSame('room:42', $response->body);
    }

    public function testUnknownPathReturnsNotFound(): void
    {
        $response = $this->router->dispatch('GET', '/missing');

        self::assertSame(404, $response->status);
    }

    public function testKnownPathWithWrongMethodReturnsMethodNotAllowed(): void
    {
        $response = $this->router->dispatch('POST', '/about');

        self::assertSame(405, $response->status);
    }
}
