CREATE TABLE IF NOT EXISTS `slider_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `seccion` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'slider_principal, slider_comunica, noticias',
  `autoplay_delay` INT NOT NULL DEFAULT 3000 COMMENT 'Tiempo en milisegundos',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_seccion` (`seccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `slider_config` (`seccion`, `autoplay_delay`) VALUES
  ('slider_principal', 3200),
  ('slider_comunica',  3200),
  ('noticias',         3000)
ON DUPLICATE KEY UPDATE `seccion` = `seccion`;
