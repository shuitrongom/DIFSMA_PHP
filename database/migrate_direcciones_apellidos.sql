-- Migración: Agregar columna apellidos a direcciones
-- Ejecutar en XAMPP local y en hosting

ALTER TABLE direcciones ADD COLUMN apellidos VARCHAR(200) DEFAULT '' AFTER nombre;

-- Intentar separar nombres existentes (primer palabra = nombre, resto = apellidos)
UPDATE direcciones SET apellidos = SUBSTRING_INDEX(nombre, ' ', -1), nombre = SUBSTRING_INDEX(nombre, ' ', 1) WHERE nombre LIKE '% %';
