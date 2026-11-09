<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Period parsing, week bucketing, totals, checksum, and filenames.
 *
 * These never touch PostgreSQL.
 *
 * @internal
 *
 * @coversNothing
 */
final class ReportHelpersTest extends TestCase
{
    public function testMonthBoundsForLeapFebruary(): void
    {
        $bounds = \report_month_bounds('2024-02');

        self::assertSame('2024-02-01', $bounds['start']);
        self::assertSame('2024-02-29', $bounds['end']);
        self::assertSame('Februari 2024', $bounds['label']);
        self::assertSame('2024-02', $bounds['value']);
    }

    public function testMonthBoundsForThirtyOneDayMonth(): void
    {
        $bounds = \report_month_bounds('1987-06');

        self::assertSame('1987-06-01', $bounds['start']);
        self::assertSame('1987-06-30', $bounds['end']);
        self::assertSame('Juni 1987', $bounds['label']);
    }

    public function testMonthBoundsForThirtyDayMonth(): void
    {
        $bounds = \report_month_bounds('2026-11');

        self::assertSame('2026-11-01', $bounds['start']);
        self::assertSame('2026-11-30', $bounds['end']);
        self::assertSame('November 2026', $bounds['label']);
    }

    public function testInvalidMonthFallsBackToTheCurrentMonth(): void
    {
        $bounds = \report_month_bounds('not-a-month');

        self::assertSame(\report_current_month(), $bounds['value']);
    }

    public function testMissingMonthFallsBackToTheCurrentMonth(): void
    {
        $bounds = \report_month_bounds(null);

        self::assertSame(\report_current_month(), $bounds['value']);
    }

    public function testWeekSpansCoverAFullMonthWithoutGaps(): void
    {
        $weeks = \report_week_spans('1987-06-01', '1987-06-30');

        self::assertCount(5, $weeks);
        self::assertSame('1987-06-01', $weeks[0]['start']);
        self::assertSame('1987-06-07', $weeks[0]['end']);
        self::assertSame('1987-06-29', $weeks[4]['start']);
        self::assertSame('1987-06-30', $weeks[4]['end']);

        foreach ($weeks as $position => $week) {
            self::assertSame($position + 1, $week['week']);
            self::assertSame('Minggu '.($position + 1), $week['label']);

            if ($position > 0) {
                $previousEnd = new \DateTimeImmutable($weeks[$position - 1]['end']);
                self::assertSame(
                    $previousEnd->modify('+1 day')->format('Y-m-d'),
                    $week['start'],
                );
            }
        }
    }

    public function testWeekSpansClipACustomRange(): void
    {
        $weeks = \report_week_spans('1987-06-08', '1987-06-21');

        self::assertCount(2, $weeks);
        self::assertSame('1987-06-08', $weeks[0]['start']);
        self::assertSame('1987-06-14', $weeks[0]['end']);
        self::assertSame('1987-06-15', $weeks[1]['start']);
        self::assertSame('1987-06-21', $weeks[1]['end']);
    }

    public function testWeekSpansReturnEmptyForAReversedRange(): void
    {
        self::assertSame([], \report_week_spans('1987-06-30', '1987-06-01'));
    }

    public function testParseDateRejectsImpossibleDates(): void
    {
        self::assertNull(\report_parse_date('2024-02-30'));
        self::assertNull(\report_parse_date('1987-6-1'));
        self::assertNull(\report_parse_date('nope'));
        self::assertSame('2024-02-29', \report_parse_date('2024-02-29'));
    }

    public function testRangeFallsBackToTheCurrentMonthWhenBothInputsAreEmpty(): void
    {
        $range = \report_range(null, null);
        $bounds = \report_month_bounds(null);

        self::assertNotNull($range);
        self::assertSame($bounds['start'], $range['start']);
        self::assertSame($bounds['end'], $range['end']);
    }

    public function testRangeWithOnlyAStartBecomesASingleDay(): void
    {
        $range = \report_range('1987-06-08', null);

        self::assertNotNull($range);
        self::assertSame('1987-06-08', $range['start']);
        self::assertSame('1987-06-08', $range['end']);
    }

    public function testRangeWithOnlyAnEndBecomesASingleDay(): void
    {
        $range = \report_range(null, '1987-06-21');

        self::assertNotNull($range);
        self::assertSame('1987-06-21', $range['start']);
        self::assertSame('1987-06-21', $range['end']);
    }

    public function testRangeRejectsAReversedPair(): void
    {
        self::assertNull(\report_range('1987-06-30', '1987-06-01'));
    }

    public function testRangeRejectsMoreThanAYear(): void
    {
        self::assertNull(\report_range('1987-01-01', '1988-01-03'));
        self::assertNotNull(\report_range('1987-01-01', '1987-12-31'));
    }

    public function testTotalsAddEveryWeek(): void
    {
        $totals = \report_totals([
            ['umum' => 2, 'gigi' => 0, 'konseling' => 0, 'total' => 2],
            ['umum' => 3, 'gigi' => 1, 'konseling' => 0, 'total' => 4],
            ['umum' => 0, 'gigi' => 0, 'konseling' => 1, 'total' => 1],
        ]);

        self::assertSame(5, $totals['umum']);
        self::assertSame(1, $totals['gigi']);
        self::assertSame(1, $totals['konseling']);
        self::assertSame(7, $totals['total']);
    }

    public function testChecksumIsStableAndContentSensitive(): void
    {
        $snapshot = $this->snapshot(null);

        self::assertSame(\report_checksum($snapshot), \report_checksum($snapshot));
        self::assertSame(64, strlen(\report_checksum($snapshot)));

        $changed = $snapshot;
        $changed['totals'] = ['umum' => 99, 'gigi' => 0, 'konseling' => 0, 'total' => 99];

        self::assertNotSame(\report_checksum($snapshot), \report_checksum($changed));
    }

    public function testFilenameForEveryScope(): void
    {
        self::assertSame(
            'Rekap_Kunjungan_Semua_Juni_1987.pdf',
            \report_filename($this->snapshot(null)),
        );
        self::assertSame(
            'Rekap_Kunjungan_Umum_Juni_1987.pdf',
            \report_filename($this->snapshot(['slug' => 'umum', 'name' => 'Poli Umum'])),
        );
        self::assertSame(
            'Rekap_Kunjungan_Gigi_Juni_1987.pdf',
            \report_filename($this->snapshot(['slug' => 'gigi', 'name' => 'Poli Gigi'])),
        );
        self::assertSame(
            'Rekap_Kunjungan_Konseling_Juni_1987.pdf',
            \report_filename($this->snapshot(['slug' => 'konseling', 'name' => 'Konseling/Psikologi'])),
        );
    }

    public function testFilenameHasNoSpacesOrSlashes(): void
    {
        $filename = \report_filename($this->snapshot(['slug' => 'konseling', 'name' => 'Konseling/Psikologi']));

        self::assertStringNotContainsString(' ', $filename);
        self::assertStringNotContainsString('/', $filename);
        self::assertStringEndsWith('.pdf', $filename);
    }

    public function testDateLabelUsesIndonesianMonthNames(): void
    {
        self::assertSame('1 Juni 1987', \report_date_label('1987-06-01'));
        self::assertSame('29 Juni 1987', \report_date_label('1987-06-29'));
    }

    public function testDateLabelFallsBackToTheInputWhenInvalid(): void
    {
        self::assertSame('nope', \report_date_label('nope'));
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
                    'umum' => 5, 'gigi' => 0, 'konseling' => 0, 'total' => 5],
            ],
            'totals' => ['umum' => 5, 'gigi' => 0, 'konseling' => 0, 'total' => 5],
        ];
    }
}
