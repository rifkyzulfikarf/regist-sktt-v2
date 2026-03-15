CREATE TABLE IF NOT EXISTS `participants` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `participant_number` VARCHAR(100) NOT NULL,
  `full_name` VARCHAR(255) NULL,
  `position` VARCHAR(255) NOT NULL,
  `birth_date` DATE NOT NULL,
  `work_unit` VARCHAR(255) NULL,
  `raw_data` LONGTEXT NULL,
  `imported_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participants_unique_number` (`participant_number`),
  KEY `participants_idx_position` (`position`),
  KEY `participants_idx_work_unit` (`work_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `work_unit` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `participant_id` BIGINT UNSIGNED NOT NULL,
  `first_scanned_at` DATETIME NULL,
  `first_scanned_by` INT UNSIGNED NULL,
  `scan_count` INT NOT NULL DEFAULT 0,
  `last_scanned_at` DATETIME NULL,
  `last_scanned_by` INT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_unique_participant` (`participant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_scan_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `participant_id` BIGINT UNSIGNED NULL,
  `admin_id` INT UNSIGNED NULL,
  `barcode_value` TEXT NULL,
  `status` VARCHAR(20) NOT NULL,
  `message` TEXT NULL,
  `scanned_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `scan_events_idx_participant` (`participant_id`),
  KEY `scan_events_idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admins` (`username`, `password_hash`, `work_unit`, `created_at`, `updated_at`)
SELECT 'admin', '\$2y\$10\$gaw1at3w.08RXNGXgD8rwO1p8/i7G2rrQi8/jQANXXI6QRec7vkQ6', NULL, NOW(), NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM `admins` WHERE `username` = 'admin'
);
