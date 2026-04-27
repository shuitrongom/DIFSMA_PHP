-- Tabla centralizada de configuración de mantenimiento
CREATE TABLE IF NOT EXISTS `mantenimiento_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) DEFAULT 'Sitio en Mantenimiento',
  `descripcion` TEXT,
  `correo_contacto` VARCHAR(255) DEFAULT 'presidencia@difsanmateoatenco.gob.mx',
  `tarjeta1_titulo` VARCHAR(100) DEFAULT 'Tiempo estimado',
  `tarjeta1_texto` VARCHAR(255) DEFAULT 'Breve interrupción',
  `tarjeta2_titulo` VARCHAR(100) DEFAULT 'Mejoras de seguridad',
  `tarjeta2_texto` VARCHAR(255) DEFAULT 'Actualizaciones del sistema',
  `tarjeta3_titulo` VARCHAR(100) DEFAULT 'Nuevas funciones',
  `tarjeta3_texto` VARCHAR(255) DEFAULT 'Próximamente disponibles',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `mantenimiento_config` (`id`, `titulo`, `descripcion`, `correo_contacto`) VALUES
(1, 'Sitio en Mantenimiento', 'Estamos realizando mejoras en nuestro sitio web para ofrecerte una mejor experiencia. Regresaremos en breve con contenido actualizado.', 'presidencia@difsanmateoatenco.gob.mx')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Tabla de páginas en mantenimiento
CREATE TABLE IF NOT EXISTS `mantenimiento_paginas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pagina_key` VARCHAR(100) NOT NULL UNIQUE,
  `pagina_nombre` VARCHAR(255) NOT NULL,
  `en_mantenimiento` TINYINT(1) NOT NULL DEFAULT 0,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pagina_key` (`pagina_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `mantenimiento_paginas` (`pagina_key`, `pagina_nombre`, `en_mantenimiento`) VALUES
('index',               'Página Principal (Index)',          0),
('presidencia',         'Presidencia',                       0),
('direcciones',         'Direcciones',                       0),
('organigrama',         'Organigrama',                       0),
('autismo',             'Unidad Municipal de Autismo',       0),
('noticias',            'Noticias',                          0),
('galeria',             'Galería',                           0),
('voluntariado',        'Voluntariado',                      0),
('seac',                'SEAC',                              0),
('cuenta_publica',      'Cuenta Pública',                    0),
('presupuesto_anual',   'Presupuesto Anual',                 0),
('pae',                 'PAE',                               0),
('matrices_indicadores','Matrices de Indicadores',           0),
('conac',               'CONAC',                             0),
('financiero',          'Financiero',                        0),
('avisos_privacidad',   'Avisos de Privacidad',              0)
ON DUPLICATE KEY UPDATE `pagina_nombre`=VALUES(`pagina_nombre`);
