-- Rollback 0016_queue_boards.sql
DROP FUNCTION IF EXISTS advance_queue(SMALLINT, UUID);

DROP FUNCTION IF EXISTS queue_boards();

DROP INDEX IF EXISTS visits_served_lookup_idx;

ALTER TABLE visits
    DROP COLUMN IF EXISTS served_at;
