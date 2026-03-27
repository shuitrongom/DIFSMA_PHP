<?php
/**
 * voluntariado.php — Página de Voluntariado del DIF San Mateo Atenco
 */
require_once __DIR__ . '/includes/db.php';

$base_path   = '';
$active_page = 'voluntariado';
$page_title  = 'Voluntariado — DIF San Mateo Atenco';

$config = null;
$imagenes = [];
try {
    $pdo = get_db();
    $stmt = $pdo->query('SELECT * FROM voluntariado_config LIMIT 1');
    $config = $stmt->fetch();

    $stmt = $pdo->query('SELECT imagen_path FROM voluntariado_imagenes WHERE activo = 1 ORDER BY orden ASC');
    $imagenes = $stmt->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('voluntariado.php: ' . $e->getMessage());
}

// Defaults
$lema = $config['lema'] ?? 'UNIDOS SÍ, TENDEMOS LA MANO';
$logo = !empty($config['logo_path']) ? htmlspecialchars($config['logo_path']) : 'img/voluntariado.png';
$mision_titulo = $config['mision_titulo'] ?? '¿Qué es ser voluntario?';
$mision_texto = $config['mision_texto'] ?? '';
$mision_subtitulo = $config['mision_subtitulo'] ?? '¿Cómo puedo aportar?';
$mision_subtexto = $config['mision_subtexto'] ?? '';
$vision_texto = $config['vision_texto'] ?? '';
$valores_texto = $config['valores_texto'] ?? '';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

    <div class="container-fluid py-5" style="background:#f5f5f5;">
        <div class="container py-4">
            <!-- Título -->
            <div class="text-center mb-4">
                <h4 style="font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:2px;color:rgb(107,98,90);">VOLUNTARIADO</h4>
                <div style="height:16px;background:rgb(199,14,44);width:130px;margin:8px auto 0;"></div>
            </div>

            <!-- Logo centrado -->
            <div class="text-center mb-3">
                <img src="<?= $logo ?>" alt="Voluntariado San Mateo Atenco" class="img-fluid vol-logo" style="max-width:500px;width:100%;">
            </div>

            <!-- Lema -->
            <h2 class="text-center mb-5 vol-lema" style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(189,185,182);font-size:clamp(1.4rem,4vw,2.7rem);">
                <?= htmlspecialchars($lema) ?>
            </h2>

            <!-- Misión -->
            <div class="row align-items-start mb-4 pb-4" style="border-bottom:1px solid #ddd;">
                <div class="col-md-4 mb-3 mb-md-0 vol-left" style="text-align:left;padding-left:clamp(12px,8vw,120px);">
                    <div class="d-flex align-items-center">
                        <span class="vol-subtitle" style="font-family:'Montserrat',sans-serif;font-weight:400;font-size:clamp(18px,2.2vw,27px);color:rgb(188,185,182);letter-spacing:1px;white-space:nowrap;">NUESTRA</span>
                        <div class="vol-line" style="height:7px;background:rgb(200,16,44);width:110px;flex-shrink:0;margin-left:70px;"></div>
                    </div>
                    <h1 class="vol-title" style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(188,185,182);font-size:clamp(2.4rem,4.5vw,4rem);margin:0;line-height:0.9;text-align:left;">MISIÓN</h1>
                </div>
                <div class="col-md-8" style="padding-left:clamp(16px,7vw,80px);">
                    <h6 style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);text-align:left;"><?= htmlspecialchars($mision_titulo) ?></h6>
                    <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:rgb(107,98,90);line-height:1.7;text-align:left;"><?= nl2br(htmlspecialchars($mision_texto)) ?></p>
                    <?php if (!empty($mision_subtitulo)): ?>
                    <h6 style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);margin-top:16px;text-align:left;"><?= htmlspecialchars($mision_subtitulo) ?></h6>
                    <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:rgb(107,98,90);line-height:1.7;text-align:left;"><?= nl2br(htmlspecialchars($mision_subtexto)) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Visión -->
            <div class="row align-items-start mb-4 pb-4" style="border-bottom:1px solid #ddd;">
                <div class="col-md-4 mb-3 mb-md-0 vol-left" style="text-align:left;padding-left:clamp(12px,8vw,120px);">
                    <div class="d-flex align-items-center">
                        <span class="vol-subtitle" style="font-family:'Montserrat',sans-serif;font-weight:400;font-size:clamp(18px,2.2vw,27px);color:rgb(188,185,182);letter-spacing:1px;white-space:nowrap;">NUESTRA</span>
                        <div class="vol-line" style="height:7px;background:rgb(200,16,44);width:110px;flex-shrink:0;margin-left:70px;"></div>
                    </div>
                    <h2 class="vol-title" style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(188,185,182);font-size:clamp(2.4rem,4.5vw,4rem);margin:0;line-height:0.9;text-align:left;">VISIÓN</h2>
                </div>
                <div class="col-md-8 d-flex align-items-center" style="padding-left:clamp(16px,7vw,80px);">
                    <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:rgb(107,98,90);line-height:1.7;margin:0;text-align:left;"><?= nl2br(htmlspecialchars($vision_texto)) ?></p>
                </div>
            </div>

            <!-- Valores -->
            <div class="row align-items-start mb-5">
                <div class="col-md-4 mb-3 mb-md-0 vol-left" style="text-align:left;padding-left:clamp(12px,8vw,120px);">
                    <div class="d-flex align-items-center">
                        <span class="vol-subtitle" style="font-family:'Montserrat',sans-serif;font-weight:400;font-size:clamp(18px,2.2vw,27px);color:rgb(188,185,182);letter-spacing:1px;white-space:nowrap;">NUESTROS</span>
                        <div class="vol-line" style="height:7px;background:rgb(200,16,44);width:110px;flex-shrink:0;margin-left:50px;"></div>
                    </div>
                    <h2 class="vol-title" style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(188,185,182);font-size:clamp(2.4rem,4.5vw,4rem);margin:0;line-height:0.9;text-align:left;">VALORES</h2>
                </div>
                <div class="col-md-8 d-flex align-items-center" style="padding-left:clamp(16px,7vw,80px);">
                    <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:rgb(107,98,90);line-height:1.7;margin:0;text-align:left;"><?= nl2br(htmlspecialchars($valores_texto)) ?></p>
                </div>
            </div>

            <!-- Galería de fotos -->
            <?php if (!empty($imagenes)): ?>
            <div class="d-flex" style="padding-left:clamp(12px,8vw,120px);gap:0;height:140px;">
                <?php foreach ($imagenes as $idx => $img):
                    $isWide = ($idx === 2);
                    $flex = $isWide ? 'flex:0 0 28%;' : 'flex:0 0 18%;';
                ?>
                <div style="<?= $flex ?>overflow:hidden;">
                    <img src="<?= htmlspecialchars($img['imagen_path']) ?>" alt="Voluntariado" style="width:100%;height:140px;object-fit:cover;display:block;">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="pleca"><img src="<?= $base_path ?>img/pleca.png" alt="pleca"></div>

    <style>
    /* Voluntariado responsive */
    @media (max-width: 767px) {
        .vol-left { padding-left: 12px !important; }
        .vol-left .vol-subtitle { font-size: 16px !important; }
        .vol-left .vol-title { font-size: 2rem !important; }
        .vol-left .vol-line { width: 50px !important; margin-left: 10px !important; }
        .vol-logo { max-width: 280px !important; }
        .vol-lema { font-size: 1.1rem !important; }
        .vol-gallery-img { aspect-ratio: 3/2 !important; }
    }
    @media (min-width: 768px) and (max-width: 991px) {
        .vol-left { padding-left: 20px !important; }
        .vol-left .vol-line { width: 80px !important; margin-left: 20px !important; }
        .vol-logo { max-width: 380px !important; }
    }
    </style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
