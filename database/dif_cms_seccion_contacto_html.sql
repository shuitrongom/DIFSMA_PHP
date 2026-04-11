-- Reemplazar campos de contacto separados por un solo campo HTML editable
ALTER TABLE `programas_secciones_paginas`
    ADD COLUMN `c_contacto` LONGTEXT DEFAULT NULL AFTER `c_correo`;
