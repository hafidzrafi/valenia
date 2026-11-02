<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    protected \PDO $pdo;

    /**
     * Most tests run inside a transaction. Tests for services that manage
     * their own transactions (auth) disable this and clean up manually.
     */
    protected bool $useTransaction = true;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\test_database_enabled()) {
            self::markTestSkipped('Set RUN_DB_TESTS=1 with a migrated valenia_test database.');
        }

        $this->pdo = \db();

        if ($this->useTransaction) {
            $this->pdo->beginTransaction();
        }
    }

    protected function tearDown(): void
    {
        if (isset($this->pdo) && $this->useTransaction && $this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        parent::tearDown();
    }

    protected function adminId(): string
    {
        $id = $this->pdo->query("SELECT id FROM users WHERE username = 'admin'")->fetchColumn();

        self::assertIsString($id, 'The seed must create the admin account.');

        return $id;
    }

    protected function serviceId(string $name = 'Poli Umum'): int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM services WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $id = $stmt->fetchColumn();

        self::assertIsNumeric($id, 'The seed must create the requested service.');

        return (int) $id;
    }

    protected function createPatient(string $identityNumber = '123456789012'): string
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO patients (identity_type, identity_number, full_name, birth_date, gender)
             VALUES ('NIM', :number, 'Test Patient', '2000-01-01', 'L')
             RETURNING id"
        );
        $stmt->execute(['number' => $identityNumber]);
        $id = $stmt->fetchColumn();

        self::assertIsString($id);

        return $id;
    }
}
