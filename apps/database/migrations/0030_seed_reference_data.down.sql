-- 0030_seed_reference_data.down.sql
-- Remove only the reference rows this migration owns. Services are kept when a
-- visit already refers to them, because visits.service_id is a foreign key.

DELETE FROM doctor_schedules ds
USING doctors d
WHERE ds.doctor_id = d.id
  AND d.full_name IN (
      'dr. Ayu Pramesti',
      'drg. Bagas Wardana',
      'Rani Kusuma, M.Psi.'
  );

DELETE FROM doctors d
USING services s
WHERE d.service_id = s.id
  AND d.full_name IN (
      'dr. Ayu Pramesti',
      'drg. Bagas Wardana',
      'Rani Kusuma, M.Psi.'
  )
  AND s.name IN ('Poli Umum', 'Poli Gigi', 'Konseling/Psikologi');

DELETE FROM services s
WHERE s.name IN ('Poli Umum', 'Poli Gigi', 'Konseling/Psikologi')
  AND NOT EXISTS (SELECT 1 FROM visits v WHERE v.service_id = s.id);
