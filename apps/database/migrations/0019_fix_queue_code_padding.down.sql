-- 0019_fix_queue_code_padding.down.sql
-- Restore the previous definitions from 0016 and 0017 (lpad based).

CREATE OR REPLACE FUNCTION queue_boards()
RETURNS TABLE (
    service_id           SMALLINT,
    service_name         VARCHAR,
    queue_prefix         VARCHAR,
    current_queue_number INT,
    current_queue_code   TEXT,
    current_visit_id     UUID,
    current_served_at    TIMESTAMPTZ,
    waiting_count        BIGINT,
    next_queue_number    INT,
    next_queue_code      TEXT
)
LANGUAGE plpgsql STABLE
SET search_path = public
AS $$
BEGIN
    RETURN QUERY
    SELECT
        s.id,
        s.name,
        s.queue_prefix,
        cur.queue_number,
        cur.queue_code,
        cur.visit_id,
        cur.served_at,
        wait.waiting_count,
        wait.next_queue_number,
        wait.next_queue_code
    FROM services s
    LEFT JOIN LATERAL (
        SELECT
            v.id AS visit_id,
            v.queue_number,
            s.queue_prefix || '-' || lpad(v.queue_number::text, 2, '0') AS queue_code,
            COALESCE(v.served_at, v.created_at) AS served_at
        FROM visits v
        WHERE v.service_id = s.id
          AND v.queue_date = app_today()
          AND (
              v.status = 'dilayani'
              OR (v.status = 'selesai' AND v.served_at IS NOT NULL)
          )
        ORDER BY COALESCE(v.served_at, v.created_at) DESC
        LIMIT 1
    ) cur ON TRUE
    LEFT JOIN LATERAL (
        SELECT
            count(*) AS waiting_count,
            min(v.queue_number) AS next_queue_number,
            CASE
                WHEN min(v.queue_number) IS NULL THEN NULL
                ELSE s.queue_prefix || '-' || lpad(min(v.queue_number)::text, 2, '0')
            END AS next_queue_code
        FROM visits v
        WHERE v.service_id = s.id
          AND v.queue_date = app_today()
          AND v.status = 'menunggu'
    ) wait ON TRUE
    WHERE s.is_active
    ORDER BY s.id;
END;
$$;

REVOKE EXECUTE ON FUNCTION queue_boards() FROM PUBLIC;

CREATE OR REPLACE FUNCTION advance_queue(
    p_service_id SMALLINT,
    p_actor_id   UUID
) RETURNS TABLE (advanced BOOLEAN, queue_code TEXT, visit_id UUID)
LANGUAGE plpgsql
SET search_path = public
AS $$
DECLARE
    v_visit visits%ROWTYPE;
    v_prefix VARCHAR;
    v_code TEXT;
BEGIN
    SELECT * INTO v_visit
    FROM visits
    WHERE service_id = p_service_id
      AND queue_date = app_today()
      AND status = 'menunggu'
    ORDER BY queue_number ASC
    LIMIT 1
    FOR UPDATE SKIP LOCKED;

    IF NOT FOUND THEN
        RETURN QUERY SELECT FALSE, NULL::TEXT, NULL::UUID;
        RETURN;
    END IF;

    SELECT queue_prefix INTO v_prefix FROM services WHERE id = p_service_id;

    IF v_prefix IS NULL THEN
        RETURN QUERY SELECT FALSE, NULL::TEXT, NULL::UUID;
        RETURN;
    END IF;

    PERFORM set_config('app.actor_id', p_actor_id::text, true);

    UPDATE visits
       SET status = 'dilayani',
           served_at = clock_timestamp()
     WHERE id = v_visit.id;

    v_code := v_prefix || '-' || lpad(v_visit.queue_number::text, 2, '0');

    RETURN QUERY SELECT TRUE, v_code, v_visit.id;
END;
$$;

REVOKE EXECUTE ON FUNCTION advance_queue(SMALLINT, UUID) FROM PUBLIC;
