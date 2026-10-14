-- Rollback 0009_sessions_payload.sql
ALTER TABLE sessions DROP COLUMN payload;
