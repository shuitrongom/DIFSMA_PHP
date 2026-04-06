-- Migración: agregar columnas de dispositivo y hostname al historial
ALTER TABLE `admin_historial`
  ADD COLUMN `dispositivo` VARCHAR(20) DEFAULT NULL COMMENT 'pc, celular, tablet' AFTER `ip`,
  ADD COLUMN `hostname` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del equipo resuelto por DNS' AFTER `dispositivo`;
