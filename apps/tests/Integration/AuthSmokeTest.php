<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class AuthSmokeTest extends DatabaseTestCase
{
    protected bool $useTransaction = false;

    protected function tearDown(): void
    {
        if (isset($this->pdo)) {
            $this->pdo->exec("DELETE FROM auth_events WHERE username = 'admin'");
            $this->pdo->exec(
                "UPDATE credentials
                 SET failed_attempts = 0, locked_until = NULL, last_login_at = NULL
                 WHERE user_id = (SELECT id FROM users WHERE username = 'admin')"
            );
        }

        parent::tearDown();
    }

    public function testSeededAdminCanAuthenticate(): void
    {
        $password = getenv('SEED_ADMIN_PASSWORD') ?: 'admin12345';
        $result = \authenticate('admin', $password);

        self::assertTrue($result['ok']);
        self::assertSame('admin', $result['user']['username']);
    }

    public function testWrongPasswordIsRejected(): void
    {
        $result = \authenticate('admin', 'definitely-not-the-password');

        self::assertFalse($result['ok']);
        self::assertFalse($result['locked']);
    }

    public function testFailedLoginIsRecorded(): void
    {
        \authenticate('admin', 'definitely-not-the-password');

        $stmt = $this->pdo->prepare(
            "SELECT count(*) FROM auth_events WHERE event = 'login_failure' AND username = 'admin'"
        );
        $stmt->execute();

        self::assertGreaterThanOrEqual(1, (int) $stmt->fetchColumn());
    }
}
