-- 0026_drop_handled_by_nurse.sql
-- Remove the unused handled_by_nurse flag.
--
-- The column was added in 0004 but never read or written by the application.
-- The nurse handling state is represented by the visit status and the
-- examination recorder instead.

ALTER TABLE visits
    DROP COLUMN IF EXISTS handled_by_nurse;
