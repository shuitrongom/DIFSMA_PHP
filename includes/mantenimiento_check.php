<?php
/**
 * includes/mantenimiento_check.php — Verificación centralizada de mantenimiento
 *
 * Uso: incluir al inicio de cada página pública ANTES del header.
 *   $pagina_key = 'presidencia';
 *   require_once __DIR__ . '/includes/mantenimiento_check.php';
 *
 * Si la página está en mantenimiento, muestra la página de mantenimiento y termina.
 */

if (!isset($pagina_key) || empty($pagina_key)) {
    return; // Sin key, no verificar
}

try {
    $_mant_pdo = function_exists('get_db') ? get_db() : null;
    if (!$_mant_pdo) {
        require_once __DIR__ . '/db.php';
        $_mant_pdo = get_db();
    }

    $_mant_stmt = $_mant_pdo->prepare(
        'SELECT en_mantenimiento FROM mantenimiento_paginas WHERE pagina_key = ? LIMIT 1'
    );
    $_mant_stmt->execute([$pagina_key]);
    $_mant_row = $_mant_stmt->fetch();

    if ($_mant_row && $_mant_row['en_mantenimiento'] == 1) {
        // Determinar ruta base al archivo mantenimiento.php
        $_mant_base = __DIR__ . '/../mantenimiento.php';
        if (file_exists($_mant_base)) {
            require_once $_mant_base;
            exit;
        }
    }
} catch (PDOException $e) {
    // Si la tabla no existe aún, continuar normalmente
}

// Limpiar variables temporales
unset($_mant_pdo, $_mant_stmt, $_mant_row, $_mant_base);
