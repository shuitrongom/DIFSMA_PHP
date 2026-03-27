-- ============================================================================
-- Sistema de Usuarios con Roles y Permisos
-- ============================================================================

-- Agregar columna de rol a la tabla admin existente
ALTER TABLE `admin` ADD COLUMN `rol` ENUM('admin','usuario') NOT NULL DEFAULT 'admin' AFTER `password`;
ALTER TABLE `admin` ADD COLUMN `nombre` VARCHAR(200) DEFAULT NULL AFTER `username`;
ALTER TABLE `admin` ADD COLUMN `activo` TINYINT(1) NOT NULL DEFAULT 1 AFTER `rol`;
ALTER TABLE `admin` ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `activo`;

-- Actualizar el admin existente
UPDATE `admin` SET rol = 'admin', nombre = 'Administrador', activo = 1 WHERE id = 1;

-- Tabla de permisos por usuario (que secciones puede ver)
CREATE TABLE IF NOT EXISTS `admin_permisos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `seccion_file` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_seccion` (`user_id`, `seccion_file`),
  FOREIGN KEY (`user_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
