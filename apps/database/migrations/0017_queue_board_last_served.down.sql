-- 0017_queue_board_last_served.down.sql
-- Restore the 0016 behaviour: only a visit still being served is current.

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
          AND v.status = 'dilayani'
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
