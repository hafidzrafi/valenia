<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class QueueBoardTest extends DatabaseTestCase
{
    public function testQueueBoardsReturnsEveryActiveService(): void
    {
        $boards = \queue_boards();
        $names = array_column($boards, 'service_name');

        self::assertContains('Poli Umum', $names);
        self::assertContains('Poli Gigi', $names);
        self::assertContains('Konseling/Psikologi', $names);

        foreach ($boards as $board) {
            self::assertNull($board['current_queue_code']);
            self::assertSame(0, (int) $board['waiting_count']);
        }
    }

    public function testAdvanceQueuePicksTheOldestWaitingVisit(): void
    {
        $patient = $this->createPatient('920000000001');
        $service = $this->serviceId('Poli Umum');

        $first = \issue_guest_ticket($patient, $service, 'Demam');
        $secondPatient = $this->createPatient('920000000101');
        $second = \issue_guest_ticket($secondPatient, $service, 'Batuk');

        $result = \advance_queue($service, $this->adminId());

        self::assertTrue($result['advanced']);
        self::assertSame($first['queue_code'], $result['queue_code']);

        $stmt = $this->pdo->prepare('SELECT status, served_at FROM visits WHERE id = :id');
        $stmt->execute(['id' => $first['visit_id']]);
        $row = $stmt->fetch();

        self::assertIsArray($row);
        self::assertSame('dilayani', $row['status']);
        self::assertNotNull($row['served_at']);

        $board = $this->boardFor('Poli Umum');
        self::assertSame($first['queue_code'], $board['current_queue_code']);
        self::assertSame(1, (int) $board['waiting_count']);
        self::assertSame($second['queue_code'], $board['next_queue_code']);
    }

    public function testAdvanceQueueReturnsFalseWithNoWaitingVisits(): void
    {
        $result = \advance_queue($this->serviceId('Poli Gigi'), $this->adminId());

        self::assertFalse($result['advanced']);
        self::assertNull($result['queue_code']);
    }

    public function testQueueBoardShowsNewestServedVisit(): void
    {
        $patient = $this->createPatient('920000000002');
        $service = $this->serviceId('Poli Umum');

        $first = \issue_guest_ticket($patient, $service, 'Demam');
        $secondPatient = $this->createPatient('920000000102');
        $second = \issue_guest_ticket($secondPatient, $service, 'Batuk');

        \advance_queue($service, $this->adminId());
        \advance_queue($service, $this->adminId());

        $board = $this->boardFor('Poli Umum');

        self::assertSame($second['queue_code'], $board['current_queue_code']);
        self::assertNotSame($first['queue_code'], $board['current_queue_code']);
        self::assertSame(0, (int) $board['waiting_count']);
    }

    public function testQueueBoardFallsBackToCreatedAtWithoutServedAt(): void
    {
        $patient = $this->createPatient('920000000003');
        $service = $this->serviceId('Poli Umum');
        $ticket = \issue_guest_ticket($patient, $service, 'Demam');

        $this->pdo->prepare("UPDATE visits SET status = 'dilayani', served_at = NULL WHERE id = :id")
            ->execute(['id' => $ticket['visit_id']])
        ;

        $board = $this->boardFor('Poli Umum');

        self::assertSame($ticket['queue_code'], $board['current_queue_code']);
    }

    public function testInactiveServicesAreExcluded(): void
    {
        $this->pdo->prepare('UPDATE services SET is_active = FALSE WHERE name = :name')
            ->execute(['name' => 'Poli Gigi'])
        ;

        $names = array_column(\queue_boards(), 'service_name');

        self::assertNotContains('Poli Gigi', $names);
        self::assertContains('Poli Umum', $names);
        self::assertContains('Konseling/Psikologi', $names);
    }

    /**
     * @return array<string, mixed>
     */
    private function boardFor(string $serviceName): array
    {
        foreach (\queue_boards() as $board) {
            if ($serviceName === $board['service_name']) {
                return $board;
            }
        }

        self::fail('Board not found for '.$serviceName);
    }
}
