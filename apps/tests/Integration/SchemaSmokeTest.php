<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class SchemaSmokeTest extends DatabaseTestCase
{
    public function testMigrationsTableContainsTheCoreSchema(): void
    {
        $count = (int) $this->pdo->query('SELECT count(*) FROM schema_migrations')->fetchColumn();

        self::assertGreaterThanOrEqual(10, $count);
    }

    public function testCoreTablesExist(): void
    {
        $tables = $this->pdo->query(
            "SELECT tablename FROM pg_tables WHERE schemaname = 'public'"
        )->fetchAll();

        $names = array_column($tables, 'tablename');

        foreach (['users', 'patients', 'services', 'visits', 'queue_counters', 'examinations'] as $table) {
            self::assertContains($table, $names);
        }
    }

    public function testSeededServicesExist(): void
    {
        $names = $this->pdo->query('SELECT name FROM services ORDER BY name')->fetchAll();
        $names = array_column($names, 'name');

        self::assertContains('Poli Umum', $names);
        self::assertContains('Poli Gigi', $names);
        self::assertContains('Konseling/Psikologi', $names);
    }
}
