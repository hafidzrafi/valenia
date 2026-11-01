<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class CsrfMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_COOKIE = [];
        $_POST = [];
        $_SERVER['HTTP_X_CSRF_TOKEN'] = '';
        \Csrf::$token = '';
    }

    protected function tearDown(): void
    {
        $_COOKIE = [];
        $_POST = [];
        unset($_SERVER['HTTP_X_CSRF_TOKEN']);
        \Csrf::$token = '';

        parent::tearDown();
    }

    public function testMissingTokenReturns419(): void
    {
        $_COOKIE['csrf_token'] = 'known-token';
        $_POST['_token'] = 'wrong-token';

        self::assertSame(419, $this->runPipeline()->status);
    }

    public function testMatchingFormTokenPasses(): void
    {
        $_COOKIE['csrf_token'] = 'known-token';
        $_POST['_token'] = 'known-token';

        $response = $this->runPipeline();

        self::assertSame(200, $response->status);
        self::assertSame('ok', $response->body);
    }

    public function testMatchingHeaderTokenPasses(): void
    {
        $_COOKIE['csrf_token'] = 'known-token';
        $_SERVER['HTTP_X_CSRF_TOKEN'] = 'known-token';

        $response = $this->runPipeline();

        self::assertSame(200, $response->status);
        self::assertSame('ok', $response->body);
    }

    private function runPipeline(string $method = 'POST'): \Response
    {
        $pipeline = \Middleware::pipeline(
            ['csrf'],
            static fn (\Request $request): \Response => new \Response('ok'),
        );

        return $pipeline(new \Request($method, '/form'));
    }
}
