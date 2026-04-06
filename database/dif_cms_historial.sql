-- Tabla de historial de actividad del admin
CREATE TABLE IF NOT EXISTS `admin_historial` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT DEFAULT NULL,
  `username` VARCHAR(100) DEFAULT NULL,
  `accion` VARCHAR(100) NOT NULL,
  `seccion` VARCHAR(200) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `dispositivo` VARCHAR(20) DEFAULT NULL COMMENT 'pc, celular, tablet',
  `hostname` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del equipo resuelto por DNS',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
