-- Migración: agregar columna texto_inferior a autismo_config
ALTER TABLE `autismo_config`
    ADD COLUMN `texto_inferior` TEXT DEFAULT NULL AFTER `texto_centro`;
