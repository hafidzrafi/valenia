-- 0013_service_queue_prefix.sql
-- Human-readable queue codes: PU-01, PG-01, PK-01.
-- The stored queue number remains an integer; the prefix is service metadata.

ALTER TABLE services
    ADD COLUMN queue_prefix VARCHAR(2) NOT NULL DEFAULT 'XX';

UPDATE services SET queue_prefix = 'PU' WHERE name = 'Poli Umum';
UPDATE services SET queue_prefix = 'PG' WHERE name = 'Poli Gigi';
UPDATE services SET queue_prefix = 'PK' WHERE name = 'Konseling/Psikologi';

ALTER TABLE services
    ADD CONSTRAINT services_queue_prefix_check
    CHECK (queue_prefix ~ '^[A-Z]{2}$');

CREATE UNIQUE INDEX services_queue_prefix_idx ON services (queue_prefix);
