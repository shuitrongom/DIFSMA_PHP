-- Migración: soporte de video en slider_principal
ALTER TABLE `slider_principal`
  ADD COLUMN `tipo` ENUM('imagen','video') NOT NULL DEFAULT 'imagen' AFTER `imagen_path`;
