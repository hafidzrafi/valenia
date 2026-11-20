-- 0031_app_today_session_timezone.sql
-- Follow the connection timezone instead of hardcoding Asia/Jakarta.
--
-- app/lib/db.php now issues set_config('TimeZone', APP_TIMEZONE) on every new
-- connection. Reading current_setting('TimeZone') here keeps PHP and
-- PostgreSQL on the same calendar day when APP_TIMEZONE is overridden, while
-- the default (Asia/Jakarta) keeps the previous behaviour unchanged.

CREATE OR REPLACE FUNCTION app_today() RETURNS DATE
LANGUAGE sql STABLE
AS $$ SELECT (now() AT TIME ZONE current_setting('TimeZone'))::date $$;
