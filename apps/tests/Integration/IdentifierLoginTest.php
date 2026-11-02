<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * Login accepts a username, an email address, or a NIP stored as the username.
 *
 * @internal
 *
 * @coversNothing
 */
final class IdentifierLoginTest extends DatabaseTestCase
{
    protected bool $useTransaction = false;

    /** @var list<string> */
    private array $usernames = [];

    protected function tearDown(): void
    {
        if (isset($this->pdo)) {
            foreach ($this->usernames as $username) {
                $this->pdo->prepare('DELETE FROM auth_events WHERE username = :u OR username = :e')
                    ->execute(['u' => $username, 'e' => $username.'@example.test'])
                ;
                $this->pdo->prepare(
                    'DELETE FROM sessions WHERE user_id IN (SELECT id FROM users WHERE username = :u)'
                )->execute(['u' => $username]);
                $this->pdo->prepare(
                    'DELETE FROM audit_logs WHERE table_name = \'users\' AND row_id IN (SELECT id FROM users WHERE username = :u)'
                )->execute(['u' => $username]);
                $this->pdo->prepare('DELETE FROM users WHERE username = :u')->execute(['u' => $username]);
            }
        }

        parent::tearDown();
    }

    public function testLoginByUsernameStillWorks(): void
    {
        $this->makeUser('login-user', 'login-user@example.test', 'E2e-Pass-123');

        $result = \authenticate('login-user', 'E2e-Pass-123');

        self::assertTrue($result['ok']);
        self::assertSame('login-user', $result['user']['username']);
    }

    public function testLoginByEmailIsCaseInsensitive(): void
    {
        $this->makeUser('login-email', 'login-email@example.test', 'E2e-Pass-123');

        $result = \authenticate('Login-Email@Example.TEST', 'E2e-Pass-123');

        self::assertTrue($result['ok']);
        self::assertSame('login-email', $result['user']['username']);
    }

    public function testLoginWorksForANumericNipUsername(): void
    {
        $this->makeUser('254107020102', null, 'E2e-Pass-123');

        $result = \authenticate('254107020102', 'E2e-Pass-123');

        self::assertTrue($result['ok']);
        self::assertSame('254107020102', $result['user']['username']);
    }

    public function testUnknownEmailLooksExactlyLikeUnknownUsername(): void
    {
        $byUsername = \authenticate('no-such-account', 'E2e-Pass-123');
        $byEmail = \authenticate('no-such-account@example.test', 'E2e-Pass-123');

        self::assertSame($byUsername, $byEmail);
        self::assertFalse($byEmail['ok']);
        self::assertNull($byEmail['user']);
        self::assertFalse($byEmail['locked']);
    }

    public function testAnExactUsernameWinsOverAnotherAccountsEmail(): void
    {
        // Both accounts collide on the same identifier: one as username, the
        // other as email. The username owner must win deterministically.
        $this->makeUser('collide@example.test', null, 'Username-Pass-123');
        $this->makeUser('collide-owner', 'collide@example.test', 'Email-Pass-123');

        $byUsername = \authenticate('collide@example.test', 'Username-Pass-123');
        $byEmailPassword = \authenticate('collide@example.test', 'Email-Pass-123');

        self::assertTrue($byUsername['ok']);
        self::assertSame('collide@example.test', $byUsername['user']['username']);
        self::assertFalse($byEmailPassword['ok']);
    }

    public function testCreateUserStoresALowercasedEmail(): void
    {
        $result = \create_user(
            'case-user',
            'Case User',
            'perawat',
            'E2e-Pass-123',
            $this->adminId(),
            '  Case-User@Example.Test  ',
        );
        $this->usernames[] = 'case-user';

        self::assertTrue($result['ok'], (string) $result['error']);

        $stmt = $this->pdo->prepare('SELECT email FROM users WHERE username = :u');
        $stmt->execute(['u' => 'case-user']);

        self::assertSame('case-user@example.test', $stmt->fetchColumn());
    }

    public function testCreateUserRejectsAnInvalidEmail(): void
    {
        $result = \create_user('bad-email', 'Bad Email', 'perawat', 'E2e-Pass-123', $this->adminId(), 'not-an-email');

        self::assertFalse($result['ok']);
        self::assertSame('Format surel tidak valid.', $result['error']);

        self::assertSame(0, $this->countUsers('bad-email'));
    }

    public function testCreateUserRejectsADuplicateEmail(): void
    {
        $this->makeUser('dup-owner', 'dup@example.test', 'E2e-Pass-123');

        $result = \create_user('dup-second', 'Dup Second', 'perawat', 'E2e-Pass-123', $this->adminId(), 'DUP@example.test');

        self::assertFalse($result['ok']);
        self::assertSame('Surel sudah dipakai.', $result['error']);
        self::assertSame(0, $this->countUsers('dup-second'));
    }

    public function testAccountsWithoutEmailDoNotCollide(): void
    {
        $first = $this->makeUser('no-email-a', null, 'E2e-Pass-123');
        $second = $this->makeUser('no-email-b', null, 'E2e-Pass-123');

        self::assertTrue($first['ok'], (string) $first['error']);
        self::assertTrue($second['ok'], (string) $second['error']);
    }

    /**
     * @return array{ok: bool, error: ?string}
     */
    private function makeUser(string $username, ?string $email, string $password): array
    {
        $result = \create_user($username, 'Test '.$username, 'perawat', $password, $this->adminId(), $email ?? '');
        $this->usernames[] = $username;

        self::assertTrue($result['ok'], (string) $result['error']);

        return $result;
    }

    private function countUsers(string $username): int
    {
        $stmt = $this->pdo->prepare('SELECT count(*) FROM users WHERE username = :u');
        $stmt->execute(['u' => $username]);

        return (int) $stmt->fetchColumn();
    }
}
