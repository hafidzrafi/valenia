-- Rollback 0007_register_visit.sql
DROP FUNCTION IF EXISTS register_visit(UUID, SMALLINT, UUID);
