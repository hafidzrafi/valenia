-- 0020_add_examination_note.sql
-- Free-text note for the "Catatan tambahan / opsional" field on the examination
-- input page. Nullable: older examinations simply have no note, and the input
-- treats it as optional while diagnosis and treatment stay required.

ALTER TABLE examinations
    ADD COLUMN note TEXT;
