-- Rollback 0011_guest_registration.sql
DROP FUNCTION IF EXISTS register_guest_visit(UUID, SMALLINT, TEXT);
DROP FUNCTION IF EXISTS app_today();
DROP INDEX IF EXISTS visits_guest_today_idx;
DROP INDEX IF EXISTS visits_ticket_token_idx;

ALTER TABLE visits
    DROP COLUMN IF EXISTS ticket_token,
    DROP COLUMN IF EXISTS complaint,
    DROP COLUMN IF EXISTS registration_source;

-- The `guest` user is intentionally kept: existing guest visits reference it
-- via created_by. created_by was never made nullable, so nothing to restore.
-- Delete the actor manually only after deleting/archiving all guest visits.

-- Restore the original CURRENT_DATE staff function.
CREATE OR REPLACE FUNCTION register_visit(
    p_patient_id UUID,
    p_service_id SMALLINT,
    p_created_by UUID
) RETURNS TABLE (visit_id UUID, queue_number INT)
LANGUAGE plpgsql
SET search_path = public
AS $$
DECLARE
    v_number INT;
    v_visit  UUID;
BEGIN
    INSERT INTO queue_counters (service_id, queue_date, last_number)
    VALUES (p_service_id, CURRENT_DATE, 1)
    ON CONFLICT (service_id, queue_date)
    DO UPDATE SET last_number = queue_counters.last_number + 1
    RETURNING last_number INTO v_number;

    INSERT INTO visits (patient_id, service_id, queue_number, created_by)
    VALUES (p_patient_id, p_service_id, v_number, p_created_by)
    RETURNING id INTO v_visit;

    RETURN QUERY SELECT v_visit, v_number;
END;
$$;

REVOKE EXECUTE ON FUNCTION register_visit(UUID, SMALLINT, UUID) FROM PUBLIC;
