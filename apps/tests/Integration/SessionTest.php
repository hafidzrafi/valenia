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

    public function testIdleAnonymousSessionIsClearedAndDeleted(): void
    {
        $id = bin2hex(random_bytes(16));
        $hash = hash('sha256', $id);

        $stmt = $this->pdo->prepare(
            "INSERT INTO sessions (user_id, token_hash, payload, last_seen_at, expires_at)
             VALUES (NULL, :hash, 'stale', now() - interval '31 minutes', now() + interval '1 hour')"
        );
        $stmt->execute(['hash' => $hash]);

        $handler = new \DbSessionHandler();
        self::assertSame('', $handler->read($id));

        $check = $this->pdo->prepare('SELECT count(*) FROM sessions WHERE token_hash = :hash');
        $check->execute(['hash' => $hash]);
        self::assertSame(0, (int) $check->fetchColumn());
    }

    public function testFreshAnonymousSessionIsRead(): void
    {
        $id = bin2hex(random_bytes(16));
        $hash = hash('sha256', $id);

        $stmt = $this->pdo->prepare(
            "INSERT INTO sessions (user_id, token_hash, payload, last_seen_at, expires_at)
             VALUES (NULL, :hash, 'fresh', now() - interval '10 minutes', now() + interval '1 hour')"
        );
        $stmt->execute(['hash' => $hash]);

        $handler = new \DbSessionHandler();
        self::assertSame('fresh', $handler->read($id));
    }
}
