-- =========================================================
-- RBAC Admin SKTT: Upgrade tabel admins + seed akun admin_unit
-- Target: MySQL/MariaDB (existing database)
-- =========================================================

START TRANSACTION;

-- 1) Tambah kolom role jika belum ada
SET @col_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'admins'
    AND COLUMN_NAME = 'role'
);

SET @sql_add_role := IF(
  @col_exists = 0,
  "ALTER TABLE `admins` ADD COLUMN `role` VARCHAR(20) NOT NULL DEFAULT 'admin_unit' AFTER `password_hash`",
  'SELECT 1'
);
PREPARE stmt_add_role FROM @sql_add_role;
EXECUTE stmt_add_role;
DEALLOCATE PREPARE stmt_add_role;

-- 1b) Buat tabel admin_login_logs jika belum ada
CREATE TABLE IF NOT EXISTS `admin_login_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT UNSIGNED NOT NULL,
  `admin_role` VARCHAR(20) NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `status` VARCHAR(20) NOT NULL,
  `message` TEXT NULL,
  `login_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_login_logs_idx_admin` (`admin_id`),
  KEY `admin_login_logs_idx_time` (`login_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Normalisasi data existing role
UPDATE `admins`
SET `role` = 'admin_unit'
WHERE `role` IS NULL OR `role` = '';

-- 3) Pastikan akun admin utama menjadi super_admin
UPDATE `admins`
SET `role` = 'super_admin'
WHERE `username` = 'admin';

-- 4) Seed akun admin_unit otomatis dari DISTINCT participants.work_unit
-- Password awal akun hasil seed: AdminUnit123!
-- Username otomatis: admin_unit_<10-char-md5(work_unit)>
-- Contoh: admin_unit_a1b2c3d4e5
INSERT INTO `admins` (`username`, `password_hash`, `role`, `work_unit`, `created_at`, `updated_at`)
SELECT
  CONCAT('admin_unit_', SUBSTRING(MD5(TRIM(p.`work_unit`)), 1, 10)) AS `username`,
  '$2y$10$vCQs.HEdHCCYloLBcGYt5eNpuT/69R2q6ss9tNdmeUx4MHWJNaS7S' AS `password_hash`,
  'admin_unit' AS `role`,
  TRIM(p.`work_unit`) AS `work_unit`,
  NOW() AS `created_at`,
  NOW() AS `updated_at`
FROM (
  SELECT DISTINCT `work_unit`
  FROM `participants`
  WHERE `work_unit` IS NOT NULL
    AND TRIM(`work_unit`) <> ''
) p
LEFT JOIN `admins` a
  ON a.`role` = 'admin_unit'
 AND TRIM(a.`work_unit`) = TRIM(p.`work_unit`)
WHERE a.`id` IS NULL
ON DUPLICATE KEY UPDATE
  `password_hash` = VALUES(`password_hash`),
  `role` = VALUES(`role`),
  `work_unit` = VALUES(`work_unit`),
  `updated_at` = NOW();

COMMIT;

-- Verifikasi cepat
SELECT `id`, `username`, `role`, `work_unit` FROM `admins` ORDER BY `username`;
