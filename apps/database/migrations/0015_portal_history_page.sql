-- 0015_portal_history_page.sql
-- Server-side pagination for the patient portal history list.
--
-- Returns one row per visit on the requested page plus the total visit count
-- (repeated on every row) so PHP can render page links without a second
-- query. Page numbers start at 1; out-of-range pages return no rows.

CREATE FUNCTION portal_history_page(
    p_patient_id UUID,
    p_page       INT,
    p_per_page   INT
) RETURNS TABLE (
    visit_id     UUID,
    queue_number INT,
    queue_date   DATE,
    status       VARCHAR,
    created_at   TIMESTAMPTZ,
    service_name VARCHAR,
    queue_prefix VARCHAR,
    queue_code   TEXT,
    total        BIGINT
)
LANGUAGE plpgsql STABLE
SET search_path = public
AS $$
DECLARE
    v_page     INT := GREATEST(p_page, 1);
    v_per_page INT := LEAST(GREATEST(p_per_page, 1), 50);
    v_total    BIGINT;
    v_offset   INT := (v_page - 1) * v_per_page;
BEGIN
    SELECT count(*) INTO v_total
    FROM visits
    WHERE patient_id = p_patient_id;

    RETURN QUERY
    SELECT v.id,
           v.queue_number,
           v.queue_date,
           v.status,
           v.created_at,
           s.name,
           s.queue_prefix,
           s.queue_prefix || '-' || lpad(v.queue_number::text, 2, '0'),
           v_total
    FROM visits v
    JOIN services s ON s.id = v.service_id
    WHERE v.patient_id = p_patient_id
    ORDER BY v.created_at DESC
    LIMIT v_per_page
    OFFSET v_offset;
END;
$$;

REVOKE EXECUTE ON FUNCTION portal_history_page(UUID, INT, INT) FROM PUBLIC;
