-- 0026_drop_handled_by_nurse.down.sql

ALTER TABLE visits
    ADD COLUMN handled_by_nurse BOOLEAN NOT NULL DEFAULT FALSE;
