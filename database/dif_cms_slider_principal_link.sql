-- Migración: agregar columna link_url al slider principal
ALTER TABLE `slider_principal`
    ADD COLUMN `link_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `activo`;
