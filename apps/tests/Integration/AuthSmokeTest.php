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

    public function testFifthFailedLoginLocksTheAccount(): void
    {
        $this->pdo->exec(
            "UPDATE credentials
             SET failed_attempts = 4, locked_until = NULL
             WHERE user_id = (SELECT id FROM users WHERE username = 'admin')"
        );

        $result = \authenticate('admin', 'definitely-not-the-password');

        self::assertFalse($result['ok']);
        self::assertTrue($result['locked']);

        $stmt = $this->pdo->prepare(
            "SELECT failed_attempts, locked_until
             FROM credentials
             WHERE user_id = (SELECT id FROM users WHERE username = 'admin')"
        );
        $stmt->execute();
        $row = $stmt->fetch();

        self::assertIsArray($row);
        self::assertSame(5, (int) $row['failed_attempts']);
        self::assertNotNull($row['locked_until']);
    }

    public function testLockedAccountRejectsTheCorrectPassword(): void
    {
        $this->pdo->exec(
            "UPDATE credentials
             SET failed_attempts = 5, locked_until = now() + interval '15 minutes'
             WHERE user_id = (SELECT id FROM users WHERE username = 'admin')"
        );

        $password = getenv('SEED_ADMIN_PASSWORD') ?: 'admin12345';
        $result = \authenticate('admin', $password);

        self::assertFalse($result['ok']);
        self::assertTrue($result['locked']);
    }

    public function testResetPasswordRevokesEverySession(): void
    {
        $userId = $this->createTempUser('reset');

        try {
            $this->insertSession($userId);
            $this->insertSession($userId);
            self::assertSame(2, $this->sessionCount($userId));

            $result = \reset_user_password($userId, 'New-Pass-123', $this->adminId());

            self::assertTrue($result['ok'], (string) $result['error']);
            self::assertSame(0, $this->sessionCount($userId));
        } finally {
            $this->deleteTempUser($userId);
        }
    }

    public function testDeactivationRevokesEverySession(): void
    {
        $userId = $this->createTempUser('deactivate');

        try {
            $this->insertSession($userId);
            self::assertSame(1, $this->sessionCount($userId));

            $result = \set_user_active($userId, false, $this->adminId());

            self::assertTrue($result['ok'], (string) $result['error']);
            self::assertSame(0, $this->sessionCount($userId));
        } finally {
            $this->deleteTempUser($userId);
        }
    }

    public function testTheLastActiveAdminCannotBeDisabled(): void
    {
        $adminId = $this->adminId();
        $tempId = $this->createTempUser('last-admin', 'admin');

        try {
            $disableSeeded = \set_user_active($adminId, false, $tempId);
            self::assertTrue($disableSeeded['ok'], (string) $disableSeeded['error']);

            $result = \set_user_active($tempId, false, $adminId);

            self::assertFalse($result['ok']);
            self::assertSame('Tidak dapat menonaktifkan admin aktif terakhir.', $result['error']);
        } finally {
            \set_user_active($adminId, true, $tempId);
            $this->deleteTempUser($tempId);
        }
    }

    private function createTempUser(string $prefix, string $role = 'perawat'): string
    {
        $username = $prefix.'-'.bin2hex(random_bytes(4));
        $result = \create_user($username, 'Temp Auth', $role, 'Start-Pass-123', $this->adminId());
        self::assertTrue($result['ok'], (string) $result['error']);

        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $id = $stmt->fetchColumn();
        self::assertIsString($id);

        return $id;
    }

    private function insertSession(string $userId): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO sessions (user_id, token_hash, expires_at)
             VALUES (:user, :hash, now() + interval '1 hour')"
        );
        $stmt->execute([
            'user' => $userId,
            'hash' => hash('sha256', bin2hex(random_bytes(16))),
        ]);
    }

    private function sessionCount(string $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT count(*) FROM sessions WHERE user_id = :user');
        $stmt->execute(['user' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    private function deleteTempUser(string $userId): void
    {
        $this->pdo->prepare('DELETE FROM sessions WHERE user_id = :user')->execute(['user' => $userId]);
        $this->pdo->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $userId]);
    }
}
