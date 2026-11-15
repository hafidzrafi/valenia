-- 0025_one_active_visit.sql
-- One active visit per patient, poli, and day.
--
-- The application used to warn about a duplicate but still allowed a second
-- active visit after confirmation. This migration makes the rule explicit.
--
-- Duplicates are resolved before the index is created: the oldest active visit
-- (lowest queue number) is kept and the rest are marked batal. The repair is
-- one-time. The down migration only drops the index; it does not reopen the
-- cancelled duplicates.

WITH ranked AS (
    SELECT id,
           row_number() OVER (
               PARTITION BY patient_id, service_id, queue_date
               ORDER BY queue_number, created_at, id
           ) AS position
    FROM visits
    WHERE status IN ('menunggu', 'dilayani')
)
UPDATE visits v
   SET status = 'batal',
       cancelled_at = now()
  FROM ranked r
 WHERE v.id = r.id
   AND r.position > 1;

CREATE UNIQUE INDEX visits_one_active_per_patient_service_day
    ON visits (patient_id, service_id, queue_date)
    WHERE status IN ('menunggu', 'dilayani');
