-- 0028_examination_updated_at.sql
-- Keep examinations.updated_at accurate when a note or any field changes.
--
-- clock_timestamp() is used instead of now() so the value changes inside the
-- same transaction, where now() would return the transaction start time.

CREATE OR REPLACE FUNCTION touch_examinations_updated_at()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    NEW.updated_at = clock_timestamp();
    RETURN NEW;
END;
$$;

CREATE TRIGGER examinations_touch_updated_at
    BEFORE UPDATE ON examinations
    FOR EACH ROW EXECUTE FUNCTION touch_examinations_updated_at();
