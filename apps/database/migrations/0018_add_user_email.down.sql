-- 0018_add_user_email.down.sql

DROP INDEX IF EXISTS users_email_unique_idx;

ALTER TABLE users
    DROP COLUMN IF EXISTS email;
