CREATE TABLE IF NOT EXISTS `tramites_galeria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tramite_id` INT NOT NULL,
  `imagen_path` VARCHAR(500) NOT NULL,
  `orden` INT NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_tramite_id` (`tramite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
