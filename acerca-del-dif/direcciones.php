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

$direcciones = [];
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare('SELECT departamento, nombre, apellidos, cargo, imagen_path FROM direcciones ORDER BY orden ASC');
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
    <style>
        .dir-card{display:flex;border-radius:0 16px 16px 16px;overflow:hidden;box-shadow:0 3px 15px rgba(0,0,0,.12);height:200px;}
        .dir-card-photo{min-width:142px;background:#e8e8e8;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
        .dir-card-photo img{max-width:100%;max-height:100%;object-fit:contain;border-radius:4px;}
        .dir-card-panel{flex:1;background:rgb(200,16,44);padding:1rem 1.2rem;border-radius:0 16px 16px 0;display:flex;flex-direction:column;justify-content:center;}
        .dir-card-panel .cargo{font-family:'Montserrat',sans-serif;font-weight:700;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:.5rem;color:#fff;}
        .dir-card-panel .nombre{font-family:'Montserrat',sans-serif;font-weight:300;font-size:22px;margin-bottom:0;color:#fff;line-height:1.3;}
        /* Tablet */
        @media(max-width:768px){
            .dir-card{flex-direction:column;height:auto;border-radius:0 16px 16px 16px;}
            .dir-card-photo{width:100%;min-width:unset;height:180px;}
            .dir-card-panel{border-radius:0 0 16px 16px;padding:.8rem 1rem;}
            .dir-card-panel .cargo{font-size:11px;}
            .dir-card-panel .nombre{font-size:15px;}
        }
        /* Móvil */
        @media(max-width:576px){
            .dir-card-photo{height:150px;}
            .dir-card-panel{padding:.7rem .8rem;}
            .dir-card-panel .cargo{font-size:10px;}
            .dir-card-panel .nombre{font-size:14px;}
        }
    </style>

<?php if (empty($direcciones)): ?>
            <div class="text-center py-4">
                <p class="text-muted">No hay direcciones disponibles.</p>
            </div>
<?php else: ?>
            <div class="row g-4 justify-content-center">
<?php foreach ($direcciones as $i => $dir):
    $has_photo = !empty($dir['imagen_path']);
    if ($has_photo) {
        $img = htmlspecialchars($base_path . $dir['imagen_path'], ENT_QUOTES, 'UTF-8');
    }
    $nombre_safe    = htmlspecialchars($dir['nombre'], ENT_QUOTES, 'UTF-8');
    $apellidos_safe = htmlspecialchars($dir['apellidos'] ?? '', ENT_QUOTES, 'UTF-8');
    $cargo_safe     = htmlspecialchars($dir['cargo'], ENT_QUOTES, 'UTF-8');
?>
                <div class="col-md-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="dir-card">
                        <!-- Foto -->
                        <div class="dir-card-photo">
                            <?php if ($has_photo): ?>
                                <img src="<?= $img ?>" alt="<?= $nombre_safe ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background:#e0e0e0;">
                                    <i class="bi bi-person-fill" style="font-size:4rem;color:#555;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Panel rojo -->
                        <div class="dir-card-panel">
                            <p class="cargo"><?= $cargo_safe ?></p>
                            <p class="nombre"><?= $nombre_safe ?></p>
                            <?php if ($apellidos_safe): ?>
                                <p class="nombre" style="margin-top:2px;"><?= $apellidos_safe ?></p>
                            <?php endif; ?>
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
