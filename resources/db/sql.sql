CREATE TABLE landlords (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(255),
  entity_name VARCHAR(255),
  contact_person VARCHAR(255),
  telephone VARCHAR(50),
  cell_number VARCHAR(50),
  email VARCHAR(255) UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE property_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  status VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE property_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE properties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  building_name VARCHAR(255),
  blurb TEXT,
  type_id INT, -- FK to property_types(id)
  status_type INT, -- FK to property_status(id)
  erf_no VARCHAR(100),
  erf_size VARCHAR(100),
  gla VARCHAR(100), -- Gross Lettable Area
  zoning VARCHAR(100),
  property_locale VARCHAR(255),
  latitude DECIMAL(10, 7),
  longitude DECIMAL(10, 7),
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE tenants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(255),
  entity_name VARCHAR(255),
  contact_person VARCHAR(255),
  telephone VARCHAR(50),
  cell_number VARCHAR(50),
  email VARCHAR(255) UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE unit_details (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    unit_type VARCHAR(100) NOT NULL,
    company_as_listing_broker BOOLEAN DEFAULT FALSE,
    listing_broker VARCHAR(100) NOT NULL,
    deal_file_id VARCHAR(100) NULL,
    availability VARCHAR(100) NULL,
    lease_expiry DATE NULL,
    unit_no VARCHAR(50) NULL,
    unit_size DECIMAL(10,2) NOT NULL,
    gross_rental DECIMAL(12,2) NULL,
    sale_price DECIMAL(15,2) NULL,
    yield_percentage DECIMAL(5,2) NULL,
    parking_bays INT NULL,
    parking_rental DECIMAL(10,2) NULL,
    tenant VARCHAR(100) NULL,
    contact VARCHAR(100) NULL,
    tel VARCHAR(50) NULL,
    cell VARCHAR(50) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE property_managers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    entity_name VARCHAR(255) NULL,
    manager_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NULL,
    telephone VARCHAR(50) NULL,
    cell_number VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);
