-- 0010_auth_events.sql
-- FR-AUTH-01/07, NFR-SEC-06: authentication event trail (login success/failure,
-- lockout, logout, password change/reset), separate from the row-diff audit_logs.

CREATE TABLE auth_events (
    id         BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    event      TEXT NOT NULL CHECK (event IN (
                   'login_success', 'login_failure', 'locked',
                   'logout', 'password_change', 'password_reset'
               )),
    username   TEXT,
    user_id    UUID REFERENCES users(id) ON DELETE SET NULL,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX auth_events_username_created_idx ON auth_events (username, created_at);
CREATE INDEX auth_events_created_at_idx       ON auth_events (created_at);
