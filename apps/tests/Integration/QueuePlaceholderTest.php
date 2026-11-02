<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class QueuePlaceholderTest extends DatabaseTestCase
{
    public function testQueueNumbersIncreasePerService(): void
    {
        $patientId = $this->createPatient('123456789013');
        $serviceId = $this->serviceId();
        $adminId = $this->adminId();

        $stmt = $this->pdo->prepare('SELECT * FROM register_visit(:patient, :service, :user)');

        $stmt->execute(['patient' => $patientId, 'service' => $serviceId, 'user' => $adminId]);
        $first = $stmt->fetch();

        $stmt->execute(['patient' => $patientId, 'service' => $serviceId, 'user' => $adminId]);
        $second = $stmt->fetch();

        self::assertIsArray($first);
        self::assertIsArray($second);
        self::assertSame(
            (int) $first['queue_number'] + 1,
            (int) $second['queue_number'],
        );
        self::assertNotSame($first['visit_id'], $second['visit_id']);
    }
}
