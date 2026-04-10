<?php
/**
 * programas/seccion.php — Página dinámica de sección de programa
 * URL: programas/seccion?slug=SLUG
 */
require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'servicios';

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) { header('Location: ../index'); exit; }

$seccion = null;
$pagina  = null;
$programa = null;

try {
    $pdo  = get_db();
    $stmt = $pdo->prepare(
        'SELECT s.*, p.nombre AS programa_nombre
         FROM programas_secciones s
         JOIN programas p ON p.id = s.programa_id
         WHERE s.slug = ? AND p.activo = 1'
    );
    $stmt->execute([$slug]);
    $seccion = $stmt->fetch();

    if (!$seccion) { header('Location: ../index'); exit; }

    $stmt = $pdo->prepare('SELECT * FROM programas_secciones_paginas WHERE seccion_id = ?');
    $stmt->execute([$seccion['id']]);
    $pagina = $stmt->fetch();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('seccion.php: ' . $e->getMessage());
}

$titulo   = $seccion['titulo'] ?? '';
$img1     = !empty($pagina['imagen1_path']) ? $base_path . htmlspecialchars($pagina['imagen1_path']) : null;
$texto1   = preg_replace('/(<li[^>]*>)(\s|&nbsp;)+/i', '$1', $pagina['texto1'] ?? '');
$img2     = !empty($pagina['imagen2_path']) ? $base_path . htmlspecialchars($pagina['imagen2_path']) : null;
$texto2   = preg_replace('/(<li[^>]*>)(\s|&nbsp;)+/i', '$1', $pagina['texto2'] ?? '');
$page_title = htmlspecialchars($titulo) . ' — DIF San Mateo Atenco';

// Contacto — por sección, con fallback al global
$contacto_global = null;
try {
    $stmt = $pdo->query('SELECT * FROM contacto_config LIMIT 1');
    $contacto_global = $stmt->fetch();
} catch (PDOException $e) {}

$c_titulo1   = !empty($pagina['c_titulo1'])   ? $pagina['c_titulo1']   : ($contacto_global['titulo1']   ?? 'SERVICIOS MÉDICOS');
$c_titulo2   = !empty($pagina['c_titulo2'])   ? $pagina['c_titulo2']   : ($contacto_global['titulo2']   ?? 'CLASES Y TALLERES');
$c_direccion = !empty($pagina['c_direccion']) ? $pagina['c_direccion'] : ($contacto_global['direccion'] ?? 'Mariano Matamoros 310, Barrio de la Concepción CP 52105,\nSan Mateo Atenco, Méx.');
$c_telefono  = !empty($pagina['c_telefono'])  ? $pagina['c_telefono']  : ($contacto_global['telefono']  ?? '722 970 77 86');
$c_horario   = !empty($pagina['c_horario'])   ? $pagina['c_horario']   : ($contacto_global['horario']   ?? 'Horario de Lunes a Viernes\n8:00 am a 3:30 pm');
$c_correo    = !empty($pagina['c_correo'])    ? $pagina['c_correo']    : ($contacto_global['correo']    ?? 'adultomayor@difsanmateoatenco.gob.mx');

// Galería de servicios
$galeria_imgs = [];
try {
    $stmt = $pdo->query('SELECT imagen_path FROM servicios_galeria WHERE activo = 1 ORDER BY orden ASC');
    $galeria_imgs = $stmt->fetchAll();
} catch (PDOException $e) {}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid px-0" style="background:#f5f5f5;overflow-x:hidden;">

    <script>
    // Escalar el contenido proporcionalmente al ancho de ventana
    // sin activar media queries — todo se ve igual pero más pequeño/grande
    (function() {
        var BASE_W = 1280; // ancho de referencia en px
        function applyScale() {
            var w = window.innerWidth;
            var scale = w / BASE_W;
            // Solo aplicar en desktop (> 991px) para no afectar móvil/tablet real
            if (w > 991) {
                document.getElementById('sec-content-wrap').style.transform = 'scale(' + scale + ')';
                document.getElementById('sec-content-wrap').style.transformOrigin = 'top center';
                document.getElementById('sec-content-wrap').style.width = BASE_W + 'px';
                document.getElementById('sec-content-wrap').style.marginLeft = 'auto';
                document.getElementById('sec-content-wrap').style.marginRight = 'auto';
                // Ajustar altura del wrapper para evitar espacio en blanco
                var h = document.getElementById('sec-content-wrap').scrollHeight * scale;
                document.getElementById('sec-outer-wrap').style.height = h + 'px';
            } else {
                document.getElementById('sec-content-wrap').style.transform = '';
                document.getElementById('sec-content-wrap').style.width = '';
                document.getElementById('sec-outer-wrap').style.height = '';
            }
        }
        window.addEventListener('load', applyScale);
        window.addEventListener('resize', applyScale);
    })();
    </script>

    <div id="sec-outer-wrap" style="overflow:hidden;">
    <div id="sec-content-wrap">

    <!-- Título en barra roja -->
    <div class="container-fluid px-0">
        <div style="display:flex;justify-content:center;">
            <div class="sec-titulo-wrap">
                <?php
                $words = explode(' ', $titulo);
                $mid   = (int)ceil(count($words) / 2);
                $line1 = implode(' ', array_slice($words, 0, $mid));
                $line2 = implode(' ', array_slice($words, $mid));
                ?>
                <h4 class="sec-titulo-text">
                    <?= htmlspecialchars($line1) ?><?php if ($line2): ?><br><?= htmlspecialchars($line2) ?><?php endif; ?>
                </h4>
            </div>
        </div>
    </div>

    <div class="container-fluid px-0 py-4">
        <div class="container px-0">

        <?php if ($img1 || $texto1): ?>
        <div class="row g-0 align-items-stretch mb-5 mx-0 sec-row">
            <?php if ($img1): ?>
            <div class="col-md-6 ps-0">
                <img src="<?= $img1 ?>" class="sec-img1" style="height:100%;min-height:250px;" alt="">
            </div>
            <?php endif; ?>
            <div class="col-md-<?= $img1 ? '6' : '12' ?> d-flex align-items-center px-4 px-md-5 py-4">
                <div class="prog-sec-texto w-100"><?= $texto1 ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($img2 || $texto2): ?>
        <div class="row g-0 align-items-stretch mb-5 mx-0 sec-row sec-row-2">
            <div class="col-md-<?= $img2 ? '6' : '12' ?> d-flex align-items-center px-4 px-md-5 py-4 mb-4 mb-md-0">
                <div class="prog-sec-texto w-100"><?= $texto2 ?></div>
            </div>
            <?php if ($img2): ?>
            <div class="col-md-6 pe-0">
                <img src="<?= $img2 ?>" class="sec-img2" style="height:100%;min-height:250px;" alt="">
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Contacto -->
        <div class="text-center py-4 px-3">
            <h5 style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(107,98,90);letter-spacing:1px;margin:0;"><?= htmlspecialchars($c_titulo1) ?></h5>
            <h5 style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(107,98,90);letter-spacing:1px;margin:0 0 12px;"><?= htmlspecialchars($c_titulo2) ?></h5>
            <p style="font-family:'Montserrat',sans-serif;font-size:14px;color:rgb(107,98,90);line-height:1.9;">
                <?= nl2br(htmlspecialchars($c_direccion)) ?><br>
                Teléfono: <?= htmlspecialchars($c_telefono) ?><br>
                <?= nl2br(htmlspecialchars($c_horario)) ?><br>
                correo: <?= htmlspecialchars($c_correo) ?>
            </p>
        </div>

        </div><!-- /container -->
    </div>
    </div><!-- /sec-content-wrap -->
    </div><!-- /sec-outer-wrap -->
</div>

<?php if (!empty($galeria_imgs)): ?>
<div class="svc-gallery">
    <?php foreach ($galeria_imgs as $idx => $img):
        $isWide = ($idx === 2);
        $extraClass = $isWide ? ' svc-gallery-wide' : '';
    ?>
    <div class="svc-gallery-item<?= $extraClass ?>">
        <img src="<?= $base_path . htmlspecialchars($img['imagen_path']) ?>"
             alt="Servicios"
             class="svc-gallery-img"
             data-src="<?= $base_path . htmlspecialchars($img['imagen_path']) ?>"
             loading="lazy">
    </div>
    <?php endforeach; ?>
</div>

<!-- Lightbox -->
<div id="svc-lightbox" role="dialog" aria-modal="true" aria-label="Imagen ampliada">
    <button id="svc-lb-close" aria-label="Cerrar">&times;</button>
    <button id="svc-lb-prev" aria-label="Anterior">&#8249;</button>
    <button id="svc-lb-next" aria-label="Siguiente">&#8250;</button>
    <div id="svc-lb-inner">
        <img id="svc-lb-img" src="" alt="Imagen ampliada">
    </div>
</div>
<?php endif; ?>

<div class="pleca"><img src="<?= $base_path ?>img/pleca.png" alt="pleca"></div>

<style>
/* ---------------------------------------------------------------
   VARIABLES AJUSTABLES � modifica estos valores a tu gusto
   translate(X, Y): positivo = derecha/abajo, negativo = izq/arriba
   --------------------------------------------------------------- */
:root {
    /* Barra roja � padding vertical */
    --sec-titulo-pad-y: 1.2rem;
    --sec-titulo-max-w: 800px;
    /* Ancho de la barra roja (100% = borde a borde, ej: 80% = mas angosta, 110% = sale del container) */
    --sec-titulo-w: 100%;
    /* Posicion horizontal (0px = centrada, positivo = derecha, negativo = izquierda) */
    --sec-titulo-x: 0px;
    /* Padding horizontal barra roja (0px = ancho completo, ej: 50px = mas angosto) */
    --sec-titulo-pad-x: 0px;

    /* Imagen 1 (izquierda) */
    --sec-img1-h: 400px;
    --sec-img1-x: 0px;
    --sec-img1-y: 0px;

    /* Imagen 2 (derecha) */
    --sec-img2-h: 400px;
    --sec-img2-x: 0px;
    --sec-img2-y: 0px;
}

/* -- Desktop normal (992px � 1399px): laptop 15" y monitor 25" -- */
@media (min-width: 992px) and (max-width: 1399px) {
    :root {
        --sec-titulo-pad-y: 1.1rem;
        --sec-titulo-max-w: 900px;
        --sec-titulo-w: 88%;
        --sec-titulo-x: 8px;
        --sec-titulo-pad-x: 0px;
        --sec-img1-h: clamp(200px, 28vw, 360px);
        --sec-img1-w: 100%;        /* <-- ancho imagen 1 */
        --sec-img1-col-w: 49%;     /* <-- ancho columna imagen 1 (horizontal) */
        --sec-img1-x: 15px;
        --sec-img1-y: 0px;
        --sec-img2-h: clamp(200px, 28vw, 360px);
        --sec-img2-w: 100%;        /* <-- ancho imagen 2 */
        --sec-img2-col-w: 49%;     /* <-- ancho columna imagen 2 (horizontal) */
        --sec-img2-x: 12px;
        --sec-img2-y: 0px;
        --sec-texto1-x: 24px;       /* <-- mover texto 1 horizontal */
        --sec-texto2-x: -12px;       /* <-- mover texto 2 horizontal */
    }
    .sec-titulo-text { font-size: clamp(1.1rem, 2vw, 1.4rem); }
    .prog-sec-texto  { font-size: 14px; }
    /* Controlar ancho horizontal de columnas de imagen */
    #sec-content-wrap .sec-row .col-md-6:has(.sec-img1) { flex: 0 0 var(--sec-img1-col-w) !important; max-width: var(--sec-img1-col-w) !important; }
    #sec-content-wrap .sec-row .col-md-6:has(.sec-img2) { flex: 0 0 var(--sec-img2-col-w) !important; max-width: var(--sec-img2-col-w) !important; }
}

/* -- Pantallas grandes (>= 1400px) -- */
@media (min-width: 1400px) {
    :root {
        --sec-titulo-pad-y: 1.1rem;
        --sec-titulo-max-w: 900px;
        --sec-titulo-w: 70%;
        --sec-titulo-x: 6px;
        --sec-titulo-pad-x: 0px;
        --sec-img1-h: clamp(200px, 22vw, 320px);
        --sec-img1-w: 94.5%;        /* <-- ancho imagen 1 */
        --sec-img1-col-w: 49%;     /* <-- ancho columna imagen 1 (horizontal) */
        --sec-img1-x: 40px;
        --sec-img1-y: -6px;
        --sec-img2-h: clamp(200px, 22vw, 320px);
        --sec-img2-w: 94.5%;        /* <-- ancho imagen 2 */
        --sec-img2-col-w: 49%;     /* <-- ancho columna imagen 2 (horizontal) */
        --sec-img2-x: -20px;
        --sec-img2-y: 11px;
        --sec-texto1-x: 7px;       /* <-- mover texto 1 horizontal */
        --sec-texto2-x: 14px;       /* <-- mover texto 2 horizontal */
    }
    .sec-titulo-text { font-size: clamp(1.1rem, 2vw, 1.4rem); }
    .prog-sec-texto  { font-size: 11px !important; }
    /* Reducir texto del bloque contacto */
    #sec-content-wrap .text-center h5 { font-size: 0.85rem !important; }
    #sec-content-wrap .text-center p  { font-size: 11px !important; }
    /* Bajar bloque contacto */
    #sec-content-wrap .text-center.py-4 { padding-top: 5.5rem !important; }
    /* Limitar ancho del contenido igual que desktop normal */
    #sec-content-wrap .container { max-width: 960px !important; }
    #sec-content-wrap .sec-row   { max-width: 960px; margin-left: auto !important; margin-right: auto !important; }
    /* Quitar espacio blanco entre filas y centrar texto */
    #sec-content-wrap .sec-row.mb-5 { margin-bottom: 0 !important; }
    #sec-content-wrap .container-fluid.py-4 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
    #sec-content-wrap .col-md-6.d-flex { padding-left: 2.5rem !important; padding-right: 2.5rem !important; }
    /* Fijar alto de imagen para que no crezca con la fila */
    #sec-content-wrap .sec-img1,
    #sec-content-wrap .sec-img2 { max-height: var(--sec-img1-h) !important; min-height: unset !important; }
    /* Controlar ancho horizontal de columnas de imagen */
    #sec-content-wrap .sec-row .col-md-6:has(.sec-img1) { flex: 0 0 var(--sec-img1-col-w) !important; max-width: var(--sec-img1-col-w) !important; }
    #sec-content-wrap .sec-row .col-md-6:has(.sec-img2) { flex: 0 0 var(--sec-img2-col-w) !important; max-width: var(--sec-img2-col-w) !important; }
}

/* Barra de titulo */
.sec-titulo-wrap {
    background: rgb(200,16,44);
    width: var(--sec-titulo-w, 100%);
    padding: var(--sec-titulo-pad-y) 1rem;
    margin-bottom: 2rem;
    transform: translateX(var(--sec-titulo-x, 0px));
}
.sec-titulo-text {
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: clamp(1.1rem, 2.5vw, 1.6rem);
    color: #fff;
    text-align: center;
    margin: 0 auto;
    max-width: var(--sec-titulo-max-w, 800px);
    padding: 0 1rem;
    line-height: 1.4;
}

/* Imagenes con variables de posicion */
.sec-img1 {
    width: var(--sec-img1-w, 100%);
    max-height: var(--sec-img1-h);
    object-fit: cover;
    display: block;
    transform: translate(var(--sec-img1-x), var(--sec-img1-y));
}
.sec-img2 {
    width: var(--sec-img2-w, 100%);
    max-height: var(--sec-img2-h);
    object-fit: cover;
    display: block;
    margin-left: auto;
    transform: translate(var(--sec-img2-x), var(--sec-img2-y));
}

.prog-sec-texto {
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: rgb(107,98,90);
    line-height: 1.8;
    text-align: center;
    position: relative;
}
.sec-row .prog-sec-texto     { transform: translateX(var(--sec-texto1-x, 0px)); }
.sec-row-2 .prog-sec-texto   { transform: translateX(var(--sec-texto2-x, 0px)); }
.prog-sec-texto p,
.prog-sec-texto td,
.prog-sec-texto div { text-align: justify; }
.prog-sec-texto ul { list-style: none; padding: 0; margin-left: 0; }
.prog-sec-texto ul li {
    display: grid;
    grid-template-columns: 26px 1fr;
    column-gap: 0.4rem;
    align-items: start;
    padding: 0;
    margin-bottom: 0.3rem;
    text-align: justify;
}
.prog-sec-texto ul li::before {
    content: '';
    display: block;
    width: 20px; height: 20px;
    margin-top: 2px;
    background-image: url('../img/botoncito.png');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center top;
}

/* -- Tablet (768px � 991px) -- */
@media (max-width: 991px) {
    .sec-titulo-text { font-size: 1.1rem; }
    .sec-row { flex-direction: column !important; }
    .sec-row .col-md-5,
    .sec-row .col-md-7 { width: 100% !important; }
    .sec-row .col-md-5 { padding: 0 !important; }
    .sec-img1, .sec-img2 { max-height: 280px !important; transform: none !important; }
    .sec-row-2 .col-md-5 { order: 1 !important; }
    .sec-row-2 .col-md-7 { order: 2 !important; padding: 1rem !important; }
}

/* -- Movil (< 768px) -- */
@media (max-width: 767px) {
    .sec-titulo-wrap { padding: 0.8rem 0.5rem; }
    .sec-titulo-text { font-size: 0.95rem; padding: 0 0.5rem; }
    .prog-sec-texto  { font-size: 13px; }
    .sec-row { flex-direction: column !important; }
    .sec-row .col-md-5,
    .sec-row .col-md-7 { width: 100% !important; padding: 0.5rem 1rem !important; }
    .sec-row .col-md-5.ps-0,
    .sec-row .col-md-5.pe-0 { padding: 0 !important; }
    .sec-img1, .sec-img2 { max-height: 220px !important; transform: none !important; }
    .sec-row-2 .col-md-5 { order: 1 !important; padding: 0 !important; }
    .sec-row-2 .col-md-7 { order: 2 !important; }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<style>
/* ── Galería servicios ── */
.svc-gallery {
    display: flex;
    gap: 4px;
    padding-left: clamp(12px, 8vw, 120px);
    padding-right: clamp(12px, 8vw, 120px);
    height: 160px;
    margin-bottom: 0;
}
.svc-gallery-item { flex: 0 0 18%; overflow: hidden; cursor: pointer; border-radius: 4px; position: relative; }
.svc-gallery-item.svc-gallery-wide { flex: 0 0 28%; }
.svc-gallery-img { width: 100%; height: 160px; object-fit: cover; display: block; transition: transform 0.3s ease, filter 0.3s ease; }
.svc-gallery-item:hover .svc-gallery-img { transform: scale(1.07); filter: brightness(1.12); }
.svc-gallery-item::after { content:''; position:absolute; inset:0; background:rgba(200,16,44,0); transition:background 0.3s ease; pointer-events:none; border-radius:4px; }
.svc-gallery-item:hover::after { background:rgba(200,16,44,0.15); }
/* Lightbox */
#svc-lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:9999; align-items:center; justify-content:center; }
#svc-lightbox.active { display:flex; }
#svc-lb-inner { max-width:90vw; max-height:88vh; display:flex; align-items:center; justify-content:center; }
#svc-lb-img { max-width:90vw; max-height:85vh; object-fit:contain; border-radius:6px; box-shadow:0 8px 40px rgba(0,0,0,0.6); }
#svc-lb-close { position:fixed; top:16px; right:20px; background:none; border:none; color:#fff; font-size:2.4rem; cursor:pointer; opacity:0.8; }
#svc-lb-close:hover { opacity:1; }
#svc-lb-prev, #svc-lb-next { position:fixed; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.12); border:none; color:#fff; font-size:2.5rem; cursor:pointer; padding:8px 16px; border-radius:4px; }
#svc-lb-prev { left:12px; } #svc-lb-next { right:12px; }
#svc-lb-prev:hover, #svc-lb-next:hover { background:rgba(200,16,44,0.7); }
@media (max-width:767px) {
    .svc-gallery { flex-wrap:wrap; height:auto !important; padding-left:0 !important; gap:6px; }
    .svc-gallery-item, .svc-gallery-item.svc-gallery-wide { flex: 0 0 calc(50% - 3px) !important; }
    .svc-gallery-img { height:120px !important; }
}
@media (min-width:768px) and (max-width:991px) {
    .svc-gallery { height:140px; }
    .svc-gallery-img { height:140px !important; }
}
</style>

<script>
(function () {
    const imgs  = Array.from(document.querySelectorAll('.svc-gallery-img'));
    if (!imgs.length) return;
    const lb    = document.getElementById('svc-lightbox');
    const lbImg = document.getElementById('svc-lb-img');
    const close = document.getElementById('svc-lb-close');
    const prev  = document.getElementById('svc-lb-prev');
    const next  = document.getElementById('svc-lb-next');
    let current = 0;
    function open(idx) { current=idx; lbImg.src=imgs[idx].dataset.src; lb.classList.add('active'); document.body.style.overflow='hidden'; }
    function closeLb() { lb.classList.remove('active'); document.body.style.overflow=''; lbImg.src=''; }
    function go(dir) { current=(current+dir+imgs.length)%imgs.length; lbImg.src=imgs[current].dataset.src; }
    imgs.forEach((img,i) => img.parentElement.addEventListener('click', () => open(i)));
    close.addEventListener('click', closeLb);
    prev.addEventListener('click', () => go(-1));
    next.addEventListener('click', () => go(1));
    lb.addEventListener('click', e => { if (e.target===lb) closeLb(); });
    document.addEventListener('keydown', e => {
        if (!lb.classList.contains('active')) return;
        if (e.key==='Escape') closeLb();
        if (e.key==='ArrowLeft') go(-1);
        if (e.key==='ArrowRight') go(1);
    });
})();
</script>
