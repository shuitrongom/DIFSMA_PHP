-- Migración: agregar campo password_changed_at a tabla admin
ALTER TABLE `admin`
  ADD COLUMN `password_changed_at` DATETIME DEFAULT NULL AFTER `email`;

-- Inicializar con fecha actual para no forzar cambio inmediato a usuarios existentes
UPDATE `admin` SET `password_changed_at` = NOW() WHERE `password_changed_at` IS NULL;
