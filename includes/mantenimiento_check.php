<?php
/**
 * includes/mantenimiento_check.php — Verificación centralizada de mantenimiento
 *
 * Uso: incluir al inicio de cada página pública DESPUÉS de definir $base_path.
 *   $pagina_key = 'presidencia';
 *   require_once __DIR__ . '/../includes/mantenimiento_check.php';
 *
 * Si la página está en mantenimiento, muestra la página de mantenimiento y termina.
 * Respeta el $base_path de la página que lo llama para que navbar/footer funcionen.
 */

if (!isset($pagina_key) || empty($pagina_key)) {
    return;
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
        // Guardar base_path original de la página que llama
        $_mant_saved_base = $base_path ?? '';

        // Leer configuración de mantenimiento
        $_mant_config = [];
        try {
            $_mant_stmt2 = $_mant_pdo->query('SELECT * FROM mantenimiento_config LIMIT 1');
            $_mant_config = $_mant_stmt2->fetch() ?: [];
        } catch (PDOException $e) {}

        $titulo      = $_mant_config['titulo']           ?? 'Sitio en Mantenimiento';
        $descripcion = $_mant_config['descripcion']      ?? 'Estamos realizando mejoras en nuestro sitio web para ofrecerte una mejor experiencia.';
        $correo      = $_mant_config['correo_contacto']  ?? 'presidencia@difsanmateoatenco.gob.mx';
        $t1_titulo   = $_mant_config['tarjeta1_titulo']  ?? 'Tiempo estimado';
        $t1_texto    = $_mant_config['tarjeta1_texto']   ?? 'Breve interrupción';
        $t2_titulo   = $_mant_config['tarjeta2_titulo']  ?? 'Mejoras de seguridad';
        $t2_texto    = $_mant_config['tarjeta2_texto']   ?? 'Actualizaciones del sistema';
        $t3_titulo   = $_mant_config['tarjeta3_titulo']  ?? 'Nuevas funciones';
        $t3_texto    = $_mant_config['tarjeta3_texto']   ?? 'Próximamente disponibles';

        // Usar el base_path de la página original
        $base_path   = $_mant_saved_base;
        $page_title  = 'En Mantenimiento — DIF San Mateo Atenco';

        require_once __DIR__ . '/header.php';
        require_once __DIR__ . '/navbar.php';
?>
<!-- Mantenimiento Start -->
<div class="container-fluid py-5" style="min-height:70vh;display:flex;align-items:center;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center wow fadeIn" data-wow-delay="0.1s">
                <div class="mb-4">
                    <div style="display:inline-flex;align-items:center;justify-content:center;width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,rgb(200,16,44) 0%,rgb(160,10,35) 100%);box-shadow:0 8px 32px rgba(200,16,44,0.35);animation:pulse 2s infinite;">
                        <i class="fas fa-tools" style="font-size:3rem;color:#fff;"></i>
                    </div>
                </div>
                <h1 style="font-family:'Montserrat',sans-serif;font-weight:800;font-size:2.8rem;color:rgb(107,98,90);margin-bottom:0.5rem;"><?= htmlspecialchars($titulo) ?></h1>
                <div style="height:4px;width:80px;background:rgb(200,16,44);border-radius:2px;margin:1rem auto 1.5rem;"></div>
                <p style="font-family:'Montserrat',sans-serif;font-size:1.1rem;color:rgb(107,98,90);line-height:1.8;max-width:600px;margin:0 auto 2rem;"><?= nl2br(htmlspecialchars($descripcion)) ?></p>
                <div class="row g-3 justify-content-center mb-4">
                    <div class="col-sm-4">
                        <div style="background:#fff;border-radius:12px;padding:1.5rem 1rem;box-shadow:0 4px 20px rgba(0,0,0,0.08);border-top:4px solid rgb(200,16,44);">
                            <i class="fas fa-clock" style="font-size:1.8rem;color:rgb(200,16,44);margin-bottom:.5rem;"></i>
                            <p style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);margin:0;font-size:.9rem;"><?= htmlspecialchars($t1_titulo) ?></p>
                            <p style="font-family:'Montserrat',sans-serif;color:#999;margin:0;font-size:.82rem;"><?= htmlspecialchars($t1_texto) ?></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div style="background:#fff;border-radius:12px;padding:1.5rem 1rem;box-shadow:0 4px 20px rgba(0,0,0,0.08);border-top:4px solid rgb(200,16,44);">
                            <i class="fas fa-shield-alt" style="font-size:1.8rem;color:rgb(200,16,44);margin-bottom:.5rem;"></i>
                            <p style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);margin:0;font-size:.9rem;"><?= htmlspecialchars($t2_titulo) ?></p>
                            <p style="font-family:'Montserrat',sans-serif;color:#999;margin:0;font-size:.82rem;"><?= htmlspecialchars($t2_texto) ?></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div style="background:#fff;border-radius:12px;padding:1.5rem 1rem;box-shadow:0 4px 20px rgba(0,0,0,0.08);border-top:4px solid rgb(200,16,44);">
                            <i class="fas fa-rocket" style="font-size:1.8rem;color:rgb(200,16,44);margin-bottom:.5rem;"></i>
                            <p style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);margin:0;font-size:.9rem;"><?= htmlspecialchars($t3_titulo) ?></p>
                            <p style="font-family:'Montserrat',sans-serif;color:#999;margin:0;font-size:.82rem;"><?= htmlspecialchars($t3_texto) ?></p>
                        </div>
                    </div>
                </div>
                <div style="background:#f8f7f6;border-radius:12px;padding:1.2rem 2rem;display:inline-block;margin-bottom:2rem;">
                    <p style="font-family:'Montserrat',sans-serif;font-size:.88rem;color:rgb(107,98,90);margin:0;">
                        <i class="fas fa-envelope me-2" style="color:rgb(200,16,44);"></i>
                        ¿Necesitas ayuda? Contáctanos: <strong><?= htmlspecialchars($correo) ?></strong>
                    </p>
                </div>
                <div>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary me-2" style="font-family:'Montserrat',sans-serif;font-weight:600;border-radius:8px;padding:.6rem 1.5rem;">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <a href="<?= htmlspecialchars($base_path) ?>index" class="btn" style="background:rgb(200,16,44);color:#fff;font-family:'Montserrat',sans-serif;font-weight:600;border-radius:8px;padding:.6rem 1.5rem;">
                        <i class="fas fa-home me-2"></i>Ir al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
@keyframes pulse {
    0%   { box-shadow: 0 8px 32px rgba(200,16,44,0.35); }
    50%  { box-shadow: 0 8px 48px rgba(200,16,44,0.6); }
    100% { box-shadow: 0 8px 32px rgba(200,16,44,0.35); }
}
</style>
<?php
        require_once __DIR__ . '/footer.php';
        exit;
    }
} catch (PDOException $e) {
    // Si la tabla no existe aún, continuar normalmente
}

unset($_mant_pdo, $_mant_stmt, $_mant_row, $_mant_config, $_mant_saved_base);
