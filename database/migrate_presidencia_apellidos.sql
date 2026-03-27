-- Migración: Agregar campo apellidos a la tabla presidencia
ALTER TABLE `presidencia` ADD COLUMN `apellidos` VARCHAR(200) DEFAULT '' AFTER `nombre`;

-- Separar el nombre actual en nombre y apellidos (tomar primera palabra como nombre, resto como apellidos)
UPDATE `presidencia` SET
    `apellidos` = TRIM(SUBSTRING(`nombre`, LOCATE(' ', `nombre`) + 1)),
    `nombre` = TRIM(SUBSTRING_INDEX(`nombre`, ' ', 1))
WHERE `nombre` LIKE '% %';
