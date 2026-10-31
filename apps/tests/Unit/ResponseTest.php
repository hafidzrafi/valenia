<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ResponseTest extends TestCase
{
    public function testFromStringCreatesOkResponse(): void
    {
        $response = \Response::from('body');

        self::assertSame('body', $response->body);
        self::assertSame(200, $response->status);
    }

    public function testFromResponseReturnsSameInstance(): void
    {
        $original = new \Response('x', 201);
        $response = \Response::from($original);

        self::assertSame($original, $response);
    }

    public function testRedirectCreatesLocationHeader(): void
    {
        $response = \redirect('/login');

        self::assertSame(302, $response->status);
        self::assertSame('/login', $response->headers['Location']);
        self::assertSame('', $response->body);
    }

    public function testRedirectPreservesCustomStatus(): void
    {
        $response = \redirect('/x', 303);

        self::assertSame(303, $response->status);
        self::assertSame('/x', $response->headers['Location']);
    }
}
