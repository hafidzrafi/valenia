<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RegistrationValidationTest extends TestCase
{
    public function testTwelveDigitIdentityIsNim(): void
    {
        $result = \validate_identity('254107020102');

        self::assertTrue($result['ok']);
        self::assertSame('NIM', $result['type']);
    }

    public function testEighteenDigitIdentityIsNip(): void
    {
        $result = \validate_identity('198765432109876543');

        self::assertTrue($result['ok']);
        self::assertSame('NIP', $result['type']);
    }

    public function testWrongLengthsAreRejectedWithMessages(): void
    {
        self::assertSame('NIM harus terdiri dari 12 digit.', \validate_identity('12345')['error']);
        self::assertSame(
            'NIM harus 12 digit atau NIP harus 18 digit.',
            \validate_identity('1234567890123')['error'],
        );
        self::assertSame(
            'NIP tidak boleh lebih dari 18 digit.',
            \validate_identity('1234567890123456789')['error'],
        );
    }

    public function testNonNumericIdentityIsRejected(): void
    {
        self::assertSame('NIM/NIP hanya boleh berisi angka.', \validate_identity('25410702ab12')['error']);
    }

    public function testValidRegistrationInputPasses(): void
    {
        $result = \validate_registration_input([
            'identity_number' => '254107020102',
            'birth_date' => '2000-01-01',
            'full_name' => 'Test Patient',
            'gender' => 'L',
            'contact' => '08123456789',
            'complaint' => 'Demam',
        ]);

        self::assertTrue($result['ok']);
        self::assertNull($result['error']);
    }

    public function testFutureBirthDateIsRejected(): void
    {
        $result = \validate_registration_input([
            'identity_number' => '254107020102',
            'birth_date' => '2999-01-01',
            'full_name' => 'Test Patient',
            'gender' => 'L',
            'contact' => '',
            'complaint' => 'Demam',
        ]);

        self::assertFalse($result['ok']);
        self::assertSame('Tanggal lahir tidak boleh di masa depan.', $result['error']);
    }

    public function testMissingNameGenderAndComplaintAreRejected(): void
    {
        $base = [
            'identity_number' => '254107020102',
            'birth_date' => '2000-01-01',
            'full_name' => 'Test Patient',
            'gender' => 'L',
            'contact' => '',
            'complaint' => 'Demam',
        ];

        self::assertSame(
            'Nama lengkap wajib diisi (2-120 karakter).',
            \validate_registration_input(['full_name' => ''] + $base)['error'],
        );
        self::assertSame(
            'Jenis kelamin wajib dipilih.',
            \validate_registration_input(['gender' => 'X'] + $base)['error'],
        );
        self::assertSame(
            'Keluhan wajib diisi (3-2000 karakter).',
            \validate_registration_input(['complaint' => ''] + $base)['error'],
        );
    }

    public function testServiceMappingUsesConfiguredNames(): void
    {
        $config = \registration_config();

        self::assertSame('Poli Umum', $config['services']['POLI UMUM']);
        self::assertSame('Poli Gigi', $config['services']['POLI GIGI']);
        self::assertSame('Konseling/Psikologi', $config['services']['KONSELING']);
    }

    public function testQueueCodeUsesPrefixAndPadsTheNumber(): void
    {
        self::assertSame('PU-01', \queue_code(1, 'PU'));
        self::assertSame('PU-11', \queue_code(11, 'PU'));
        self::assertSame('PG-100', \queue_code(100, 'PG'));
        self::assertSame('1', \queue_code(1, ''));
    }
}
