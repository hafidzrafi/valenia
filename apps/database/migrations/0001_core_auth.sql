-- 0001_core_auth.sql
-- Akun, kredensial, dan sesi login (PRD Bab 6).
-- UUIDv7 dipakai bawaan PostgreSQL 18 (tanpa ekstensi).

CREATE TABLE users (
    id          UUID PRIMARY KEY DEFAULT uuidv7(),
    username    VARCHAR(40)  NOT NULL UNIQUE,
    full_name   VARCHAR(120) NOT NULL,
    role        VARCHAR(10)  NOT NULL CHECK (role IN ('admin', 'perawat')),
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ  NOT NULL DEFAULT now()
);

CREATE TABLE credentials (
    id                    UUID PRIMARY KEY DEFAULT uuidv7(),
    user_id               UUID        NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    provider              VARCHAR(20) NOT NULL DEFAULT 'credential',
    password_hash         TEXT        NOT NULL,
    must_change_password  BOOLEAN     NOT NULL DEFAULT TRUE,
    failed_attempts       SMALLINT    NOT NULL DEFAULT 0,
    locked_until          TIMESTAMPTZ,
    last_login_at         TIMESTAMPTZ,
    created_at            TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at            TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (user_id, provider)
);

CREATE TABLE sessions (
    id           UUID PRIMARY KEY DEFAULT uuidv7(),
    user_id      UUID        NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token_hash   CHAR(64)    NOT NULL UNIQUE,
    ip_address   INET,
    user_agent   TEXT,
    created_at   TIMESTAMPTZ NOT NULL DEFAULT now(),
    last_seen_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    expires_at   TIMESTAMPTZ NOT NULL
);

CREATE INDEX sessions_user_id_idx    ON sessions (user_id);
CREATE INDEX sessions_expires_at_idx ON sessions (expires_at);
