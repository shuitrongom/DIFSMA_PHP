<?php
/**
 * slider_config_helper.php — Helper para leer/guardar configuración de autoplay
 */

function get_slider_delay(string $seccion, int $default = 3000): int {
    try {
        $pdo = get_db();
        $stmt = $pdo->prepare('SELECT autoplay_delay FROM slider_config WHERE seccion = ?');
        $stmt->execute([$seccion]);
        $row = $stmt->fetch();
        return $row ? (int)$row['autoplay_delay'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

function save_slider_delay(string $seccion, int $delay): void {
    try {
        $pdo = get_db();
        $pdo->prepare('INSERT INTO slider_config (seccion, autoplay_delay) VALUES (?,?)
                       ON DUPLICATE KEY UPDATE autoplay_delay = VALUES(autoplay_delay)')
            ->execute([$seccion, $delay]);
    } catch (PDOException $e) {
        // Tabla no existe aún — ignorar silenciosamente
    }
}
