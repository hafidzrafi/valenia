-- 0014_cancel_guest_visit.sql
-- Atomic cancellation for self-service portal visits.
--
-- Rules enforced in the database:
--   * the visit must belong to the patient;
--   * only status = 'menunggu' can be cancelled;
--   * only today's visit (Asia/Jakarta) can be cancelled;
--   * concurrent cancels are serialised with FOR UPDATE.

CREATE FUNCTION cancel_guest_visit(
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
       SET status = 'batal'
     WHERE id = p_visit_id;

    RETURN QUERY SELECT TRUE, v_visit.queue_number, v_visit.queue_date;
END;
$$;

REVOKE EXECUTE ON FUNCTION cancel_guest_visit(UUID, UUID) FROM PUBLIC;
