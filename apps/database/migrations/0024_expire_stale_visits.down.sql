-- 0024_expire_stale_visits.down.sql

DROP FUNCTION IF EXISTS expire_stale_visits();

UPDATE visits
   SET status = 'batal',
       cancelled_at = COALESCE(cancelled_at, expired_at)
 WHERE status = 'expired';

ALTER TABLE visits
    DROP COLUMN IF EXISTS expired_at;

ALTER TABLE visits
    DROP CONSTRAINT visits_status_check;

ALTER TABLE visits
    ADD CONSTRAINT visits_status_check
    CHECK (status IN ('menunggu', 'dilayani', 'selesai', 'batal'));
