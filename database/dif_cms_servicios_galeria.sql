-- Galería de fotos para la página de sección de servicios/programas
CREATE TABLE IF NOT EXISTS `servicios_galeria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `imagen_path` VARCHAR(500) NOT NULL,
  `orden` INT NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
