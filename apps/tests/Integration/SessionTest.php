<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class SessionTest extends DatabaseTestCase
{
    public function testAnonymousSessionPayloadIsPersisted(): void
    {
        $id = bin2hex(random_bytes(16));
        $_SESSION = [];

        $handler = new \DbSessionHandler();
        self::assertTrue($handler->write($id, 'anonymous-payload'));
        self::assertSame('anonymous-payload', $handler->read($id));

        $stmt = $this->pdo->prepare('SELECT user_id FROM sessions WHERE token_hash = :hash');
        $stmt->execute(['hash' => hash('sha256', $id)]);
        $row = $stmt->fetch();

        self::assertIsArray($row);
        self::assertNull($row['user_id']);

        $_SESSION = [];
    }
}
