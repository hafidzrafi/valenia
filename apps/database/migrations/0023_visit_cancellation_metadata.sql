-- 0023_visit_cancellation_metadata.sql
-- Record when and by whom a visit was cancelled.
--
-- Portal cancellation uses the shared guest system account, so cancelled_by
-- points at that account rather than the patient. Staff cancellation points at
-- the staff account.

ALTER TABLE visits
    ADD COLUMN cancelled_at TIMESTAMPTZ,
    ADD COLUMN cancelled_by UUID REFERENCES users(id);

CREATE OR REPLACE FUNCTION cancel_guest_visit(
    p_visit_id   UUID,
    p_patient_id UUID
) RETURNS TABLE (cancelled BOOLEAN, queue_number INT, queue_date DATE)
LANGUAGE plpgsql
SET search_path = public
AS $$
DECLARE
    v_visit visits%ROWTYPE;
    v_guest UUID;
BEGIN
    SELECT * INTO v_visit
    FROM visits
    WHERE id = p_visit_id
      AND patient_id = p_patient_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RETURN QUERY SELECT FALSE, NULL::INT, NULL::DATE;
        RETURN;
    END IF;

    IF v_visit.status <> 'menunggu'
       OR v_visit.queue_date <> app_today() THEN
        RETURN QUERY SELECT FALSE, v_visit.queue_number, v_visit.queue_date;
        RETURN;
    END IF;

    SELECT id INTO v_guest FROM users WHERE username = 'guest';

    IF v_guest IS NOT NULL THEN
        PERFORM set_config('app.actor_id', v_guest::text, true);
    END IF;

    UPDATE visits
       SET status = 'batal',
           cancelled_at = now(),
           cancelled_by = v_guest
     WHERE id = p_visit_id;

    RETURN QUERY SELECT TRUE, v_visit.queue_number, v_visit.queue_date;
END;
$$;

REVOKE EXECUTE ON FUNCTION cancel_guest_visit(UUID, UUID) FROM PUBLIC;
