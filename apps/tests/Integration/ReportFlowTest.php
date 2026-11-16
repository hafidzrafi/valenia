<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * Monthly recap aggregation and the PDF download route.
 *
 * Visits are inserted on fixed dates in 1987 so today based suites cannot
 * change the counts. Everything runs inside the default wrapping transaction.
 *
 * @internal
 *
 * @coversNothing
 */
final class ReportFlowTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_POST = [];
        $_GET = [];
        \Auth::$user = null;
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];
        \Auth::$user = null;

        parent::tearDown();
    }

    public function testSnapshotBucketsVisitsIntoSevenDayWeeks(): void
    {
        $patient = $this->createPatient('940000000001');
        $umum = $this->serviceId('Poli Umum');

        $this->visitOn('1987-06-02', $umum, 'selesai', $patient);
        $this->visitOn('1987-06-09', $umum, 'selesai', $patient);
        $this->visitOn('1987-06-29', $umum, 'dilayani', $patient);

        $snapshot = \report_build_snapshot('1987-06-01', '1987-06-30', null);

        self::assertNull($snapshot['service']);
        self::assertCount(5, $snapshot['weeks']);
        self::assertSame(1, $snapshot['weeks'][0]['umum']);
        self::assertSame(1, $snapshot['weeks'][0]['total']);
        self::assertSame(1, $snapshot['weeks'][1]['umum']);
        self::assertSame(1, $snapshot['weeks'][4]['umum']);
        self::assertSame(3, $snapshot['totals']['umum']);
        self::assertSame(3, $snapshot['totals']['total']);
    }

    public function testSnapshotExcludesCancelledVisitsAndVisitsOutsideTheRange(): void
    {
        $patient = $this->createPatient('940000000002');
        $umum = $this->serviceId('Poli Umum');

        $this->visitOn('1987-06-02', $umum, 'selesai', $patient);
        $this->visitOn('1987-06-16', $umum, 'batal', $patient);
        $this->visitOn('1987-05-28', $umum, 'selesai', $patient);
        $this->visitOn('1987-07-01', $umum, 'selesai', $patient);

        $snapshot = \report_build_snapshot('1987-06-01', '1987-06-30', null);

        self::assertSame(1, $snapshot['totals']['umum']);
        self::assertSame(1, $snapshot['totals']['total']);
    }

    public function testSnapshotFiltersByService(): void
    {
        $patient = $this->createPatient('940000000003');

        $this->visitOn('1987-06-02', $this->serviceId('Poli Umum'), 'selesai', $patient);
        $this->visitOn('1987-06-03', $this->serviceId('Poli Gigi'), 'selesai', $patient);
        $this->visitOn('1987-06-04', $this->serviceId('Konseling/Psikologi'), 'selesai', $patient);

        $snapshot = \report_build_snapshot('1987-06-01', '1987-06-30', 'gigi');

        self::assertSame(['slug' => 'gigi', 'name' => 'Poli Gigi'], $snapshot['service']);
        self::assertSame(1, $snapshot['weeks'][0]['gigi']);
        self::assertSame(0, $snapshot['weeks'][0]['umum']);
        self::assertSame(0, $snapshot['weeks'][0]['konseling']);
        self::assertSame(1, $snapshot['totals']['gigi']);
        self::assertSame(1, $snapshot['totals']['total']);
    }

    public function testSnapshotForAllPolisSumsEveryService(): void
    {
        $patient = $this->createPatient('940000000004');

        $this->visitOn('1987-06-02', $this->serviceId('Poli Umum'), 'selesai', $patient);
        $this->visitOn('1987-06-03', $this->serviceId('Poli Gigi'), 'selesai', $patient);
        $this->visitOn('1987-06-04', $this->serviceId('Konseling/Psikologi'), 'selesai', $patient);

        $snapshot = \report_build_snapshot('1987-06-01', '1987-06-30', null);

        self::assertSame(1, $snapshot['totals']['umum']);
        self::assertSame(1, $snapshot['totals']['gigi']);
        self::assertSame(1, $snapshot['totals']['konseling']);
        self::assertSame(3, $snapshot['totals']['total']);
    }

    public function testSnapshotExcludesExpiredVisits(): void
    {
        $patient = $this->createPatient('940000000008');
        $umum = $this->serviceId('Poli Umum');

        $this->visitOn('1987-06-02', $umum, 'selesai', $patient);
        $this->visitOn('1987-06-03', $umum, 'expired', $patient);

        $snapshot = \report_build_snapshot('1987-06-01', '1987-06-30', null);

        self::assertSame(1, $snapshot['totals']['umum']);
        self::assertSame(1, $snapshot['totals']['total']);
    }

    public function testExpireStaleVisitsMarksPreviousDayRows(): void
    {
        $patient = $this->createPatient('940000000009');
        $umum = $this->serviceId('Poli Umum');
        $gigi = $this->serviceId('Poli Gigi');
        $konseling = $this->serviceId('Konseling/Psikologi');
        $timezone = new \DateTimeZone('Asia/Jakarta');
        $yesterday = (new \DateTimeImmutable('yesterday', $timezone))->format('Y-m-d');
        $today = (new \DateTimeImmutable('now', $timezone))->format('Y-m-d');

        $this->visitOn($yesterday, $umum, 'menunggu', $patient);
        $this->visitOn($yesterday, $gigi, 'dilayani', $patient);
        $this->visitOn($yesterday, $konseling, 'selesai', $patient);
        $this->visitOn($today, $umum, 'menunggu', $patient);

        $count = (int) $this->pdo->query('SELECT expire_stale_visits()')->fetchColumn();
        self::assertGreaterThanOrEqual(2, $count);

        $stmt = $this->pdo->prepare(
            'SELECT status, expired_at FROM visits WHERE patient_id = :patient AND queue_date = :date'
        );
        $stmt->execute(['patient' => $patient, 'date' => $yesterday]);
        $rows = $stmt->fetchAll();

        $statuses = array_column($rows, 'status');
        sort($statuses);
        self::assertSame(['expired', 'expired', 'selesai'], $statuses);

        foreach ($rows as $row) {
            if ('expired' === $row['status']) {
                self::assertNotNull($row['expired_at']);
            }
        }

        $stmt = $this->pdo->prepare(
            'SELECT status FROM visits WHERE patient_id = :patient AND queue_date = :date'
        );
        $stmt->execute(['patient' => $patient, 'date' => $today]);
        self::assertSame('menunggu', $stmt->fetchColumn());
    }

    public function testExpireStaleVisitsIsIdempotent(): void
    {
        $patient = $this->createPatient('940000000010');
        $umum = $this->serviceId('Poli Umum');
        $yesterday = (new \DateTimeImmutable('yesterday', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

        $this->visitOn($yesterday, $umum, 'menunggu', $patient);

        $first = (int) $this->pdo->query('SELECT expire_stale_visits()')->fetchColumn();
        self::assertGreaterThanOrEqual(1, $first);

        $second = (int) $this->pdo->query('SELECT expire_stale_visits()')->fetchColumn();
        self::assertSame(0, $second);
    }

    public function testDownloadRouteStreamsAPdfAndLogsTheExport(): void
    {
        $patient = $this->createPatient('940000000005');
        $umum = $this->serviceId('Poli Umum');

        $this->visitOn('1987-06-02', $umum, 'selesai', $patient);
        $this->visitOn('1987-06-03', $umum, 'selesai', $patient);

        $actorId = $this->adminActor();
        $_POST = ['dari' => '1987-06-01', 'sampai' => '1987-06-30', 'poli' => 'umum'];

        $response = $this->download($actorId);

        self::assertSame(200, $response->status);
        self::assertSame('application/pdf', $response->headers['Content-Type']);
        self::assertStringContainsString(
            'Rekap_Kunjungan_Umum_Juni_1987.pdf',
            $response->headers['Content-Disposition'],
        );
        self::assertStringStartsWith('%PDF-', $response->body);
        self::assertGreaterThan(2000, strlen($response->body));

        $row = $this->lastExport($actorId);

        self::assertIsArray($row);
        self::assertSame('rekap_bulanan', $row['report_type']);
        self::assertSame('1987-06-01', $row['period_start']);
        self::assertSame('1987-06-30', $row['period_end']);
        self::assertSame($umum, (int) $row['service_id']);
        self::assertSame('Rekap_Kunjungan_Umum_Juni_1987.pdf', $row['filename']);
        self::assertSame(strlen($response->body), (int) $row['byte_size']);
        self::assertSame(64, strlen((string) $row['checksum']));

        $snapshot = json_decode((string) $row['snapshot'], true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($snapshot);
        self::assertSame(2, $snapshot['totals']['umum']);
    }

    public function testDownloadRouteForAllPolisStoresANullService(): void
    {
        $patient = $this->createPatient('940000000006');
        $this->visitOn('1987-06-02', $this->serviceId('Poli Umum'), 'selesai', $patient);

        $actorId = $this->adminActor();
        $_POST = ['dari' => '1987-06-01', 'sampai' => '1987-06-30', 'poli' => 'semua'];

        $response = $this->download($actorId);

        self::assertSame(200, $response->status);
        self::assertStringContainsString(
            'Rekap_Kunjungan_Semua_Juni_1987.pdf',
            $response->headers['Content-Disposition'],
        );

        $row = $this->lastExport($actorId);

        self::assertIsArray($row);
        self::assertNull($row['service_id']);
    }

    public function testDownloadRouteRejectsAnUnknownServiceWithoutLogging(): void
    {
        $actorId = $this->adminActor();
        $_POST = ['dari' => '1987-06-01', 'sampai' => '1987-06-30', 'poli' => 'bogus'];

        $before = $this->exportCount();

        $response = $this->download($actorId);

        self::assertSame(400, $response->status);
        self::assertSame($before, $this->exportCount());
    }

    public function testDownloadRouteRejectsAReversedRangeWithoutLogging(): void
    {
        $actorId = $this->adminActor();
        $_POST = ['dari' => '1987-06-30', 'sampai' => '1987-06-01', 'poli' => 'umum'];

        $before = $this->exportCount();

        $response = $this->download($actorId);

        self::assertSame(400, $response->status);
        self::assertSame($before, $this->exportCount());
    }

    public function testDownloadRouteStillRendersAnEmptyMonth(): void
    {
        $actorId = $this->adminActor();
        $_POST = ['dari' => '1987-07-01', 'sampai' => '1987-07-31', 'poli' => 'semua'];

        $response = $this->download($actorId);

        self::assertSame(200, $response->status);
        self::assertStringStartsWith('%PDF-', $response->body);

        $row = $this->lastExport($actorId);

        self::assertIsArray($row);
        $snapshot = json_decode((string) $row['snapshot'], true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($snapshot);
        self::assertSame(0, $snapshot['totals']['total']);
    }

    public function testPageUsesTheRequestedMonth(): void
    {
        $patient = $this->createPatient('940000000007');

        $this->visitOn('1987-06-02', $this->serviceId('Poli Umum'), 'selesai', $patient);
        $this->visitOn('1987-06-15', $this->serviceId('Poli Gigi'), 'selesai', $patient);

        $actorId = $this->adminActor();
        $_GET = ['bulan' => '1987-06'];

        $html = $this->page($actorId);

        self::assertStringContainsString('Periode: Juni 1987', $html);
        self::assertStringContainsString('Minggu 5', $html);
        self::assertStringNotContainsString('Belum ada kunjungan pada periode ini.', $html);
    }

    public function testPageShowsTheEmptyNoteForAMonthWithoutVisits(): void
    {
        $actorId = $this->adminActor();
        $_GET = ['bulan' => '1987-07'];

        $html = $this->page($actorId);

        self::assertStringContainsString('Periode: Juli 1987', $html);
        self::assertStringContainsString('Belum ada kunjungan pada periode ini.', $html);
    }

    public function testPageFallsBackToTheCurrentMonth(): void
    {
        $actorId = $this->adminActor();

        $html = $this->page($actorId);

        self::assertStringContainsString('Periode: '.\report_month_bounds(null)['label'], $html);
    }

    private function adminActor(): string
    {
        $id = $this->adminId();

        \Auth::$user = [
            'id' => $id,
            'username' => 'admin',
            'full_name' => 'Administrator',
            'role' => 'admin',
        ];

        return $id;
    }

    private function download(string $actorId): \Response
    {
        /** @var array<string, callable> $route */
        $route = require \app_path('app/routes/admin.rekap-bulanan.unduh.php');

        $response = $route['POST'](new \Request('POST', 'admin/rekap-bulanan/unduh', [], [
            'user_id' => $actorId,
            'user' => 'admin',
            'role' => 'admin',
        ]));

        self::assertInstanceOf(\Response::class, $response);

        return $response;
    }

    private function page(string $actorId): string
    {
        /** @var array<string, callable> $route */
        $route = require \app_path('app/routes/admin.rekap-bulanan.php');

        $html = $route['GET'](new \Request('GET', 'admin/rekap-bulanan', [], [
            'user_id' => $actorId,
            'user' => 'admin',
            'role' => 'admin',
        ]));

        self::assertIsString($html);

        return $html;
    }

    /** @return null|array<string, mixed> */
    private function lastExport(string $actorId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM report_exports WHERE generated_by = :actor ORDER BY created_at DESC, id DESC LIMIT 1'
        );
        $stmt->execute(['actor' => $actorId]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    private function exportCount(): int
    {
        return (int) $this->pdo->query('SELECT count(*) FROM report_exports')->fetchColumn();
    }

    private function visitOn(string $date, int $serviceId, string $status, string $patientId): string
    {
        $next = $this->pdo->prepare(
            'SELECT COALESCE(MAX(queue_number), 0) + 1
             FROM visits
             WHERE service_id = :service AND queue_date = :date'
        );
        $next->execute(['service' => $serviceId, 'date' => $date]);
        $number = (int) $next->fetchColumn();

        $stmt = $this->pdo->prepare(
            "INSERT INTO visits (
                patient_id, service_id, queue_number, queue_date, status, created_by, registration_source
             ) VALUES (
                :patient, :service, :number, :date, :status, :actor, 'staff'
             ) RETURNING id"
        );
        $stmt->execute([
            'patient' => $patientId,
            'service' => $serviceId,
            'number' => $number,
            'date' => $date,
            'status' => $status,
            'actor' => $this->adminId(),
        ]);

        return (string) $stmt->fetchColumn();
    }
}
