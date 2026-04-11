-- Agregar campo de enlace opcional a la imagen del programa
ALTER TABLE `programas` ADD COLUMN `imagen_link` VARCHAR(500) DEFAULT NULL AFTER `imagen_path`;
