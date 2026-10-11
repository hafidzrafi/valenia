-- 0006_audit_logs.sql
-- Jejak audit perubahan data medis (PRD Bab 6) dan trigger diff JSONB.

CREATE TABLE audit_logs (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    table_name  TEXT        NOT NULL,
    row_id      UUID        NOT NULL,
    action      TEXT        NOT NULL CHECK (action IN ('INSERT', 'UPDATE', 'DELETE')),
    actor_id    UUID,
    old_data    JSONB,
    new_data    JSONB,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX audit_logs_table_row_idx  ON audit_logs (table_name, row_id);
CREATE INDEX audit_logs_created_at_idx ON audit_logs (created_at);

CREATE OR REPLACE FUNCTION audit_change() RETURNS trigger
LANGUAGE plpgsql AS $$
BEGIN
    INSERT INTO audit_logs (table_name, row_id, action, actor_id, old_data, new_data)
    VALUES (
        TG_TABLE_NAME,
        CASE WHEN TG_OP = 'DELETE' THEN OLD.id ELSE NEW.id END,
        TG_OP,
        nullif(current_setting('app.actor_id', true), '')::uuid,
        CASE WHEN TG_OP = 'INSERT' THEN NULL ELSE to_jsonb(OLD) END,
        CASE WHEN TG_OP = 'DELETE' THEN NULL ELSE to_jsonb(NEW) END
    );

    RETURN NULL;
END;
$$;

CREATE TRIGGER visits_audit
    AFTER INSERT OR UPDATE OR DELETE ON visits
    FOR EACH ROW EXECUTE FUNCTION audit_change();

CREATE TRIGGER examinations_audit
    AFTER INSERT OR UPDATE OR DELETE ON examinations
    FOR EACH ROW EXECUTE FUNCTION audit_change();

-- NFR-SEC-11: jejak audit append-only. Akun aplikasi sebaiknya bukan pemilik
-- tabel; cabut juga hak UPDATE/DELETE dari peran aplikasi saat provisioning.
REVOKE UPDATE, DELETE ON audit_logs FROM PUBLIC;
