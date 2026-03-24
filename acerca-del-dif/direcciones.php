<?php
/**
 * acerca-del-dif/direcciones.php — Página de Direcciones del DIF San Mateo Atenco
 *
 * Consulta la tabla `direcciones` ordenada por `orden` ASC para obtener
 * imagen, nombre y cargo por departamento.
 */

require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'acerca';
$page_title  = 'Direcciones — DIF San Mateo Atenco';

$default_images = [
    'Procuraduría Municipal de Protección de Niñas, Niños y Adolescentes' => 'img/team-3.jpg',
    'Dirección de Atención a Adultos Mayores'                             => 'img/team-3.jpg',
    'Dirección de Alimentación y Nutrición Familiar'                      => 'img/team-4.jpg',
    'Dirección de Atención a la Discapacidad'                             => 'img/team-1.jpg',
    'Dirección de Prevención y Bienestar Familiar'                        => 'img/team-1.jpg',
    'Dirección de Servicios Jurídicos – Asistenciales e Igualdad de Género' => 'img/team-3.jpg',
];
$fallback_image = 'img/team-3.jpg';

$direcciones = [];
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare('SELECT departamento, nombre, cargo, imagen_path FROM direcciones ORDER BY orden ASC');
    $stmt->execute();
    $direcciones = $stmt->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('direcciones.php PDOException: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <!-- Direcciones Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    DIRECCIONES</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>
<?php if (empty($direcciones)): ?>
            <div class="text-center py-4">
                <p class="text-muted">No hay direcciones disponibles.</p>
            </div>
<?php else: ?>
            <div class="row g-4 justify-content-center">
<?php foreach ($direcciones as $i => $dir):
    if (!empty($dir['imagen_path'])) {
        $img = htmlspecialchars($base_path . $dir['imagen_path'], ENT_QUOTES, 'UTF-8');
    } else {
        $dept = $dir['departamento'];
        $img = $base_path . ($default_images[$dept] ?? $fallback_image);
    }
    $nombre_safe = htmlspecialchars($dir['nombre'], ENT_QUOTES, 'UTF-8');
    $cargo_safe  = htmlspecialchars($dir['cargo'], ENT_QUOTES, 'UTF-8');
?>
                <div class="col-md-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="row g-0" style="border-radius:12px;overflow:hidden;box-shadow:0 3px 15px rgba(0,0,0,0.12);height:100%;">
                        <!-- Foto -->
                        <div class="col-5" style="background:#111;min-height:160px;">
                            <img src="<?= $img ?>" class="w-100 h-100" style="object-fit:cover;" alt="<?= $nombre_safe ?>">
                        </div>
                        <!-- Panel rojo -->
                        <div class="col-7 d-flex flex-column justify-content-center text-center text-white" style="background:rgb(200,16,44);padding:1.2rem 1rem;">
                            <p style="font-family:'Montserrat',sans-serif;font-weight:700;font-size:11px;letter-spacing:1px;text-transform:uppercase;margin-bottom:0.6rem;color:#fff;"><?= $cargo_safe ?></p>
                            <p style="font-family:'Montserrat',sans-serif;font-weight:400;font-size:16px;margin-bottom:0;color:#fff;"><?= $nombre_safe ?></p>
                        </div>
                    </div>
                </div>
<?php endforeach; ?>
            </div>
<?php endif; ?>
        </div>
    </div>
    <!-- Direcciones End -->

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
