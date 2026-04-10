CREATE TABLE IF NOT EXISTS `contacto_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo1` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT 'SERVICIOS MÉDICOS',
  `titulo2` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT 'CLASES Y TALLERES',
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `telefono` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horario` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `contacto_config` (`id`, `titulo1`, `titulo2`, `direccion`, `telefono`, `horario`, `correo`) VALUES
(1, 'SERVICIOS MÉDICOS', 'CLASES Y TALLERES',
 'Mariano Matamoros 310, Barrio de la Concepción CP 52105,\nSan Mateo Atenco, Méx.',
 '722 970 77 86',
 'Horario de Lunes a Viernes\n8:00 am a 3:30 pm',
 'adultomayor@difsanmateoatenco.gob.mx');
