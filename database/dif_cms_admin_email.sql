-- Migración: agregar campo email a tabla admin
ALTER TABLE `admin`
  ADD COLUMN `email` VARCHAR(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `nombre`;
