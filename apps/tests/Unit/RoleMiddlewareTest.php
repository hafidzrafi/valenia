<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RoleMiddlewareTest extends TestCase
{
    public function testSingleRoleStillMatches(): void
    {
        self::assertSame(200, $this->runPipeline('admin', 'admin')->status);
        self::assertSame(403, $this->runPipeline('perawat', 'admin')->status);
    }

    public function testCommaSeparatedRolesAllowEitherRole(): void
    {
        self::assertSame(200, $this->runPipeline('admin', 'admin,perawat')->status);
        self::assertSame(200, $this->runPipeline('perawat', 'admin,perawat')->status);
        self::assertSame(403, $this->runPipeline('guest', 'admin,perawat')->status);
    }

    private function runPipeline(string $role, string $required): \Response
    {
        $request = new \Request('GET', '/admin');
        $request->attributes['role'] = $role;

        $pipeline = \Middleware::pipeline(
            ['role:'.$required],
            static fn (\Request $request): \Response => new \Response('ok'),
        );

        return $pipeline($request);
    }
}
