-- Tablas para la página Unidad Municipal de Autismo
CREATE TABLE IF NOT EXISTS `autismo_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `logo_path` VARCHAR(500) DEFAULT NULL,
  `texto_derecha` TEXT,
  `texto_centro` TEXT,
  `imagen_centro_path` VARCHAR(500) DEFAULT NULL,
  `imagen_inferior_path` VARCHAR(500) DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `autismo_config` (`id`, `texto_derecha`, `texto_centro`) VALUES
(1,
'El Centro de rehabilitación e Integración Social (CRIS) se a consolidado por la calidad en la atención que brinda y el servicio humanista de su personal: médico especialista, médico general, enfermeras y terapeutas.',
'El Centro de rehabilitación e Integración Social (CRIS) se a consolidado por la calidad en la atención que brinda y el servicio humanista de su personal: médico especialista, médico general, enfermeras y terapeutas.');
