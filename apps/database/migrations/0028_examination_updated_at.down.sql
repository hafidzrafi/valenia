-- 0028_examination_updated_at.down.sql

DROP TRIGGER IF EXISTS examinations_touch_updated_at ON examinations;
DROP FUNCTION IF EXISTS touch_examinations_updated_at();
