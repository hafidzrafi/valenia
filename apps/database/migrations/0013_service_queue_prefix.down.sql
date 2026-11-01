-- Rollback 0013_service_queue_prefix.sql
DROP INDEX IF EXISTS services_queue_prefix_idx;

ALTER TABLE services
    DROP CONSTRAINT IF EXISTS services_queue_prefix_check,
    DROP COLUMN IF EXISTS queue_prefix;
