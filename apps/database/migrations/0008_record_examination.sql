-- 0008_record_examination.sql
-- Atomic examination recording + visit completion (FR-EXM-04, FR-EXM-06).
--
-- SECURITY INVOKER and the same RLS contract as 0007_register_visit.sql:
--   * app.actor_id / app.actor_role set by the application.
--   * Policies must allow INSERT + SELECT (RETURNING) on examinations and
--     UPDATE + SELECT on visits for the app role.
--   * Call inside the transaction that sets app.actor_role.

CREATE FUNCTION record_examination(
    p_visit_id      UUID,
    p_examiner_role VARCHAR,
    p_doctor_id     UUID,
    p_recorded_by   UUID,
    p_complaint     TEXT,
    p_findings      TEXT,
    p_diagnosis     TEXT,
    p_treatment     TEXT
) RETURNS UUID
LANGUAGE plpgsql
SET search_path = public
AS $$
DECLARE
    v_examination UUID;
BEGIN
    -- examinations.visit_id is UNIQUE (1-1): a second call for the same visit
    -- raises a unique violation and rolls the whole call back.
    INSERT INTO examinations (
        visit_id, examiner_role, doctor_id, recorded_by,
        complaint, findings, diagnosis, treatment
    ) VALUES (
        p_visit_id, p_examiner_role, p_doctor_id, p_recorded_by,
        p_complaint, p_findings, p_diagnosis, p_treatment
    )
    RETURNING id INTO v_examination;

    UPDATE visits SET status = 'selesai' WHERE id = p_visit_id;

    RETURN v_examination;
END;
$$;

REVOKE EXECUTE ON FUNCTION record_examination(UUID, VARCHAR, UUID, UUID, TEXT, TEXT, TEXT, TEXT) FROM PUBLIC;
