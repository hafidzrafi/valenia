-- 0029_add_doctors_str_number.down.sql

DROP INDEX IF EXISTS doctors_str_number_unique_idx;

ALTER TABLE doctors
    DROP COLUMN IF EXISTS str_number;
