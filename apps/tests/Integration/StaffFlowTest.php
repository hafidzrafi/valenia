<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * Staff dashboard, attendance, and visit completion.
 *
 * These run outside the default wrapping transaction because
 * complete_staff_visit() manages its own transaction. Cleanup is explicit.
 *
 * @internal
 *
 * @coversNothing
 */
final class StaffFlowTest extends DatabaseTestCase
{
    protected bool $useTransaction = false;

    /** @var list<string> */
    private array $patientIds = [];

    /** @var list<string> */
    private array $visitIds = [];

    /** @var list<string> */
    private array $doctorIds = [];

    protected function tearDown(): void
    {
        if (isset($this->pdo)) {
            foreach ($this->visitIds as $visitId) {
                $this->pdo->prepare('DELETE FROM examinations WHERE visit_id = :id')->execute(['id' => $visitId]);
                $this->pdo->prepare("DELETE FROM audit_logs WHERE table_name = 'visits' AND row_id = :id")->execute(['id' => $visitId]);
                $this->pdo->prepare('DELETE FROM visits WHERE id = :id')->execute(['id' => $visitId]);
            }

            foreach ($this->patientIds as $patientId) {
                $this->pdo->prepare('DELETE FROM patients WHERE id = :id')->execute(['id' => $patientId]);
            }

            foreach ($this->doctorIds as $doctorId) {
                $this->pdo->prepare('DELETE FROM doctor_attendance WHERE doctor_id = :id')->execute(['id' => $doctorId]);
            }

            $this->pdo->exec(
                'DELETE FROM queue_counters
                 WHERE queue_date = app_today()
                   AND NOT EXISTS (
                       SELECT 1 FROM visits v
                       WHERE v.service_id = queue_counters.service_id
                         AND v.queue_date = queue_counters.queue_date
                   )'
            );
        }

        parent::tearDown();
    }

    public function testTodayVisitsExposeQueueCodeAndPatient(): void
    {
        $patient = $this->patient('930000000001', 'Staff Row');
        $service = $this->serviceId('Poli Umum');
        $ticket = \issue_guest_ticket($patient, $service, 'Demam');
        $this->visitIds[] = (string) $ticket['visit_id'];

        $visits = \staff_today_visits();
        $row = $this->findVisit($visits, (string) $ticket['visit_id']);

        self::assertNotNull($row);
        self::assertSame($ticket['queue_code'], $row['queue_code']);
        self::assertSame('Staff Row', $row['full_name']);
        self::assertSame('Poli Umum', $row['service_name']);
        self::assertSame('menunggu', $row['status']);
    }

    public function testSummaryCountsByService(): void
    {
        $patient = $this->patient('930000000002', 'Staff Summary');
        $umum = \issue_guest_ticket($patient, $this->serviceId('Poli Umum'), 'Demam');
        $gigi = \issue_guest_ticket($patient, $this->serviceId('Poli Gigi'), 'Sakit gigi');
        $this->visitIds[] = (string) $umum['visit_id'];
        $this->visitIds[] = (string) $gigi['visit_id'];

        $summary = \staff_summary();

        self::assertSame(2, $summary['total']);
        self::assertSame(1, $summary['by_service']['Poli Umum']);
        self::assertSame(1, $summary['by_service']['Poli Gigi']);
    }

    public function testCancelledVisitsAreExcludedFromTheDashboard(): void
    {
        $patient = $this->patient('930000000003', 'Staff Cancel');
        $ticket = \issue_guest_ticket($patient, $this->serviceId('Poli Umum'), 'Demam');
        $this->visitIds[] = (string) $ticket['visit_id'];

        self::assertTrue(\cancel_portal_visit($patient, (string) $ticket['visit_id']));
        self::assertNull($this->findVisit(\staff_today_visits(), (string) $ticket['visit_id']));
    }

    public function testAttendanceUpsertKeepsOneRowPerDoctorPerDay(): void
    {
        $doctorId = $this->firstDoctorId();
        $this->doctorIds[] = $doctorId;

        self::assertTrue(\set_staff_attendance($doctorId, true, $this->adminId()));
        self::assertTrue(\set_staff_attendance($doctorId, false, $this->adminId()));

        $stmt = $this->pdo->prepare(
            'SELECT count(*) AS rows, bool_and(NOT is_present) AS all_absent
             FROM doctor_attendance
             WHERE doctor_id = :id AND attendance_date = app_today()'
        );
        $stmt->execute(['id' => $doctorId]);
        $row = $stmt->fetch();

        self::assertSame(1, (int) $row['rows']);
        self::assertTrue((bool) $row['all_absent']);
    }

    public function testAttendanceRejectsUnknownDoctor(): void
    {
        self::assertFalse(\set_staff_attendance('00000000-0000-0000-0000-000000000000', true, $this->adminId()));
    }

    public function testCompleteVisitWritesExaminationAndClosesTheVisit(): void
    {
        $patient = $this->patient('930000000004', 'Staff Complete');
        $service = $this->serviceId('Poli Umum');
        $ticket = \issue_guest_ticket($patient, $service, 'Demam');
        $this->visitIds[] = (string) $ticket['visit_id'];

        \advance_queue($service, $this->adminId());

        $result = \complete_staff_visit((string) $ticket['visit_id'], $this->adminId());
        self::assertTrue($result['ok'], (string) $result['error']);

        $stmt = $this->pdo->prepare('SELECT status FROM visits WHERE id = :id');
        $stmt->execute(['id' => $ticket['visit_id']]);
        self::assertSame('selesai', $stmt->fetchColumn());

        $exam = $this->pdo->prepare(
            'SELECT examiner_role, doctor_id, recorded_by FROM examinations WHERE visit_id = :id'
        );
        $exam->execute(['id' => $ticket['visit_id']]);
        $row = $exam->fetch();

        self::assertIsArray($row);
        self::assertSame('perawat', $row['examiner_role']);
        self::assertNull($row['doctor_id']);
        self::assertSame($this->adminId(), (string) $row['recorded_by']);
    }

    public function testCompleteVisitRejectsAWatingVisit(): void
    {
        $patient = $this->patient('930000000005', 'Staff Waiting');
        $ticket = \issue_guest_ticket($patient, $this->serviceId('Poli Umum'), 'Demam');
        $this->visitIds[] = (string) $ticket['visit_id'];

        $result = \complete_staff_visit((string) $ticket['visit_id'], $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Panggil antrean terlebih dahulu sebelum menyelesaikan.', $result['error']);

        $stmt = $this->pdo->prepare('SELECT count(*) FROM examinations WHERE visit_id = :id');
        $stmt->execute(['id' => $ticket['visit_id']]);
        self::assertSame(0, (int) $stmt->fetchColumn());
    }

    public function testDoubleCompletionIsRejectedAndKeepsOneExamination(): void
    {
        $patient = $this->patient('930000000008', 'Staff Double');
        $service = $this->serviceId('Poli Umum');
        $ticket = \issue_guest_ticket($patient, $service, 'Demam');
        $this->visitIds[] = (string) $ticket['visit_id'];

        \advance_queue($service, $this->adminId());

        $first = \complete_staff_visit((string) $ticket['visit_id'], $this->adminId());
        $second = \complete_staff_visit((string) $ticket['visit_id'], $this->adminId());

        self::assertTrue($first['ok'], (string) $first['error']);
        self::assertFalse($second['ok']);
        self::assertSame('Antrean ini sudah diselesaikan.', $second['error']);

        $stmt = $this->pdo->prepare('SELECT count(*) FROM examinations WHERE visit_id = :id');
        $stmt->execute(['id' => $ticket['visit_id']]);
        self::assertSame(1, (int) $stmt->fetchColumn());
    }

    public function testCompletionRejectsAVisitFromAnotherDay(): void
    {
        $patient = $this->patient('930000000009', 'Staff Yesterday');
        $service = $this->serviceId('Poli Gigi');

        $stmt = $this->pdo->prepare(
            "INSERT INTO visits (patient_id, service_id, queue_number, queue_date, status, served_at, created_by, registration_source)
             VALUES (:patient, :service, 901, app_today() - 1, 'dilayani', now(), :actor, 'staff')
             RETURNING id"
        );
        $stmt->execute([
            'patient' => $patient,
            'service' => $service,
            'actor' => $this->adminId(),
        ]);
        $visitId = (string) $stmt->fetchColumn();
        $this->visitIds[] = $visitId;

        $result = \complete_staff_visit($visitId, $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Hanya antrean hari ini yang dapat diselesaikan.', $result['error']);

        $check = $this->pdo->prepare('SELECT status FROM visits WHERE id = :id');
        $check->execute(['id' => $visitId]);
        self::assertSame('dilayani', $check->fetchColumn());
    }

    public function testActivityFallsBackWhenTheActorNoLongerExists(): void
    {
        $tempUserId = $this->temporaryUser('activity-actor');
        $patient = $this->patient('930000000010', 'Staff Actor');
        $service = $this->serviceId('Konseling/Psikologi');

        $ticket = \issue_guest_ticket($patient, $service, 'Konsultasi');
        $this->visitIds[] = (string) $ticket['visit_id'];

        \advance_queue($service, $tempUserId);

        $this->pdo->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $tempUserId]);

        $events = array_values(array_filter(
            \staff_queue_activity(),
            static fn (array $event): bool => $event['code'] === $ticket['queue_code'] && 'Dipanggil' === $event['event']
        ));

        self::assertCount(1, $events);
        self::assertSame('akun dihapus', $events[0]['actor']);
    }

    public function testBoardKeepsTheLastServedCodeAfterCompletion(): void
    {
        $patient = $this->patient('930000000007', 'Staff Board');
        $service = $this->serviceId('Poli Umum');
        $ticket = \issue_guest_ticket($patient, $service, 'Demam');
        $this->visitIds[] = (string) $ticket['visit_id'];

        \advance_queue($service, $this->adminId());
        $result = \complete_staff_visit((string) $ticket['visit_id'], $this->adminId());
        self::assertTrue($result['ok'], (string) $result['error']);

        $board = $this->board('Poli Umum');

        self::assertSame($ticket['queue_code'], $board['current_queue_code']);
        self::assertSame(0, (int) $board['waiting_count']);
    }

    public function testQueueActivityRecordsTransitionsWithActor(): void
    {
        $patient = $this->patient('930000000006', 'Staff Activity');
        $service = $this->serviceId('Poli Umum');
        $ticket = \issue_guest_ticket($patient, $service, 'Demam');
        $this->visitIds[] = (string) $ticket['visit_id'];

        \advance_queue($service, $this->adminId());
        \complete_staff_visit((string) $ticket['visit_id'], $this->adminId());

        $events = array_values(array_filter(
            \staff_queue_activity(),
            static fn (array $event): bool => $event['code'] === $ticket['queue_code']
        ));

        $labels = array_column($events, 'event');
        self::assertContains('Didaftarkan', $labels);
        self::assertContains('Dipanggil', $labels);
        self::assertContains('Selesai', $labels);

        /** @var array{time: string, code: string, patient: string, event: string, actor: string} $called */
        $called = array_values(array_filter($events, static fn (array $event): bool => 'Dipanggil' === $event['event']))[0];
        self::assertSame('Staff Activity', $called['patient']);
    }

    public function testConcurrentAdvanceServesDistinctTickets(): void
    {
        $patient = $this->patient('930000000011', 'Staff Concurrent');
        $service = $this->serviceId('Poli Umum');

        $first = \issue_guest_ticket($patient, $service, 'Demam');
        $second = \issue_guest_ticket($patient, $service, 'Batuk');
        $this->visitIds[] = (string) $first['visit_id'];
        $this->visitIds[] = (string) $second['visit_id'];

        $locked = $this->newConnection();
        $other = $this->newConnection();

        try {
            // Hold the oldest waiting row open so the second connection has to
            // skip it. This is exactly the situation FOR UPDATE SKIP LOCKED
            // exists for: two staff pressing the button at the same moment.
            $locked->beginTransaction();

            $statement = $locked->prepare('SELECT advanced, queue_code, visit_id FROM advance_queue(:service, :actor)');
            $statement->execute(['service' => $service, 'actor' => $this->adminId()]);
            $firstResult = $statement->fetch();
            $statement->closeCursor();

            $statement = $other->prepare('SELECT advanced, queue_code, visit_id FROM advance_queue(:service, :actor)');
            $statement->execute(['service' => $service, 'actor' => $this->adminId()]);
            $secondResult = $statement->fetch();
            $statement->closeCursor();

            self::assertIsArray($firstResult);
            self::assertIsArray($secondResult);
            self::assertTrue((bool) $firstResult['advanced']);
            self::assertTrue((bool) $secondResult['advanced']);
            self::assertNotSame($firstResult['queue_code'], $secondResult['queue_code']);

            $locked->commit();
        } finally {
            if ($locked->inTransaction()) {
                $locked->rollBack();
            }
        }

        $stmt = $this->pdo->prepare(
            "SELECT count(*) FROM visits WHERE service_id = :service AND queue_date = app_today()
             AND status = 'dilayani' AND id = ANY(:ids)"
        );
        $stmt->execute([
            'service' => $service,
            'ids' => '{'.$first['visit_id'].','.$second['visit_id'].'}',
        ]);

        self::assertSame(2, (int) $stmt->fetchColumn());

        // Nothing left to serve, so a third call must report failure.
        self::assertFalse(\advance_queue($service, $this->adminId())['advanced']);
    }

    public function testActivityShowsTheCancellationLabel(): void
    {
        $patient = $this->patient('930000000012', 'Staff Cancelled');
        $ticket = \issue_guest_ticket($patient, $this->serviceId('Poli Gigi'), 'Sakit gigi');
        $this->visitIds[] = (string) $ticket['visit_id'];

        self::assertTrue(\cancel_portal_visit($patient, (string) $ticket['visit_id']));

        $events = array_values(array_filter(
            \staff_queue_activity(),
            static fn (array $event): bool => $event['code'] === $ticket['queue_code'] && 'Dibatalkan' === $event['event']
        ));

        self::assertCount(1, $events);
        self::assertSame('Staff Cancelled', $events[0]['patient']);
    }

    public function testThreeDigitQueueNumbersAreNotTruncated(): void
    {
        $patient = $this->patient('930000000013', 'Staff Wide Code');
        $service = $this->serviceId('Poli Gigi');

        $stmt = $this->pdo->prepare(
            "INSERT INTO visits (patient_id, service_id, queue_number, queue_date, status, created_by, registration_source)
             VALUES (:patient, :service, 105, app_today(), 'menunggu', :actor, 'staff')
             RETURNING id"
        );
        $stmt->execute([
            'patient' => $patient,
            'service' => $service,
            'actor' => $this->adminId(),
        ]);
        $this->visitIds[] = (string) $stmt->fetchColumn();

        $board = $this->board('Poli Gigi');

        self::assertSame(105, (int) $board['next_queue_number']);
        self::assertSame('PG-105', $board['next_queue_code']);
    }

    /**
     * @param list<array<string, mixed>> $visits
     *
     * @return null|array<string, mixed>
     */
    private function findVisit(array $visits, string $visitId): ?array
    {
        foreach ($visits as $visit) {
            if ((string) $visit['id'] === $visitId) {
                return $visit;
            }
        }

        return null;
    }

    private function patient(string $identityNumber, string $fullName): string
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO patients (identity_type, identity_number, full_name, birth_date, gender)
             VALUES ('NIM', :number, :name, '2000-01-01', 'L')
             RETURNING id"
        );
        $stmt->execute(['number' => $identityNumber, 'name' => $fullName]);
        $id = (string) $stmt->fetchColumn();
        $this->patientIds[] = $id;

        return $id;
    }

    private function firstDoctorId(): string
    {
        $id = $this->pdo->query('SELECT id FROM doctors ORDER BY id LIMIT 1')->fetchColumn();
        self::assertIsString($id, 'The seed must create at least one doctor.');

        return $id;
    }

    /** Create a throwaway perawat account and return its id. */
    private function temporaryUser(string $prefix): string
    {
        $username = $prefix.'-'.bin2hex(random_bytes(4));

        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, full_name, role) VALUES (:username, 'Temp Staff', 'perawat') RETURNING id"
        );
        $stmt->execute(['username' => $username]);
        $id = (string) $stmt->fetchColumn();

        $this->pdo->prepare(
            "INSERT INTO credentials (user_id, password_hash, must_change_password) VALUES (:id, 'x', FALSE)"
        )->execute(['id' => $id]);

        return $id;
    }

    /**
     * @return array<string, mixed>
     */
    private function board(string $serviceName): array
    {
        foreach (\queue_boards() as $board) {
            if ((string) $board['service_name'] === $serviceName) {
                return $board;
            }
        }

        self::fail('Board not found for '.$serviceName);
    }

    /** A second connection so two transactions can overlap. */
    private function newConnection(): \PDO
    {
        return new \PDO(
            (string) \env('DB_DSN'),
            (string) \env('DB_USER'),
            (string) \env('DB_PASSWORD'),
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION],
        );
    }
}
