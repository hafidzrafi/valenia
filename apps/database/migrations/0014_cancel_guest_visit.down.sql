-- Rollback 0014_cancel_guest_visit.sql
DROP FUNCTION IF EXISTS cancel_guest_visit(UUID, UUID);
