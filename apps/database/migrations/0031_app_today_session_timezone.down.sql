-- 0031_app_today_session_timezone.down.sql
-- Restore the hardcoded Asia/Jakarta definition from 0011.

CREATE OR REPLACE FUNCTION app_today() RETURNS DATE
LANGUAGE sql STABLE
AS $$ SELECT (now() AT TIME ZONE 'Asia/Jakarta')::date $$;
