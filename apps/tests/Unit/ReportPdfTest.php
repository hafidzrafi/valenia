<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * PDF renderer smoke tests. These exercise dompdf without PostgreSQL.
 *
 * @internal
 *
 * @coversNothing
 */
final class ReportPdfTest extends TestCase
{
    public function testRendersAPdfForAllPolis(): void
    {
        $pdf = \report_render_pdf(
            $this->snapshot(null),
            '9 November 2026 10:00',
            'Administrator',
        );

        self::assertStringStartsWith('%PDF-', $pdf);
        self::assertGreaterThan(2000, strlen($pdf));
    }

    public function testRendersAPdfForOnePoli(): void
    {
        $pdf = \report_render_pdf(
            $this->snapshot(['slug' => 'umum', 'name' => 'Poli Umum']),
            '9 November 2026 10:00',
            'Administrator',
        );

        self::assertStringStartsWith('%PDF-', $pdf);
        self::assertGreaterThan(2000, strlen($pdf));
    }

    /**
     * @param null|array{slug: string, name: string} $service
     *
     * @return array<string, mixed>
     */
    private function snapshot(?array $service): array
    {
        return [
            'report' => 'rekap_bulanan',
            'period' => ['start' => '1987-06-01', 'end' => '1987-06-30'],
            'service' => $service,
            'weeks' => [
                ['week' => 1, 'label' => 'Minggu 1', 'start' => '1987-06-01', 'end' => '1987-06-07',
                    'umum' => 2, 'gigi' => 0, 'konseling' => 0, 'total' => 2],
                ['week' => 2, 'label' => 'Minggu 2', 'start' => '1987-06-08', 'end' => '1987-06-14',
                    'umum' => 3, 'gigi' => 1, 'konseling' => 0, 'total' => 4],
                ['week' => 5, 'label' => 'Minggu 5', 'start' => '1987-06-29', 'end' => '1987-06-30',
                    'umum' => 0, 'gigi' => 0, 'konseling' => 1, 'total' => 1],
            ],
            'totals' => ['umum' => 5, 'gigi' => 1, 'konseling' => 1, 'total' => 7],
        ];
    }
}
