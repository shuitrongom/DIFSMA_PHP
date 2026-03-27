-- Tabla de configuración de la página de Voluntariado
CREATE TABLE IF NOT EXISTS `voluntariado_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `logo_path` VARCHAR(500) DEFAULT NULL,
  `lema` VARCHAR(300) DEFAULT 'UNIDOS SÍ, TENDEMOS LA MANO',
  `mision_titulo` VARCHAR(200) DEFAULT '¿Qué es ser voluntario?',
  `mision_texto` TEXT,
  `mision_subtitulo` VARCHAR(200) DEFAULT '¿Cómo puedo aportar?',
  `mision_subtexto` TEXT,
  `vision_texto` TEXT,
  `valores_texto` TEXT,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de imágenes de voluntariado (galería inferior)
CREATE TABLE IF NOT EXISTS `voluntariado_imagenes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `imagen_path` VARCHAR(500) NOT NULL,
  `orden` INT DEFAULT 1,
  `activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar registro inicial
INSERT INTO `voluntariado_config` (`id`, `lema`, `mision_titulo`, `mision_texto`, `mision_subtitulo`, `mision_subtexto`, `vision_texto`, `valores_texto`) VALUES
(1, 'UNIDOS SÍ, TENDEMOS LA MANO',
'¿Qué es ser voluntario?',
'Establecer un compromiso de ayuda con la población más necesitada, compartiendo su tiempo, talento y recursos de manera desinteresada. Ser voluntario es saber amar y estar dispuesto a llevar a la práctica el amor por el ser humano a través del servicio',
'¿Cómo puedo aportar?',
'Se puede participar en estas actividades:\n- Realizar campañas de recaudación permanente de artículos en especie.\n- Vincular al sector privado para que apoye mediante donativos.\n- Distribuir los donativos entre los sectores más vulnerables.',
'Lograr una transformación social y solidaria generando acciones y servicios para todos los ciudadanos, construyendo una cultura de derechos para niñas, niños, adolescentes, jóvenes, adultos mayores, personas con discapacidad, mujeres y familias atenquenses más vulnerables.',
'Compromiso social\nSolidaridad\nResponsabilidad\nEmpatía');
