-- =============================================================================
-- database/schema.sql — Esquema completo DIF CMS
-- MySQL 5.7+ / MariaDB 10.3+
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- Administrador (único registro)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin` (
    `id`       INT          NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50)  NOT NULL,
    `password` VARCHAR(255) NOT NULL COMMENT 'bcrypt hash via password_hash()',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Intentos de login fallidos (rate limiting por IP)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id`           INT         NOT NULL AUTO_INCREMENT,
    `ip`           VARCHAR(45) NOT NULL COMMENT 'IPv4 o IPv6',
    `attempted_at` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ip_time` (`ip`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Slider Principal (carrusel pantalla completa en index)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `slider_principal` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `imagen_path` VARCHAR(500) NOT NULL,
    `orden`       INT          NOT NULL DEFAULT 0,
    `activo`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Slider DIF Comunica (Swiper 3D en index)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `slider_comunica` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `imagen_path` VARCHAR(500) NOT NULL,
    `orden`       INT          NOT NULL DEFAULT 0,
    `activo`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Noticias por día (compartido entre index.php y noticias.php)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `noticias_imagenes` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `imagen_path`   VARCHAR(500) NOT NULL,
    `fecha_noticia` DATE         NOT NULL,
    `activo`        TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`    DATETIME              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_fecha` (`fecha_noticia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Presidencia
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `presidencia` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `imagen_path` VARCHAR(500)          DEFAULT NULL,
    `nombre`      VARCHAR(200) NOT NULL,
    `cargo`       VARCHAR(200) NOT NULL,
    `descripcion` TEXT                  DEFAULT NULL,
    `updated_at`  DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Direcciones por departamento
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `direcciones` (
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `departamento` VARCHAR(200) NOT NULL,
    `nombre`       VARCHAR(200) NOT NULL,
    `cargo`        VARCHAR(300) NOT NULL,
    `imagen_path`  VARCHAR(500)          DEFAULT NULL,
    `orden`        INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Organigrama
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `organigrama` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `pdf_path`   VARCHAR(500)          DEFAULT NULL,
    `titulo`     VARCHAR(200) NOT NULL DEFAULT 'Organigrama 2025-2027',
    `updated_at` DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Trámites y Servicios (6 páginas)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tramites` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(50)  NOT NULL COMMENT 'PMPNNA, DAAM, DANF, DAD, DPAF, DSJAIG',
    `titulo`      VARCHAR(200) NOT NULL,
    `imagen_path` VARCHAR(500)          DEFAULT NULL,
    `contenido`   LONGTEXT              DEFAULT NULL COMMENT 'HTML enriquecido (TinyMCE)',
    `updated_at`  DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Galería — Álbumes
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `galeria_albumes` (
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `nombre`       VARCHAR(200) NOT NULL,
    `fecha_album`  DATE         NOT NULL,
    `portada_path` VARCHAR(500)          DEFAULT NULL,
    `activo`       TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`   DATETIME              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Galería — Imágenes por álbum
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `galeria_imagenes` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `album_id`    INT          NOT NULL,
    `imagen_path` VARCHAR(500) NOT NULL,
    `orden`       INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_galeria_album`
        FOREIGN KEY (`album_id`) REFERENCES `galeria_albumes` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- SEAC — Bloques por año
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `seac_bloques` (
    `id`    INT  NOT NULL AUTO_INCREMENT,
    `anio`  YEAR NOT NULL,
    `orden` INT  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- SEAC — Conceptos por bloque (filas de la tabla de trimestres)
-- -----------------------------------------------------------------------------
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

-- -----------------------------------------------------------------------------
-- SEAC — PDFs por bloque / trimestre / concepto
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `seac_pdfs` (
    `id`          INT        NOT NULL AUTO_INCREMENT,
    `bloque_id`   INT        NOT NULL,
    `concepto_id` INT        NOT NULL,
    `trimestre`   TINYINT    NOT NULL COMMENT '1-4',
    `pdf_path`    VARCHAR(500)        DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_bloque_concepto_trim` (`bloque_id`, `concepto_id`, `trimestre`),
    CONSTRAINT `fk_seac_bloque`
        FOREIGN KEY (`bloque_id`) REFERENCES `seac_bloques` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_seac_concepto`
        FOREIGN KEY (`concepto_id`) REFERENCES `seac_conceptos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Nuestros Programas
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `programas` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(200) NOT NULL,
    `imagen_path` VARCHAR(500)          DEFAULT NULL,
    `orden`       INT          NOT NULL DEFAULT 0,
    `activo`      TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Secciones de acordeón por programa
-- -----------------------------------------------------------------------------
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

-- -----------------------------------------------------------------------------
-- Transparencia del index
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `transparencia_items` (
    `id`          INT           NOT NULL AUTO_INCREMENT,
    `titulo`      VARCHAR(300)  NOT NULL,
    `url`         VARCHAR(1000) NOT NULL,
    `imagen_path` VARCHAR(500)           DEFAULT NULL,
    `orden`       INT           NOT NULL DEFAULT 0,
    `activo`      TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Footer (registro único, id=1)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `footer_config` (
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `texto_inst`   TEXT                  DEFAULT NULL,
    `horario`      VARCHAR(200)          DEFAULT NULL,
    `direccion`    TEXT                  DEFAULT NULL,
    `telefono`     VARCHAR(50)           DEFAULT NULL,
    `email`        VARCHAR(200)          DEFAULT NULL,
    `url_facebook` VARCHAR(500)          DEFAULT NULL,
    `url_twitter`  VARCHAR(500)          DEFAULT NULL,
    `url_instagram` VARCHAR(500)         DEFAULT NULL,
    `updated_at`   DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Footer Links (enlaces de navegación de la columna derecha del footer)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `footer_links` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `titulo`     VARCHAR(200) NOT NULL,
    `url`        VARCHAR(500) NOT NULL DEFAULT '#',
    `nueva_tab`  TINYINT(1)   NOT NULL DEFAULT 0,
    `orden`      INT          NOT NULL DEFAULT 0,
    `activo`     TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Banner Institucional (Team section en index, registro único)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `institucion_banner` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `imagen_path` VARCHAR(500) NOT NULL DEFAULT 'img/institucion.png',
    `updated_at`  DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Cuenta Pública — Bloques por año
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_bloques` (
    `id`    INT  NOT NULL AUTO_INCREMENT,
    `anio`  YEAR NOT NULL,
    `orden` INT  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_cp_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Cuenta Pública — Títulos (módulos) por bloque
-- Ej: "CUENTA PÚBLICA 2024 MODULO 1 DISCIPLINA FINANCIERA"
-- -----------------------------------------------------------------------------
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

-- -----------------------------------------------------------------------------
-- Cuenta Pública — Conceptos por título (cada uno con su PDF)
-- Ej: "1.- ESFCDLDF2024" + pdf
-- -----------------------------------------------------------------------------
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

-- -----------------------------------------------------------------------------
-- Presupuesto Anual — Bloques por año
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pa_bloques` (
    `id`    INT  NOT NULL AUTO_INCREMENT,
    `anio`  YEAR NOT NULL,
    `orden` INT  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pa_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Presupuesto Anual — Conceptos por bloque
-- -----------------------------------------------------------------------------
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

-- -----------------------------------------------------------------------------
-- Presupuesto Anual — PDFs por concepto y sub-año
-- (sub_anio no puede ser mayor que el año del bloque padre)
-- -----------------------------------------------------------------------------
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

-- -----------------------------------------------------------------------------
-- PAE (Programa Anual de Evaluación) — Títulos dinámicos
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pae_titulos` (
    `id`     INT          NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(500) NOT NULL,
    `orden`  INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- PAE — PDFs por título y año
-- -----------------------------------------------------------------------------
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

-- -----------------------------------------------------------------------------
-- Matrices de Indicadores — PDFs por año (horizontal)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mi_pdfs` (
    `id`       INT          NOT NULL AUTO_INCREMENT,
    `anio`     YEAR         NOT NULL,
    `pdf_path` VARCHAR(500)          DEFAULT NULL,
    `orden`    INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_mi_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- CONAC — Bloques por año
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `conac_bloques` (
    `id`    INT  NOT NULL AUTO_INCREMENT,
    `anio`  YEAR NOT NULL,
    `orden` INT  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_conac_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- CONAC — Conceptos por bloque
-- -----------------------------------------------------------------------------
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

-- -----------------------------------------------------------------------------
-- CONAC — PDFs por bloque / trimestre / concepto
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `conac_pdfs` (
    `id`          INT        NOT NULL AUTO_INCREMENT,
    `bloque_id`   INT        NOT NULL,
    `concepto_id` INT        NOT NULL,
    `trimestre`   TINYINT    NOT NULL COMMENT '1-4',
    `pdf_path`    VARCHAR(500)        DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_conac_bloque_concepto_trim` (`bloque_id`, `concepto_id`, `trimestre`),
    CONSTRAINT `fk_conac_pdf_bloque`
        FOREIGN KEY (`bloque_id`) REFERENCES `conac_bloques` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_conac_pdf_concepto`
        FOREIGN KEY (`concepto_id`) REFERENCES `conac_conceptos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Financiero — Bloques por año
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fin_bloques` (
    `id`    INT  NOT NULL AUTO_INCREMENT,
    `anio`  YEAR NOT NULL,
    `orden` INT  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_fin_anio` (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Financiero — Conceptos por bloque (cada uno con su PDF)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fin_conceptos` (
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `bloque_id` INT          NOT NULL,
    `numero`    INT          NOT NULL,
    `nombre`    VARCHAR(500) NOT NULL,
    `pdf_path`  VARCHAR(500)          DEFAULT NULL,
    `orden`     INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_fin_concepto_bloque`
        FOREIGN KEY (`bloque_id`) REFERENCES `fin_bloques` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Avisos de Privacidad — Configuración (texto del aviso, registro único)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `avisos_privacidad_config` (
    `id`          INT  NOT NULL AUTO_INCREMENT,
    `texto_aviso` TEXT NOT NULL,
    `updated_at`  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Avisos de Privacidad — Botones con PDF
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `avisos_privacidad` (
    `id`       INT          NOT NULL AUTO_INCREMENT,
    `titulo`   VARCHAR(500) NOT NULL,
    `pdf_path` VARCHAR(500)          DEFAULT NULL,
    `orden`    INT          NOT NULL DEFAULT 0,
    `activo`   TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
