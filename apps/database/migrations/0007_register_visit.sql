-- 0007_register_visit.sql
-- Atomic visit registration + daily queue-number issuance (FR-REG-05, NFR-REL-01).
--
-- SECURITY INVOKER (the default) so that, when Row-Level Security lands
-- (NFR-SEC-10), policies apply to the statements below. Never switch this to
-- SECURITY DEFINER — that would run as the table owner and bypass RLS.
--
-- RLS contract (for the future RLS migration):
--   * app.actor_id   -> read by audit_change(); set by the application.
--   * app.actor_role -> to be read by RLS policies.
--   * Policies on visits / queue_counters must allow, for the app role:
--     INSERT, UPDATE (for ON CONFLICT DO UPDATE) and SELECT (for RETURNING).
--     Without a SELECT policy, INSERT ... RETURNING fails under FORCE RLS.
--   * set_config('app.actor_role', ..., true) is transaction-local, so callers
--     must invoke this function inside the same transaction:
--       BEGIN;
--       SELECT set_config('app.actor_role', 'perawat', true);
--       SELECT * FROM register_visit(...);
--       COMMIT;
--   * Note: the development container connects as POSTGRES_USER, which is a
--     superuser/owner and therefore bypasses RLS. A later card must introduce a
--     dedicated non-owner application role before RLS can take effect.

CREATE FUNCTION register_visit(
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
    -- Atomic upsert: the row lock serialises concurrent registrations so no two
    -- calls can read the same last_number.
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

-- Revoke the default PUBLIC EXECUTE; the owning role keeps access. Grant a
-- future non-owner application role explicitly if one is introduced:
--   GRANT EXECUTE ON FUNCTION register_visit(UUID, SMALLINT, UUID) TO <app_role>;
REVOKE EXECUTE ON FUNCTION register_visit(UUID, SMALLINT, UUID) FROM PUBLIC;
