-- 0012_anonymous_sessions.sql
-- Public patient-flow drafts (registration draft, ticket, portal unlock) need
-- session persistence before login. Allow owner-less session rows while keeping
-- authenticated sessions linked to users.

ALTER TABLE sessions ALTER COLUMN user_id DROP NOT NULL;
