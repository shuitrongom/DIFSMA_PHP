-- =============================================================================
-- database/seed.sql — Datos semilla iniciales DIF CMS
-- Ejecutar DESPUÉS de schema.sql
-- =============================================================================

SET NAMES utf8mb4;

-- -----------------------------------------------------------------------------
-- Administrador inicial
-- Contraseña de ejemplo: Admin1234!
-- Hash generado con: password_hash('Admin1234!', PASSWORD_BCRYPT)
-- IMPORTANTE: cambiar la contraseña inmediatamente tras el primer login.
-- -----------------------------------------------------------------------------
INSERT INTO `admin` (`username`, `password`) VALUES
('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- -----------------------------------------------------------------------------
-- Footer — configuración predeterminada (id=1)
-- -----------------------------------------------------------------------------
INSERT INTO `footer_config`
    (`id`, `texto_inst`, `horario`, `direccion`, `telefono`, `email`,
     `url_facebook`, `url_twitter`, `url_instagram`)
VALUES (
    1,
    'El DIF San Mateo Atenco es un organismo público descentralizado comprometido con el bienestar social de las familias del municipio.',
    'Lunes a Viernes: 9:00 am – 3:00 pm',
    'Av. Independencia S/N, San Mateo Atenco, Estado de México, C.P. 52100',
    '(722) 123-4567',
    'contacto@difsanmateoatenco.gob.mx',
    'https://www.facebook.com/DIFSanMateoAtenco',
    NULL,
    NULL
)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- -----------------------------------------------------------------------------
-- Trámites y Servicios — 6 slugs predefinidos
-- -----------------------------------------------------------------------------
INSERT INTO `tramites` (`slug`, `titulo`, `imagen_path`, `contenido`) VALUES
('PMPNNA',
 'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes',
 NULL,
 '<p>Información sobre los servicios de la Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes.</p>'),
('DAAM',
 'Dirección de Atención a Adultos Mayores',
 NULL,
 '<p>Información sobre los programas y servicios de atención a adultos mayores.</p>'),
('DANF',
 'Dirección de Alimentación y Nutrición Familiar',
 NULL,
 '<p>Información sobre los programas de alimentación y nutrición familiar.</p>'),
('DAD',
 'Dirección de Atención a la Discapacidad',
 NULL,
 '<p>Información sobre los servicios de atención a personas con discapacidad.</p>'),
('DPAF',
 'Dirección de Prevención y Bienestar Familiar',
 NULL,
 '<p>Información sobre los programas de prevención y bienestar familiar.</p>'),
('DSJAIG',
 'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género',
 NULL,
 '<p>Información sobre los servicios jurídicos asistenciales e igualdad de género.</p>')
ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);

-- -----------------------------------------------------------------------------
-- SEAC — Sin conceptos predefinidos (se crean desde el panel por bloque)
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- Organigrama — registro inicial (sin PDF, usa imagen fallback)
-- -----------------------------------------------------------------------------
INSERT INTO `organigrama` (`titulo`, `pdf_path`) VALUES
('Organigrama 2025-2027', NULL)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- -----------------------------------------------------------------------------
-- Presidencia — registro inicial
-- -----------------------------------------------------------------------------
INSERT INTO `presidencia` (`nombre`, `cargo`, `imagen_path`) VALUES
('Presidenta del DIF San Mateo Atenco', 'Presidenta Municipal del DIF', NULL)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- -----------------------------------------------------------------------------
-- Direcciones — departamentos predefinidos
-- -----------------------------------------------------------------------------
-- -----------------------------------------------------------------------------
-- Banner Institucional — imagen predeterminada
-- -----------------------------------------------------------------------------
INSERT INTO `institucion_banner` (`id`, `imagen_path`) VALUES
(1, 'img/institucion.png')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- -----------------------------------------------------------------------------
-- Direcciones — departamentos predefinidos
-- -----------------------------------------------------------------------------
INSERT INTO `direcciones` (`departamento`, `nombre`, `cargo`, `imagen_path`, `orden`) VALUES
('Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes',
 'Director(a) PMPNNA', 'Director(a) de la Procuraduría Municipal', NULL, 1),
('Dirección de Atención a Adultos Mayores',
 'Director(a) DAAM', 'Director(a) de Atención a Adultos Mayores', NULL, 2),
('Dirección de Alimentación y Nutrición Familiar',
 'Director(a) DANF', 'Director(a) de Alimentación y Nutrición Familiar', NULL, 3),
('Dirección de Atención a la Discapacidad',
 'Director(a) DAD', 'Director(a) de Atención a la Discapacidad', NULL, 4),
('Dirección de Prevención y Bienestar Familiar',
 'Director(a) DPAF', 'Director(a) de Prevención y Bienestar Familiar', NULL, 5),
('Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género',
 'Director(a) DSJAIG', 'Director(a) de Servicios Jurídicos Asistenciales', NULL, 6);
