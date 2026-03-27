-- ============================================================================
-- Transparencia Dinámica: Secciones con plantillas reutilizables
-- ============================================================================

-- Registro de secciones dinámicas
CREATE TABLE IF NOT EXISTS `trans_secciones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(300) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `plantilla` ENUM('seac','cuenta_publica','presupuesto_anual','pae','matrices','conac','financiero') NOT NULL,
  `icono` VARCHAR(100) DEFAULT 'bi-file-earmark-text',
  `activo` TINYINT(1) DEFAULT 1,
  `orden` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_trans_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bloques por año (para plantillas: seac, cuenta_publica, presupuesto_anual, conac, financiero)
CREATE TABLE IF NOT EXISTS `trans_bloques` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `seccion_id` INT NOT NULL,
  `anio` YEAR NOT NULL,
  `orden` INT DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_trans_bloque` (`seccion_id`, `anio`),
  FOREIGN KEY (`seccion_id`) REFERENCES `trans_secciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Títulos/módulos (para plantillas: cuenta_publica, pae)
CREATE TABLE IF NOT EXISTS `trans_titulos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `seccion_id` INT NOT NULL,
  `bloque_id` INT DEFAULT NULL,
  `nombre` VARCHAR(500) NOT NULL,
  `orden` INT DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`seccion_id`) REFERENCES `trans_secciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`bloque_id`) REFERENCES `trans_bloques`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conceptos (para plantillas: seac, cuenta_publica, presupuesto_anual, conac, financiero)
CREATE TABLE IF NOT EXISTS `trans_conceptos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `seccion_id` INT NOT NULL,
  `bloque_id` INT DEFAULT NULL,
  `titulo_id` INT DEFAULT NULL,
  `nombre` VARCHAR(500) NOT NULL,
  `pdf_path` VARCHAR(500) DEFAULT NULL,
  `orden` INT DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`seccion_id`) REFERENCES `trans_secciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`bloque_id`) REFERENCES `trans_bloques`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`titulo_id`) REFERENCES `trans_titulos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PDFs (para plantillas con PDFs separados: seac/conac trimestrales, presupuesto_anual sub-años, pae por año, matrices por año)
CREATE TABLE IF NOT EXISTS `trans_pdfs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `seccion_id` INT NOT NULL,
  `concepto_id` INT DEFAULT NULL,
  `titulo_id` INT DEFAULT NULL,
  `anio` YEAR DEFAULT NULL,
  `trimestre` TINYINT DEFAULT NULL,
  `pdf_path` VARCHAR(500) DEFAULT NULL,
  `orden` INT DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`seccion_id`) REFERENCES `trans_secciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`concepto_id`) REFERENCES `trans_conceptos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`titulo_id`) REFERENCES `trans_titulos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
