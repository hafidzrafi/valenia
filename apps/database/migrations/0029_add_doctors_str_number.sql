-- 0029_add_doctors_str_number.sql
-- STR is nullable and unique when present. Display metadata only: the format is
-- unverified and there is no expiry date, so it does not gate practice.

ALTER TABLE doctors
    ADD COLUMN str_number VARCHAR(32);

CREATE UNIQUE INDEX doctors_str_number_unique_idx
    ON doctors (str_number)
    WHERE str_number IS NOT NULL;
