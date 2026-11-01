-- Rollback 0012_anonymous_sessions.sql
DELETE FROM sessions WHERE user_id IS NULL;

ALTER TABLE sessions ALTER COLUMN user_id SET NOT NULL;
