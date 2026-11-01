<?php

declare(strict_types=1);

namespace Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class PortalInteractiveTest extends DatabaseTestCase
{
    public function testDefaultSelectionIsTheActiveVisit(): void
    {
        $patientId = $this->createPatient('910000000001');
        $ticket = \issue_guest_ticket($patientId, $this->serviceId(), 'Demam');

        $data = \portal_data($patientId);

        self::assertNotNull($data['active']);
        self::assertNotNull($data['selected']);
        self::assertSame($ticket['visit_id'], $data['selected']['id']);
        self::assertTrue((bool) $data['selected']['is_active']);
    }

    public function testSelectedVisitMustBelongToThePatient(): void
    {
        $patientA = $this->createPatient('910000000002');
        $patientB = $this->createPatient('910000000003');
        $visitA = \issue_guest_ticket($patientA, $this->serviceId(), 'Demam')['visit_id'];
        $visitB = \issue_guest_ticket($patientB, $this->serviceId(), 'Batuk')['visit_id'];

        $data = \portal_data($patientB, $visitA);

        self::assertNotNull($data['selected']);
        self::assertSame($visitB, $data['selected']['id']);
        self::assertNotSame($visitA, $data['selected']['id']);
    }

    public function testSelectedHistoryVisitIsReturnedWithItsCode(): void
    {
        $patientId = $this->createPatient('910000000004');
        $first = \issue_guest_ticket($patientId, $this->serviceId(), 'Demam');
        $second = \issue_guest_ticket($patientId, $this->serviceId(), 'Batuk');

        $data = \portal_data($patientId, (string) $first['visit_id']);

        self::assertNotNull($data['selected']);
        self::assertSame($first['visit_id'], $data['selected']['id']);
        self::assertSame($first['queue_code'], $data['selected']['queue_code']);
        self::assertNotSame($second['visit_id'], $data['selected']['id']);
    }

    public function testCancelOwnTodayMenungguVisit(): void
    {
        $patientId = $this->createPatient('910000000005');
        $ticket = \issue_guest_ticket($patientId, $this->serviceId(), 'Demam');

        self::assertTrue(\cancel_portal_visit($patientId, (string) $ticket['visit_id']));

        $stmt = $this->pdo->prepare('SELECT status FROM visits WHERE id = :id');
        $stmt->execute(['id' => $ticket['visit_id']]);
        self::assertSame('batal', $stmt->fetchColumn());

        self::assertFalse(\cancel_portal_visit($patientId, (string) $ticket['visit_id']));
    }

    public function testCancelRejectsAnotherPatientsVisit(): void
    {
        $patientA = $this->createPatient('910000000006');
        $patientB = $this->createPatient('910000000007');
        $visitA = \issue_guest_ticket($patientA, $this->serviceId(), 'Demam')['visit_id'];

        self::assertFalse(\cancel_portal_visit($patientB, (string) $visitA));

        $stmt = $this->pdo->prepare('SELECT status FROM visits WHERE id = :id');
        $stmt->execute(['id' => $visitA]);
        self::assertSame('menunggu', $stmt->fetchColumn());
    }

    public function testCancelRejectsNonMenungguStatuses(): void
    {
        $patientId = $this->createPatient('910000000008');
        $ticket = \issue_guest_ticket($patientId, $this->serviceId(), 'Demam');

        $this->pdo->prepare("UPDATE visits SET status = 'dilayani' WHERE id = :id")
            ->execute(['id' => $ticket['visit_id']])
        ;

        self::assertFalse(\cancel_portal_visit($patientId, (string) $ticket['visit_id']));
    }

    public function testCancelRejectsPreviousDayVisit(): void
    {
        $patientId = $this->createPatient('910000000009');
        $ticket = \issue_guest_ticket($patientId, $this->serviceId(), 'Demam');

        $this->pdo->prepare('UPDATE visits SET queue_date = app_today() - 1 WHERE id = :id')
            ->execute(['id' => $ticket['visit_id']])
        ;

        self::assertFalse(\cancel_portal_visit($patientId, (string) $ticket['visit_id']));
    }
}
