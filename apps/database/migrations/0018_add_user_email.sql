-- 0018_add_user_email.sql
-- Staff accounts may carry an email address used as an alternative login
-- identifier. The mockup labels the login field "EMAIL / NIP", so the lookup in
-- authenticate() accepts username, email, or a numeric NIP used as username.
--
-- NULL is allowed on purpose: existing accounts keep working, and PostgreSQL
-- permits any number of NULLs in a unique column, so email-less accounts never
-- collide with each other.

ALTER TABLE users
    ADD COLUMN email VARCHAR(120);

CREATE UNIQUE INDEX users_email_unique_idx
    ON users (lower(email))
    WHERE email IS NOT NULL;
