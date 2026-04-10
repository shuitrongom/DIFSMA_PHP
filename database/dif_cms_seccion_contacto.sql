-- Agregar campos de contacto por sección a programas_secciones_paginas
ALTER TABLE `programas_secciones_paginas`
    ADD COLUMN `c_titulo1`   VARCHAR(200) DEFAULT NULL AFTER `texto2`,
    ADD COLUMN `c_titulo2`   VARCHAR(200) DEFAULT NULL AFTER `c_titulo1`,
    ADD COLUMN `c_direccion` TEXT         DEFAULT NULL AFTER `c_titulo2`,
    ADD COLUMN `c_telefono`  VARCHAR(100) DEFAULT NULL AFTER `c_direccion`,
    ADD COLUMN `c_horario`   VARCHAR(200) DEFAULT NULL AFTER `c_telefono`,
    ADD COLUMN `c_correo`    VARCHAR(200) DEFAULT NULL AFTER `c_horario`;
