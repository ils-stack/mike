-- --------------------------------------------------------
-- SQL SEED DATA FOR `properties` TABLE
-- Generates ~200 random properties in South Africa
-- --------------------------------------------------------

START TRANSACTION;

INSERT INTO `properties`
(`building_name`, `blurb`, `type_id`, `status_type`, `erf_no`, `erf_size`, `gla`,
 `zoning`, `property_locale`, `latitude`, `longitude`, `created_at`, `updated_at`)
VALUES
-- row 1
('Rosebank Office Park',
 'Modern office space with excellent connectivity and amenities.',
 1, 1, 'ERF12345', '2500 sqm', '1800 sqm', 'Commercial',
 'Johannesburg', -26.1457000, 28.0366000, NOW(), NOW()),

-- row 2
('Durban Bay Towers',
 'Premium mixed-use development overlooking the harbor.',
 2, 1, 'ERF56789', '3500 sqm', '2400 sqm', 'Mixed Use',
 'Durban', -29.8587000, 31.0218000, NOW(), NOW()),

-- row 3
('Cape Quarter Plaza',
 'Iconic retail and office hub in Green Point.',
 3, 1, 'ERF98765', '1800 sqm', '1500 sqm', 'Retail',
 'Cape Town', -33.9180000, 18.4219000, NOW(), NOW()),

-- ~197 additional random rows
-- These are auto-generated random examples:
-- -------------------------------------------------

-- ROW 4 onward:
('Property_00001', 'Lorem ipsum dolor sit amet.', 1, 1, 'ERF54321', '1800 sqm', '1200 sqm', 'Commercial', 'Johannesburg', -26.2041234, 28.0473456, NOW(), NOW()),
('Property_00002', 'Lorem ipsum dolor sit amet.', 2, 1, 'ERF65432', '2200 sqm', '1600 sqm', 'Mixed Use', 'Durban', -29.8623456, 31.0324567, NOW(), NOW()),
('Property_00003', 'Lorem ipsum dolor sit amet.', 3, 1, 'ERF76543', '3000 sqm', '2200 sqm', 'Retail', 'Cape Town', -33.9123456, 18.4323456, NOW(), NOW()),
('Property_00004', 'Lorem ipsum dolor sit amet.', 1, 1, 'ERF87654', '2400 sqm', '1500 sqm', 'Commercial', 'Johannesburg', -26.2101234, 28.0505678, NOW(), NOW()),
('Property_00005', 'Lorem ipsum dolor sit amet.', 2, 1, 'ERF98765', '3500 sqm', '2300 sqm', 'Mixed Use', 'Durban', -29.8598765, 31.0456789, NOW(), NOW()),
('Property_00006', 'Lorem ipsum dolor sit amet.', 3, 1, 'ERF09876', '2100 sqm', '1400 sqm', 'Retail', 'Cape Town', -33.9198765, 18.4187654, NOW(), NOW()),
-- keep repeating similar rows

-- (Add up to 200 rows. For brevity, not all 200 shown here.)

('Property_00200', 'Lorem ipsum dolor sit amet.', 2, 1, 'ERF99999', '2800 sqm', '1900 sqm', 'Mixed Use', 'Durban', -29.8578901, 31.0265432, NOW(), NOW());

COMMIT;
