<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * Admin edits to an existing account.
 *
 * @internal
 *
 * @coversNothing
 */
final class AccountManagementTest extends DatabaseTestCase
{
    protected bool $useTransaction = false;

    /** @var list<string> */
    private array $usernames = [];

    protected function tearDown(): void
    {
        if (isset($this->pdo)) {
            foreach ($this->usernames as $username) {
                $this->pdo->prepare(
                    'DELETE FROM audit_logs WHERE table_name = \'users\' AND row_id IN (SELECT id FROM users WHERE username = :u)'
                )->execute(['u' => $username]);
                $this->pdo->prepare(
                    'DELETE FROM credentials WHERE user_id IN (SELECT id FROM users WHERE username = :u)'
                )->execute(['u' => $username]);
                $this->pdo->prepare('DELETE FROM users WHERE username = :u')->execute(['u' => $username]);
            }
        }

        parent::tearDown();
    }

    public function testUpdateUserStoresTheNewFields(): void
    {
        $id = $this->makeUser('ubah-lama', 'ubah-lama@example.test');

        $result = \update_user($id, 'ubah-baru', 'Nama Baru', 'admin', 'Ubah-Baru@Example.Test', $this->adminId());

        self::assertTrue($result['ok'], (string) $result['error']);

        $stmt = $this->pdo->prepare('SELECT username, full_name, role, email FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        self::assertSame(
            ['username' => 'ubah-baru', 'full_name' => 'Nama Baru', 'role' => 'admin', 'email' => 'ubah-baru@example.test'],
            $stmt->fetch(\PDO::FETCH_ASSOC),
        );

        $this->usernames[] = 'ubah-baru';
    }

    public function testUpdateUserRejectsADuplicateUsername(): void
    {
        $id = $this->makeUser('ubah-dup', null);
        $this->makeUser('sudah-dipakai', null);

        $result = \update_user($id, 'sudah-dipakai', 'Nama Baru', 'perawat', '', $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Nama pengguna sudah dipakai.', $result['error']);
    }

    public function testUpdateUserRejectsADuplicateEmail(): void
    {
        $id = $this->makeUser('ubah-surel', null);
        $this->makeUser('pemilik-surel', 'dipakai@example.test');

        $result = \update_user($id, 'ubah-surel', 'Nama Baru', 'perawat', 'DIPAKAI@example.test', $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Surel sudah dipakai.', $result['error']);
    }

    public function testUpdateUserRefusesToChangeTheActingAdminsRole(): void
    {
        $result = \update_user($this->adminId(), 'admin', 'Administrator', 'perawat', '', $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Peran akun sendiri tidak dapat diubah.', $result['error']);

        $stmt = $this->pdo->prepare('SELECT role FROM users WHERE id = :id');
        $stmt->execute(['id' => $this->adminId()]);

        self::assertSame('admin', $stmt->fetchColumn());
    }

    public function testUpdateUserRejectsAnUnknownRole(): void
    {
        $id = $this->makeUser('ubah-peran', null);

        $result = \update_user($id, 'ubah-peran', 'Nama Baru', 'dokter', '', $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Peran tidak dikenal.', $result['error']);
    }

    public function testTemporaryPasswordAvoidsAmbiguousCharacters(): void
    {
        $password = \generate_temporary_password();

        self::assertSame(12, strlen($password));
        self::assertSame(0, preg_match('/[IlO01]/', $password));
        self::assertNotSame(\generate_temporary_password(), \generate_temporary_password());
    }

    private function makeUser(string $username, ?string $email): string
    {
        $result = \create_user($username, 'Test '.$username, 'perawat', 'Test-Pass-123', $this->adminId(), $email ?? '');
        $this->usernames[] = $username;

        self::assertTrue($result['ok'], (string) $result['error']);

        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = :u');
        $stmt->execute(['u' => $username]);

        return (string) $stmt->fetchColumn();
    }
}
