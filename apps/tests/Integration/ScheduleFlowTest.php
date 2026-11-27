<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * Weekly schedule reads and writes against the real database. They opt out of
 * the wrapping transaction because the library manages its own.
 *
 * @internal
 *
 * @coversNothing
 */
final class ScheduleFlowTest extends DatabaseTestCase
{
    protected bool $useTransaction = false;

    /** @var list<string> */
    private array $slotIds = [];

    /** @var list<string> */
    private array $doctorIds = [];

    protected function tearDown(): void
    {
        if (isset($this->pdo)) {
            foreach ($this->slotIds as $slotId) {
                $audit = $this->pdo->prepare(
                    "DELETE FROM audit_logs WHERE table_name = 'doctor_schedules' AND row_id = :id"
                );
                $audit->execute(['id' => $slotId]);
                $this->pdo->prepare('DELETE FROM doctor_schedules WHERE id = :id')->execute(['id' => $slotId]);
            }

            foreach ($this->doctorIds as $doctorId) {
                $this->pdo->prepare('DELETE FROM doctor_schedules WHERE doctor_id = :id')->execute(['id' => $doctorId]);
                $this->pdo->prepare('DELETE FROM doctors WHERE id = :id')->execute(['id' => $doctorId]);
            }
        }

        parent::tearDown();
    }

    public function testReadGroupsSlotsPerWeekdayAndSkipsInactiveDoctors(): void
    {
        $active = $this->createDoctor('dr. Jadwal Aktif', true);
        $inactive = $this->createDoctor('dr. Jadwal Nonaktif', false);

        $first = \create_schedule_slot($active, 3, '08:00', '12:00', $this->adminId());
        self::assertTrue($first['ok'], (string) $first['error']);
        $this->slotIds[] = $this->lastSlotId($active);

        $second = \create_schedule_slot($active, 3, '13:00', '15:00', $this->adminId());
        self::assertTrue($second['ok'], (string) $second['error']);
        $this->slotIds[] = $this->lastSlotId($active);

        $schedule = \list_weekly_schedule();
        $row = $this->rowFor($schedule, $active);

        self::assertNotNull($row, 'The active doctor must appear in the schedule.');
        self::assertCount(2, $row['slots'][3]);
        self::assertSame('08:00', $row['slots'][3][0]['start_time']);
        self::assertSame('15:00', $row['slots'][3][1]['end_time']);
        self::assertNull($this->rowFor($schedule, $inactive), 'Inactive doctors are not schedulable.');
    }

    public function testCreateWritesTheSlotAndTheAuditRow(): void
    {
        $doctor = $this->createDoctor('dr. Jadwal Baru', true);

        $result = \create_schedule_slot($doctor, 5, '09:00', '13:00', $this->adminId());
        self::assertTrue($result['ok'], (string) $result['error']);

        $slotId = $this->lastSlotId($doctor);
        $this->slotIds[] = $slotId;

        $stmt = $this->pdo->prepare('SELECT day_of_week, start_time, end_time FROM doctor_schedules WHERE id = :id');
        $stmt->execute(['id' => $slotId]);
        $slot = $stmt->fetch();

        self::assertIsArray($slot);
        self::assertSame(5, (int) $slot['day_of_week']);
        self::assertSame('09:00', substr((string) $slot['start_time'], 0, 5));
        self::assertSame('13:00', substr((string) $slot['end_time'], 0, 5));

        self::assertSame('INSERT', $this->auditAction($slotId));
    }

    public function testCreateRejectsAnInactiveDoctor(): void
    {
        $doctor = $this->createDoctor('dr. Jadwal Nonaktif', false);

        $result = \create_schedule_slot($doctor, 1, '08:00', '15:00', $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Tenaga medis tidak ditemukan atau nonaktif.', $result['error']);
        self::assertSame(0, $this->slotCount($doctor));
    }

    public function testCreateRejectsASlotThatOverlapsAnExistingOne(): void
    {
        $doctor = $this->createDoctor('dr. Jadwal Bentrok', true);
        \create_schedule_slot($doctor, 3, '09:00', '12:00', $this->adminId());
        $this->slotIds[] = $this->lastSlotId($doctor);

        $result = \create_schedule_slot($doctor, 3, '11:00', '13:00', $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Jadwal bertabrakan dengan slot lain di hari yang sama.', $result['error']);
        self::assertSame(1, $this->slotCount($doctor), 'The clashing slot must not be written.');
    }

    public function testCreateAcceptsAdjacentAndDifferentDaySlots(): void
    {
        $doctor = $this->createDoctor('dr. Jadwal Bersebelahan', true);
        \create_schedule_slot($doctor, 3, '09:00', '12:00', $this->adminId());
        $this->slotIds[] = $this->lastSlotId($doctor);

        $adjacent = \create_schedule_slot($doctor, 3, '12:00', '15:00', $this->adminId());
        self::assertTrue($adjacent['ok'], (string) $adjacent['error']);
        $this->slotIds[] = $this->lastSlotId($doctor);

        $otherDay = \create_schedule_slot($doctor, 4, '09:00', '12:00', $this->adminId());
        self::assertTrue($otherDay['ok'], (string) $otherDay['error']);
        $this->slotIds[] = $this->lastSlotId($doctor);

        self::assertSame(3, $this->slotCount($doctor));
    }

    public function testDeleteRemovesTheSlotAndAuditsIt(): void
    {
        $doctor = $this->createDoctor('dr. Jadwal Hapus', true);
        \create_schedule_slot($doctor, 2, '10:00', '11:00', $this->adminId());
        $slotId = $this->lastSlotId($doctor);

        $result = \delete_schedule_slot($slotId, $this->adminId());

        self::assertTrue($result['ok'], (string) $result['error']);
        self::assertSame(0, $this->slotCount($doctor));
        self::assertSame('DELETE', $this->auditAction($slotId));
        $this->slotIds[] = $slotId;
    }

    public function testDeleteRejectsAnUnknownSlot(): void
    {
        $result = \delete_schedule_slot('0192f0a0-0000-7000-8000-000000000000', $this->adminId());

        self::assertFalse($result['ok']);
        self::assertSame('Jadwal tidak ditemukan.', $result['error']);
    }

    public function testPageRendersTheMatrixForARealDoctor(): void
    {
        $doctor = $this->createDoctor('dr. Halaman Jadwal', true);
        \create_schedule_slot($doctor, 4, '07:30', '12:00', $this->adminId());
        $this->slotIds[] = $this->lastSlotId($doctor);

        /** @var array<string, callable> $route */
        $route = require \app_path('app/routes/admin.jadwal.php');

        $html = $route['GET'](new \Request('GET', 'admin/jadwal', [], [
            'user_id' => $this->adminId(),
            'user' => 'admin',
            'role' => 'admin',
        ]));

        self::assertIsString($html);
        self::assertStringContainsString('dr. Halaman Jadwal', $html);
        self::assertStringContainsString('07:30-12:00', $html);
        self::assertStringContainsString('name="aksi" value="tambah-jadwal"', $html);
    }

    public function testPostAddsASlotAndRedirectsWithAFlash(): void
    {
        $doctor = $this->createDoctor('dr. Posting Jadwal', true);
        $_SESSION = [];
        $_GET = [];
        $_POST = [
            'aksi' => 'tambah-jadwal',
            'doctor_id' => $doctor,
            'day_of_week' => '6',
            'start_time' => '09:00',
            'end_time' => '11:30',
        ];

        /** @var array<string, callable> $route */
        $route = require \app_path('app/routes/admin.jadwal.php');

        $response = $route['POST'](new \Request('POST', 'admin/jadwal', [], [
            'user_id' => $this->adminId(),
            'user' => 'admin',
            'role' => 'admin',
        ]));

        $_POST = [];

        self::assertInstanceOf(\Response::class, $response);
        self::assertSame(302, $response->status);
        self::assertSame('/admin/jadwal', $response->headers['Location'] ?? null);
        self::assertSame('Jadwal ditambahkan.', \take_staff_flash());
        self::assertSame(1, $this->slotCount($doctor));
        $this->slotIds[] = $this->lastSlotId($doctor);
    }

    private function createDoctor(string $fullName, bool $active): string
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO doctors (full_name, service_id, is_active)
             VALUES (:name, :service, :active)
             RETURNING id'
        );
        $stmt->execute([
            'name' => $fullName,
            'service' => $this->serviceId('Poli Umum'),
            'active' => $active ? 'true' : 'false',
        ]);
        $id = (string) $stmt->fetchColumn();
        $this->doctorIds[] = $id;

        return $id;
    }

    private function lastSlotId(string $doctorId): string
    {
        $stmt = $this->pdo->prepare(
            'SELECT id FROM doctor_schedules WHERE doctor_id = :id ORDER BY start_time DESC, id DESC LIMIT 1'
        );
        $stmt->execute(['id' => $doctorId]);

        return (string) $stmt->fetchColumn();
    }

    private function slotCount(string $doctorId): int
    {
        $stmt = $this->pdo->prepare('SELECT count(*) FROM doctor_schedules WHERE doctor_id = :id');
        $stmt->execute(['id' => $doctorId]);

        return (int) $stmt->fetchColumn();
    }

    private function auditAction(string $slotId): ?string
    {
        $stmt = $this->pdo->prepare(
            "SELECT action FROM audit_logs
             WHERE table_name = 'doctor_schedules' AND row_id = :id
             ORDER BY created_at DESC, id DESC LIMIT 1"
        );
        $stmt->execute(['id' => $slotId]);
        $action = $stmt->fetchColumn();

        return false === $action ? null : (string) $action;
    }

    /**
     * @param list<array<string, mixed>> $schedule
     *
     * @return null|array<string, mixed>
     */
    private function rowFor(array $schedule, string $doctorId): ?array
    {
        foreach ($schedule as $row) {
            if ((string) $row['id'] === $doctorId) {
                return $row;
            }
        }

        return null;
    }
}
