-- 0020_add_examination_note.down.sql

ALTER TABLE examinations
    DROP COLUMN IF EXISTS note;
