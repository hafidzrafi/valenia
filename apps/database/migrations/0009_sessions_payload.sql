-- 0009_sessions_payload.sql
-- FR-AUTH-02: payload column so the PHP session save handler (see
-- app/lib/session.php) can persist $_SESSION in PostgreSQL.
ALTER TABLE sessions ADD COLUMN payload TEXT;
