-- Migración programas v2: secciones dinámicas con páginas propias

-- 1. Quitar columna contenido de programas_secciones (ya no se usa)
ALTER TABLE `programas_secciones` DROP COLUMN `contenido`;

-- 2. Agregar slug a programas_secciones para identificar la página
ALTER TABLE `programas_secciones`
    ADD COLUMN `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `titulo`;

-- 3. Tabla de contenido de cada página de sección
CREATE TABLE IF NOT EXISTS `programas_secciones_paginas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `seccion_id` int NOT NULL,
  `imagen1_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `texto1` text COLLATE utf8mb4_unicode_ci,
  `imagen2_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `texto2` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seccion_id` (`seccion_id`),
  CONSTRAINT `fk_psp_seccion` FOREIGN KEY (`seccion_id`) REFERENCES `programas_secciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
