-- Footer Links: enlaces de navegación de la columna derecha del footer
CREATE TABLE IF NOT EXISTS `footer_links` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `titulo`     VARCHAR(200) NOT NULL,
    `url`        VARCHAR(500) NOT NULL DEFAULT '#',
    `nueva_tab`  TINYINT(1)   NOT NULL DEFAULT 0,
    `orden`      INT          NOT NULL DEFAULT 0,
    `activo`     TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales (los links actuales del footer)
INSERT INTO `footer_links` (`titulo`, `url`, `nueva_tab`, `orden`, `activo`) VALUES
('Inicio',                        'index.php',                                                          0, 1, 1),
('Nosotros',                      'acerca-del-dif/presidencia.php',                                     0, 2, 1),
('Noticias',                      'comunicacion-social/noticias.php',                                   0, 3, 1),
('Transparencia',                 'transparencia/SEAC.php',                                             0, 4, 1),
('Compras y adquisiciones',       'https://www.ipomex.org.mx/ipo3/lgt/indice/DIFSANMATEO.web',         1, 5, 1),
('Declaraciones',                 'https://difsanmateoatenco.gob.mx/plantilla/29',                      1, 6, 1),
('Sistema de Gestión de Usuarios','https://www.saimex.org.mx/saimex/ciudadano/login.page',              1, 7, 1),
('Servicios en línea',            'tramites/PMPNNA.php',                                                0, 8, 1),
('Ubícanos',                      '__ubicacion__',                                                      1, 9, 1);
