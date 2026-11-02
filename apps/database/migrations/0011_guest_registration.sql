-- 0011_guest_registration.sql
-- Self-service patient registration: a non-login guest actor, complaint at
-- registration, a QR ticket token, and an Asia/Jakarta queue date.
--
-- created_by stays NOT NULL. Self-service rows use the shared `guest` system
-- account below; registration_source records how the row was created. The
-- guest account has no credentials and is inactive, so it cannot log in.

CREATE FUNCTION app_today() RETURNS DATE
LANGUAGE sql STABLE
AS $$ SELECT (now() AT TIME ZONE 'Asia/Jakarta')::date $$;

INSERT INTO users (username, full_name, role, is_active)
VALUES ('guest', 'Pendaftaran Mandiri', 'perawat', FALSE)
ON CONFLICT (username) DO NOTHING;

ALTER TABLE visits
    ADD COLUMN registration_source VARCHAR(10) NOT NULL DEFAULT 'staff'
        CHECK (registration_source IN ('staff', 'guest')),
    ADD COLUMN complaint TEXT,
    ADD COLUMN ticket_token UUID NOT NULL DEFAULT gen_random_uuid();

CREATE UNIQUE INDEX visits_ticket_token_idx ON visits (ticket_token);
CREATE INDEX visits_guest_today_idx
    ON visits (patient_id, service_id, queue_date)
    WHERE status IN ('menunggu', 'dilayani');

-- Keep the staff path on the same WIB date as the guest path.
CREATE OR REPLACE FUNCTION register_visit(
    p_patient_id UUID,
    p_service_id SMALLINT,
    p_created_by UUID
) RETURNS TABLE (visit_id UUID, queue_number INT)
LANGUAGE plpgsql
SET search_path = public
AS $$
DECLARE
    v_number INT;
    v_visit  UUID;
    v_date   DATE := app_today();
BEGIN
    INSERT INTO queue_counters (service_id, queue_date, last_number)
    VALUES (p_service_id, v_date, 1)
    ON CONFLICT (service_id, queue_date)
    DO UPDATE SET last_number = queue_counters.last_number + 1
    RETURNING last_number INTO v_number;

    INSERT INTO visits (patient_id, service_id, queue_number, queue_date, created_by)
    VALUES (p_patient_id, p_service_id, v_number, v_date, p_created_by)
    RETURNING id INTO v_visit;

    RETURN QUERY SELECT v_visit, v_number;
END;
$$;

CREATE FUNCTION register_guest_visit(
    p_patient_id UUID,
    p_service_id SMALLINT,
    p_complaint  TEXT
) RETURNS TABLE (visit_id UUID, queue_number INT, ticket_token UUID)
LANGUAGE plpgsql
SET search_path = public
AS $$
DECLARE
    v_number INT;
    v_visit  UUID;
    v_token  UUID;
    v_guest  UUID;
    v_date   DATE := app_today();
BEGIN
    SELECT id INTO v_guest FROM users WHERE username = 'guest';

    IF v_guest IS NULL THEN
        RAISE EXCEPTION 'Akun sistem guest tidak ditemukan' USING ERRCODE = '22023';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM services WHERE id = p_service_id AND is_active
    ) THEN
        RAISE EXCEPTION 'Layanan tidak aktif' USING ERRCODE = '22023';
    END IF;

    -- Make the audit trigger record the system actor for this transaction.
    PERFORM set_config('app.actor_id', v_guest::text, true);

    INSERT INTO queue_counters (service_id, queue_date, last_number)
    VALUES (p_service_id, v_date, 1)
    ON CONFLICT (service_id, queue_date)
    DO UPDATE SET last_number = queue_counters.last_number + 1
    RETURNING last_number INTO v_number;

    INSERT INTO visits (
        patient_id, service_id, queue_number, queue_date,
        complaint, registration_source, created_by
    ) VALUES (
        p_patient_id, p_service_id, v_number, v_date,
        p_complaint, 'guest', v_guest
    )
    RETURNING id, visits.ticket_token INTO v_visit, v_token;

    RETURN QUERY SELECT v_visit, v_number, v_token;
END;
$$;

REVOKE EXECUTE ON FUNCTION register_visit(UUID, SMALLINT, UUID) FROM PUBLIC;
REVOKE EXECUTE ON FUNCTION register_guest_visit(UUID, SMALLINT, TEXT) FROM PUBLIC;
