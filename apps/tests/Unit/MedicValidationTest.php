<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class MedicValidationTest extends TestCase
{
    public function testRejectsNameOutsideLengthBounds(): void
    {
        self::assertSame('Nama tenaga medis wajib diisi (2-120 karakter).', \medic_error('   ', '', 1));
        self::assertSame('Nama tenaga medis wajib diisi (2-120 karakter).', \medic_error(str_repeat('a', 121), '', 1));
    }

    public function testRejectsOverlongStr(): void
    {
        self::assertSame('Nomor STR maksimal 32 karakter.', \medic_error('dr. Uji', str_repeat('9', 33), 1));
    }

    public function testRejectsMissingPoli(): void
    {
        self::assertSame('Poli wajib dipilih.', \medic_error('dr. Uji', '', 0));
        self::assertSame('Poli tidak ditemukan.', \medic_error('dr. Uji', '', 32768));
    }

    public function testAcceptsValidInput(): void
    {
        self::assertNull(\medic_error('dr. Uji', 'STR-1', 1));
        self::assertNull(\medic_error('dr. Uji', '', 32767));
    }
}
