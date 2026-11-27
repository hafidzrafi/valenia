<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ScheduleValidationTest extends TestCase
{
    private const UUID = '0192f0a0-0000-7000-8000-000000000000';

    public function testRejectsMalformedDoctorIdBeforeAnyQuery(): void
    {
        self::assertSame('Tenaga medis tidak ditemukan.', \schedule_slot_error('not-a-uuid', 1, '08:00', '15:00'));
        self::assertSame('Tenaga medis tidak ditemukan.', \schedule_slot_error('', 1, '08:00', '15:00'));
    }

    public function testRejectsWeekdayOutsideOneToSeven(): void
    {
        self::assertSame('Hari tidak valid.', \schedule_slot_error(self::UUID, 0, '08:00', '15:00'));
        self::assertSame('Hari tidak valid.', \schedule_slot_error(self::UUID, 8, '08:00', '15:00'));
    }

    public function testRejectsMalformedTime(): void
    {
        self::assertSame('Jam harus dalam format HH:MM.', \schedule_slot_error(self::UUID, 1, '8:00', '15:00'));
        self::assertSame('Jam harus dalam format HH:MM.', \schedule_slot_error(self::UUID, 1, '08:00', '25:00'));
        self::assertSame('Jam harus dalam format HH:MM.', \schedule_slot_error(self::UUID, 1, '08:00', '15:60'));
    }

    public function testRejectsEndNotAfterStart(): void
    {
        self::assertSame('Jam selesai harus setelah jam mulai.', \schedule_slot_error(self::UUID, 1, '15:00', '15:00'));
        self::assertSame('Jam selesai harus setelah jam mulai.', \schedule_slot_error(self::UUID, 1, '15:00', '08:00'));
    }

    public function testAcceptsAWellFormedSlot(): void
    {
        self::assertNull(\schedule_slot_error(self::UUID, 7, '00:00', '23:59'));
    }
}
