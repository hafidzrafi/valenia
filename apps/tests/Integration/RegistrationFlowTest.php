<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class RegistrationFlowTest extends DatabaseTestCase
{
    public function testFindOrCreatePatientCreatesThenFinds(): void
    {
        $input = [
            'identity_number' => '123456789014',
            'birth_date' => '2000-01-01',
            'full_name' => 'Pasien Baru',
            'gender' => 'P',
            'contact' => '08123456789',
            'complaint' => 'Demam',
        ];

        $created = \find_or_create_patient($input);
        self::assertTrue($created['ok']);
        self::assertTrue($created['created']);
        self::assertSame('123456789014', $created['patient']['identity_number']);

        $found = \find_or_create_patient($input);
        self::assertTrue($found['ok']);
        self::assertFalse($found['created']);
        self::assertSame($created['patient']['id'], $found['patient']['id']);

        $mismatch = \find_or_create_patient(['birth_date' => '1999-01-01'] + $input);
        self::assertFalse($mismatch['ok']);
        self::assertSame('Data pasien tidak ditemukan atau tidak cocok.', $mismatch['error']);
    }

    public function testGuestTicketUsesGuestActorAndIncrementsQueue(): void
    {
        $patientId = $this->createPatient('123456789015');
        $serviceId = $this->serviceId();
        $guestId = (string) $this->pdo->query("SELECT id FROM users WHERE username = 'guest'")->fetchColumn();

        self::assertNull(\has_active_visit_today($patientId, $serviceId));

        $first = \issue_guest_ticket($patientId, $serviceId, 'Sakit kepala');
        $second = \issue_guest_ticket($patientId, $serviceId, 'Sakit kepala');

        self::assertSame((int) $first['queue_number'] + 1, (int) $second['queue_number']);
        self::assertNotSame($first['ticket_token'], $second['ticket_token']);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $first['ticket_token'],
        );

        $stmt = $this->pdo->prepare(
            'SELECT created_by, registration_source, complaint, ticket_token, queue_date
             FROM visits WHERE id = :id'
        );
        $stmt->execute(['id' => $first['visit_id']]);
        $visit = $stmt->fetch();

        self::assertIsArray($visit);
        self::assertSame($guestId, $visit['created_by']);
        self::assertSame('guest', $visit['registration_source']);
        self::assertSame('Sakit kepala', $visit['complaint']);
        self::assertSame($first['ticket_token'], $visit['ticket_token']);
        self::assertSame(
            (string) $this->pdo->query('SELECT app_today()')->fetchColumn(),
            (string) $visit['queue_date'],
        );

        self::assertNotNull(\has_active_visit_today($patientId, $serviceId));
    }

    public function testInactiveServiceIsRejected(): void
    {
        $patientId = $this->createPatient('123456789016');
        $serviceId = $this->serviceId();
        $this->pdo->prepare('UPDATE services SET is_active = FALSE WHERE id = :id')
            ->execute(['id' => $serviceId])
        ;

        $this->expectException(\PDOException::class);
        \issue_guest_ticket($patientId, $serviceId, 'Demam');
    }

    public function testPortalLookupRequiresMatchingBirthDate(): void
    {
        $this->createPatient('123456789017');
        $identity = '123456789017';

        self::assertNotNull(\find_patient_for_portal($identity, '2000-01-01'));
        self::assertNull(\find_patient_for_portal($identity, '1999-01-01'));
        self::assertNull(\find_patient_for_portal('invalid', '2000-01-01'));
    }

    public function testPortalDataReturnsActiveVisitAndHistory(): void
    {
        $patientId = $this->createPatient('123456789018');
        $serviceId = $this->serviceId();

        \issue_guest_ticket($patientId, $serviceId, 'Kontrol');

        $data = \portal_data($patientId);

        self::assertNotNull($data['active']);
        self::assertNotEmpty($data['history']);
        self::assertSame($patientId, (string) $this->pdo->query(
            'SELECT patient_id FROM visits WHERE id = '.$this->pdo->quote((string) $data['active']['id'])
        )->fetchColumn());
    }

    public function testPortalSessionExpiresAfterTtl(): void
    {
        $patientId = $this->createPatient('123456789019');
        \unlock_portal($patientId);

        self::assertNotNull(\portal_patient());

        $_SESSION['portal_unlocked_at'] = time() - 901;

        self::assertNull(\portal_patient());
        self::assertArrayNotHasKey('portal_patient_id', $_SESSION);
        self::assertArrayNotHasKey('portal_unlocked_at', $_SESSION);
    }
}
