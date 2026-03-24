-- Migración: Agregar columnas mes y anio a slider_comunica
-- Ejecutar en XAMPP local y en hosting

ALTER TABLE slider_comunica
    ADD COLUMN mes  TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER activo,
    ADD COLUMN anio SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER mes;

-- Asignar mes/anio a registros existentes basándose en created_at
UPDATE slider_comunica SET mes = MONTH(created_at), anio = YEAR(created_at) WHERE mes = 0;

-- Índice para consultas por mes/anio
ALTER TABLE slider_comunica ADD INDEX idx_mes_anio (anio, mes);
