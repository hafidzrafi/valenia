-- 0024_expire_stale_visits.sql
-- Add the expired terminal state and the end-of-day cleanup.
--
-- A visit left in menunggu or dilayani after its queue date is no longer
-- actionable. expire_stale_visits() marks those rows expired so history and
-- reports stop showing them as active. It is idempotent.

ALTER TABLE visits
    ADD COLUMN expired_at TIMESTAMPTZ;

ALTER TABLE visits
    DROP CONSTRAINT visits_status_check;

ALTER TABLE visits
    ADD CONSTRAINT visits_status_check
    CHECK (status IN ('menunggu', 'dilayani', 'selesai', 'batal', 'expired'));

CREATE FUNCTION expire_stale_visits()
RETURNS integer
LANGUAGE plpgsql
SET search_path = public
AS $$
DECLARE
    v_count integer;
BEGIN
    UPDATE visits
       SET status = 'expired',
           expired_at = now()
     WHERE queue_date < app_today()
       AND status IN ('menunggu', 'dilayani');

    GET DIAGNOSTICS v_count = ROW_COUNT;

    RETURN v_count;
END;
$$;

REVOKE EXECUTE ON FUNCTION expire_stale_visits() FROM PUBLIC;
