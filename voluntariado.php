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
            <div class="text-center mb-4 wow fadeIn" data-wow-delay="0.1s">
                <h4 style="font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:2px;color:rgb(107,98,90);">VOLUNTARIADO</h4>
                <div style="height:16px;background:rgb(199,14,44);width:130px;margin:8px auto 0;"></div>
            </div>

            <!-- Logo centrado -->
            <div class="text-center mb-3 wow fadeIn" data-wow-delay="0.2s">
                <img src="<?= $logo ?>" alt="Voluntariado San Mateo Atenco" class="img-fluid vol-logo" style="max-width:500px;width:100%;">
            </div>

            <!-- Lema -->
            <h2 class="text-center mb-5 vol-lema wow fadeIn" data-wow-delay="0.3s" style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(189,185,182);font-size:clamp(1.4rem,4vw,2.7rem);">
                <?= htmlspecialchars($lema) ?>
            </h2>

            <!-- Misión -->
            <div class="row align-items-start mb-4 pb-4 wow fadeIn" data-wow-delay="0.1s" style="border-bottom:1px solid #ddd;">
                <div class="col-md-4 mb-3 mb-md-0 vol-left" style="text-align:left;padding-left:clamp(12px,8vw,120px);">
                    <div class="d-flex align-items-center">
                        <span class="vol-subtitle" style="font-family:'Montserrat',sans-serif;font-weight:400;font-size:clamp(18px,2.2vw,27px);color:rgb(188,185,182);letter-spacing:1px;white-space:nowrap;">NUESTRA</span>
                        <div class="vol-line" style="height:7px;background:rgb(200,16,44);width:110px;flex-shrink:0;margin-left:70px;"></div>
                    </div>
                    <h1 class="vol-title" style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(188,185,182);font-size:clamp(2.4rem,4.5vw,4rem);margin:0;line-height:0.9;text-align:left;">MISIÓN</h1>
                </div>
                <div class="col-md-8" style="padding-left:clamp(16px,7vw,80px);padding-right:clamp(16px,7vw,80px);">
                    <h6 style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);text-align:left;"><?= htmlspecialchars($mision_titulo) ?></h6>
                    <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:rgb(107,98,90);line-height:1.7;text-align:justify;"><?= nl2br(htmlspecialchars($mision_texto)) ?></p>
                    <?php if (!empty($mision_subtitulo)): ?>
                    <h6 style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);margin-top:16px;text-align:left;"><?= htmlspecialchars($mision_subtitulo) ?></h6>
                    <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:rgb(107,98,90);line-height:1.7;text-align:justify;"><?= nl2br(htmlspecialchars($mision_subtexto)) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Visión -->
            <div class="row align-items-start mb-4 pb-4 wow fadeIn" data-wow-delay="0.1s" style="border-bottom:1px solid #ddd;">
                <div class="col-md-4 mb-3 mb-md-0 vol-left" style="text-align:left;padding-left:clamp(12px,8vw,120px);">
                    <div class="d-flex align-items-center">
                        <span class="vol-subtitle" style="font-family:'Montserrat',sans-serif;font-weight:400;font-size:clamp(18px,2.2vw,27px);color:rgb(188,185,182);letter-spacing:1px;white-space:nowrap;">NUESTRA</span>
                        <div class="vol-line" style="height:7px;background:rgb(200,16,44);width:110px;flex-shrink:0;margin-left:70px;"></div>
                    </div>
                    <h2 class="vol-title" style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(188,185,182);font-size:clamp(2.4rem,4.5vw,4rem);margin:0;line-height:0.9;text-align:left;">VISIÓN</h2>
                </div>
                <div class="col-md-8 d-flex align-items-center" style="padding-left:clamp(16px,7vw,80px);padding-right:clamp(16px,7vw,80px);">
                    <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:rgb(107,98,90);line-height:1.7;margin:0;text-align:justify;"><?= nl2br(htmlspecialchars($vision_texto)) ?></p>
                </div>
            </div>

            <!-- Valores -->
            <div class="row align-items-start mb-5 wow fadeIn" data-wow-delay="0.1s">
                <div class="col-md-4 mb-3 mb-md-0 vol-left" style="text-align:left;padding-left:clamp(12px,8vw,120px);">
                    <div class="d-flex align-items-center">
                        <span class="vol-subtitle" style="font-family:'Montserrat',sans-serif;font-weight:400;font-size:clamp(18px,2.2vw,27px);color:rgb(188,185,182);letter-spacing:1px;white-space:nowrap;">NUESTROS</span>
                        <div class="vol-line" style="height:7px;background:rgb(200,16,44);width:110px;flex-shrink:0;margin-left:50px;"></div>
                    </div>
                    <h2 class="vol-title" style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(188,185,182);font-size:clamp(2.4rem,4.5vw,4rem);margin:0;line-height:0.9;text-align:left;">VALORES</h2>
                </div>
                <div class="col-md-8 d-flex align-items-center" style="padding-left:clamp(16px,7vw,80px);padding-right:clamp(16px,7vw,80px);">
                    <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:rgb(107,98,90);line-height:1.7;margin:0;text-align:justify;"><?= nl2br(htmlspecialchars($valores_texto)) ?></p>
                </div>
            </div>

            <!-- Galería de fotos -->
            <?php if (!empty($imagenes)): ?>
            <div class="vol-gallery wow fadeIn" data-wow-delay="0.1s">
                <?php foreach ($imagenes as $idx => $img):
                    $isWide = ($idx === 2);
                    $extraClass = $isWide ? ' vol-gallery-wide' : '';
                ?>
                <div class="vol-gallery-item<?= $extraClass ?>">
                    <img src="<?= htmlspecialchars($img['imagen_path']) ?>"
                         alt="Voluntariado"
                         class="vol-gallery-img"
                         data-src="<?= htmlspecialchars($img['imagen_path']) ?>"
                         loading="lazy">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Lightbox -->
            <div id="vol-lightbox" role="dialog" aria-modal="true" aria-label="Imagen ampliada">
                <button id="vol-lb-close" aria-label="Cerrar">&times;</button>
                <button id="vol-lb-prev" aria-label="Anterior">&#8249;</button>
                <button id="vol-lb-next" aria-label="Siguiente">&#8250;</button>
                <div id="vol-lb-inner">
                    <img id="vol-lb-img" src="" alt="Imagen ampliada">
                </div>
            </div>
        </div>
    </div>

    <div class="pleca"><img src="<?= $base_path ?>img/pleca.png" alt="pleca"></div>

    <style>
    /* ── Galería ─────────────────────────────────────────────────── */
    .vol-gallery {
        display: flex;
        gap: 4px;
        padding-left: clamp(12px, 8vw, 120px);
        padding-right: clamp(12px, 8vw, 120px);
        height: 160px;
    }
    .vol-gallery-item {
        flex: 0 0 18%;
        overflow: hidden;
        cursor: pointer;
        border-radius: 4px;
        position: relative;
    }
    .vol-gallery-item.vol-gallery-wide { flex: 0 0 28%; }
    .vol-gallery-img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease, filter 0.3s ease;
    }
    .vol-gallery-item:hover .vol-gallery-img {
        transform: scale(1.07);
        filter: brightness(1.12);
    }
    .vol-gallery-item::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(200,16,44,0);
        transition: background 0.3s ease;
        pointer-events: none;
        border-radius: 4px;
    }
    .vol-gallery-item:hover::after {
        background: rgba(200,16,44,0.15);
    }

    /* ── Lightbox ────────────────────────────────────────────────── */
    #vol-lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.88);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    #vol-lightbox.active { display: flex; }
    #vol-lb-inner {
        max-width: 90vw;
        max-height: 88vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #vol-lb-img {
        max-width: 90vw;
        max-height: 85vh;
        object-fit: contain;
        border-radius: 6px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.6);
        animation: vol-lb-in 0.2s ease;
    }
    @keyframes vol-lb-in {
        from { opacity: 0; transform: scale(0.93); }
        to   { opacity: 1; transform: scale(1); }
    }
    #vol-lb-close {
        position: fixed;
        top: 16px; right: 20px;
        background: none; border: none;
        color: #fff; font-size: 2.4rem;
        cursor: pointer; line-height: 1;
        opacity: 0.8; transition: opacity 0.2s;
    }
    #vol-lb-close:hover { opacity: 1; }
    #vol-lb-prev, #vol-lb-next {
        position: fixed;
        top: 50%; transform: translateY(-50%);
        background: rgba(255,255,255,0.12);
        border: none; color: #fff;
        font-size: 2.5rem; cursor: pointer;
        padding: 8px 16px; border-radius: 4px;
        transition: background 0.2s;
        line-height: 1;
    }
    #vol-lb-prev { left: 12px; }
    #vol-lb-next { right: 12px; }
    #vol-lb-prev:hover, #vol-lb-next:hover { background: rgba(200,16,44,0.7); }

    /* ── Responsive ──────────────────────────────────────────────── */
    @media (max-width: 767px) {
        .vol-left { padding-left: 12px !important; }
        .vol-left .vol-subtitle { font-size: 16px !important; }
        .vol-left .vol-title { font-size: 2rem !important; }
        .vol-left .vol-line { width: 50px !important; margin-left: 10px !important; }
        .vol-logo { max-width: 280px !important; }
        .vol-lema { font-size: 1.1rem !important; }
        .vol-gallery {
            flex-wrap: wrap;
            height: auto !important;
            padding-left: 0 !important;
            gap: 6px;
        }
        .vol-gallery-item,
        .vol-gallery-item.vol-gallery-wide {
            flex: 0 0 calc(50% - 3px) !important;
        }
        .vol-gallery-img { height: 120px !important; }
        #vol-lb-prev { left: 4px; }
        #vol-lb-next { right: 4px; }
    }
    @media (min-width: 768px) and (max-width: 991px) {
        .vol-left { padding-left: 20px !important; }
        .vol-left .vol-line { width: 80px !important; margin-left: 20px !important; }
        .vol-logo { max-width: 380px !important; }
        .vol-gallery { height: 140px; }
        .vol-gallery-img { height: 140px !important; }
    }
    </style>

    <script>
    (function () {
        const imgs   = Array.from(document.querySelectorAll('.vol-gallery-img'));
        const lb     = document.getElementById('vol-lightbox');
        const lbImg  = document.getElementById('vol-lb-img');
        const close  = document.getElementById('vol-lb-close');
        const prev   = document.getElementById('vol-lb-prev');
        const next   = document.getElementById('vol-lb-next');
        let current  = 0;

        function open(idx) {
            current = idx;
            lbImg.src = imgs[idx].dataset.src;
            lb.classList.add('active');
            document.body.style.overflow = 'hidden';
            prev.style.display = imgs.length > 1 ? '' : 'none';
            next.style.display = imgs.length > 1 ? '' : 'none';
        }
        function closeLb() {
            lb.classList.remove('active');
            document.body.style.overflow = '';
            lbImg.src = '';
        }
        function go(dir) {
            current = (current + dir + imgs.length) % imgs.length;
            lbImg.style.animation = 'none';
            lbImg.offsetHeight; // reflow
            lbImg.style.animation = '';
            lbImg.src = imgs[current].dataset.src;
        }

        imgs.forEach((img, i) => img.parentElement.addEventListener('click', () => open(i)));
        close.addEventListener('click', closeLb);
        prev.addEventListener('click', () => go(-1));
        next.addEventListener('click', () => go(1));
        lb.addEventListener('click', e => { if (e.target === lb) closeLb(); });
        document.addEventListener('keydown', e => {
            if (!lb.classList.contains('active')) return;
            if (e.key === 'Escape')      closeLb();
            if (e.key === 'ArrowLeft')   go(-1);
            if (e.key === 'ArrowRight')  go(1);
        });
    })();
    </script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
