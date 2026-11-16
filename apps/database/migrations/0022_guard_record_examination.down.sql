-- 0022_guard_record_examination.down.sql

CREATE OR REPLACE FUNCTION record_examination(
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

ALTER TABLE visits
    DROP COLUMN IF EXISTS completed_at;
