START TRANSACTION;

INSERT INTO `properties`
(`building_name`, `blurb`, `type_id`, `status_type`,
 `erf_no`, `erf_size`, `gla`, `zoning`,
 `property_locale`, `latitude`, `longitude`,
 `created_at`, `updated_at`)
SELECT
  CONCAT('Property_', LPAD(n, 3, '0')) AS building_name,
  'Lorem ipsum dolor sit amet.' AS blurb,
  ELT(FLOOR(1 + (RAND()*3)), 1, 2, 3) AS type_id,
  1 AS status_type,
  CONCAT('ERF', 10000 + n) AS erf_no,
  CONCAT(FLOOR(1500 + (RAND() * 1500)), ' sqm') AS erf_size,
  CONCAT(FLOOR(1000 + (RAND() * 1000)), ' sqm') AS gla,
  ELT(FLOOR(1 + (RAND()*3)), 'Commercial', 'Mixed Use', 'Retail') AS zoning,
  ELT(FLOOR(1 + (RAND()*3)), 'Johannesburg', 'Durban', 'Cape Town') AS property_locale,
  CASE
    WHEN RAND() < 0.33 THEN -26.2041 + (RAND()*0.02)
    WHEN RAND() < 0.66 THEN -29.8587 + (RAND()*0.02)
    ELSE -33.9180 + (RAND()*0.02)
  END AS latitude,
  CASE
    WHEN RAND() < 0.33 THEN 28.0473 + (RAND()*0.02)
    WHEN RAND() < 0.66 THEN 31.0218 + (RAND()*0.02)
    ELSE 18.4219 + (RAND()*0.02)
  END AS longitude,
  NOW(), NOW()
FROM (
  SELECT @row := @row + 1 AS n
  FROM (
    SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
    SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL
    SELECT 8 UNION ALL SELECT 9
  ) a,
  (
    SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
    SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL
    SELECT 8 UNION ALL SELECT 9
  ) b,
  (SELECT @row := 0) c
) numbers
WHERE n <= 200;

COMMIT;
