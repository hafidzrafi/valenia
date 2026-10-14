-- Rollback 0006_audit_logs.sql
DROP TRIGGER IF EXISTS examinations_audit ON examinations;
DROP TRIGGER IF EXISTS visits_audit ON visits;
DROP FUNCTION IF EXISTS audit_change();
DROP TABLE IF EXISTS audit_logs;
