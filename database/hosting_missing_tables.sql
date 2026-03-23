-- =============================================================================
-- Tablas faltantes en el hosting — Ejecutar en DBeaver
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Noticias (la tabla real se llama noticias_imagenes)
CREATE TABLE IF NOT EXISTS `noticias_imagenes` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `imagen_path`   VARCHAR(500) NOT NULL,
    `fecha_noticia` DATE         NOT NULL,
    `activo`        TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`    DATETIME              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_fecha` (`fecha_noticia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEAC — Conceptos (la tabla que falta, no seac_trimestres)
CREATE TABLE IF NOT EXISTS `seac_conceptos` (
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `bloque_id` INT          NOT NULL,
    `numero`    INT          NOT NULL,
    `nombre`    VARCHAR(500) NOT NULL,
    `orden`     INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_seac_concepto_bloque`
        FOREIGN KEY (`bloque_id`) REFERENCES `seac_bloques` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cuenta Pública — Bloques
CREATE TABLE IF NOT EXISTS `cp_bloques` (
    `id`    INT  NOT NULL AUTO_INCREMENT,
    `anio`  YEAR NOT NULL,
    `orden` INT  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_cp_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cuenta Pública — Títulos
CREATE TABLE IF NOT EXISTS `cp_titulos` (
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `bloque_id` INT          NOT NULL,
    `nombre`    VARCHAR(500) NOT NULL,
    `orden`     INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_cp_titulo_bloque`
        FOREIGN KEY (`bloque_id`) REFERENCES `cp_bloques` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cuenta Pública — Conceptos
CREATE TABLE IF NOT EXISTS `cp_conceptos` (
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `titulo_id` INT          NOT NULL,
    `numero`    INT          NOT NULL,
    `nombre`    VARCHAR(500) NOT NULL,
    `pdf_path`  VARCHAR(500)          DEFAULT NULL,
    `orden`     INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_cp_concepto_titulo`
        FOREIGN KEY (`titulo_id`) REFERENCES `cp_titulos` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Presupuesto Anual — Bloques
CREATE TABLE IF NOT EXISTS `pa_bloques` (
    `id`    INT  NOT NULL AUTO_INCREMENT,
    `anio`  YEAR NOT NULL,
    `orden` INT  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pa_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Presupuesto Anual — Conceptos
CREATE TABLE IF NOT EXISTS `pa_conceptos` (
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `bloque_id` INT          NOT NULL,
    `nombre`    VARCHAR(500) NOT NULL,
    `orden`     INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_pa_concepto_bloque`
        FOREIGN KEY (`bloque_id`) REFERENCES `pa_bloques` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Presupuesto Anual — PDFs
CREATE TABLE IF NOT EXISTS `pa_pdfs` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `concepto_id` INT          NOT NULL,
    `sub_anio`    YEAR         NOT NULL,
    `pdf_path`    VARCHAR(500)          DEFAULT NULL,
    `orden`       INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pa_concepto_anio` (`concepto_id`, `sub_anio`),
    CONSTRAINT `fk_pa_pdf_concepto`
        FOREIGN KEY (`concepto_id`) REFERENCES `pa_conceptos` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PAE — Títulos
CREATE TABLE IF NOT EXISTS `pae_titulos` (
    `id`     INT          NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(500) NOT NULL,
    `orden`  INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- PAE — PDFs
CREATE TABLE IF NOT EXISTS `pae_pdfs` (
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `titulo_id` INT          NOT NULL,
    `anio`      YEAR         NOT NULL,
    `pdf_path`  VARCHAR(500)          DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pae_titulo_anio` (`titulo_id`, `anio`),
    CONSTRAINT `fk_pae_pdf_titulo`
        FOREIGN KEY (`titulo_id`) REFERENCES `pae_titulos` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Matrices de Indicadores
CREATE TABLE IF NOT EXISTS `mi_pdfs` (
    `id`       INT          NOT NULL AUTO_INCREMENT,
    `anio`     YEAR         NOT NULL,
    `pdf_path` VARCHAR(500)          DEFAULT NULL,
    `orden`    INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_mi_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CONAC — Conceptos
CREATE TABLE IF NOT EXISTS `conac_conceptos` (
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `bloque_id` INT          NOT NULL,
    `numero`    INT          NOT NULL,
    `nombre`    VARCHAR(500) NOT NULL,
    `orden`     INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_conac_concepto_bloque`
        FOREIGN KEY (`bloque_id`) REFERENCES `conac_bloques` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Banner Institucional
CREATE TABLE IF NOT EXISTS `institucion_banner` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `imagen_path` VARCHAR(500) NOT NULL DEFAULT 'img/institucion.png',
    `updated_at`  DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Programas
CREATE TABLE IF NOT EXISTS `programas` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(200) NOT NULL,
    `imagen_path` VARCHAR(500)          DEFAULT NULL,
    `orden`       INT          NOT NULL DEFAULT 0,
    `activo`      TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Secciones de programas
CREATE TABLE IF NOT EXISTS `programas_secciones` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `programa_id` INT          NOT NULL,
    `titulo`      VARCHAR(300) NOT NULL,
    `contenido`   TEXT         NOT NULL,
    `orden`       INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_programa_seccion`
        FOREIGN KEY (`programa_id`) REFERENCES `programas` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
