-- 0022_guard_record_examination.sql
-- Guard visit completion at the SQL level and record completed_at.
--
-- record_examination() used to set status = 'selesai' without checking the
-- current state. The PHP helper checked 'dilayani', but the SQL primitive could
-- move any visit, including a cancelled one, to 'selesai'. The function now
-- locks the visit, rejects anything except 'dilayani', and updates only that
-- state.

ALTER TABLE visits
    ADD COLUMN completed_at TIMESTAMPTZ;

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
    v_status      VARCHAR(12);
BEGIN
    SELECT status INTO v_status
    FROM visits
    WHERE id = p_visit_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Kunjungan tidak ditemukan' USING ERRCODE = 'P0002';
    END IF;

    IF v_status <> 'dilayani' THEN
        RAISE EXCEPTION 'Kunjungan tidak dalam status dilayani' USING ERRCODE = 'P0001';
    END IF;

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

    UPDATE visits
       SET status = 'selesai',
           completed_at = now()
     WHERE id = p_visit_id
       AND status = 'dilayani';

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Kunjungan tidak dapat diselesaikan' USING ERRCODE = 'P0001';
    END IF;

    RETURN v_examination;
END;
$$;

REVOKE EXECUTE ON FUNCTION record_examination(UUID, VARCHAR, UUID, UUID, TEXT, TEXT, TEXT, TEXT) FROM PUBLIC;
