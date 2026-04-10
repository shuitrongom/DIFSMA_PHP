<?php
/**
 * tramites/_tramite_template.php — Plantilla compartida para páginas de trámites
 *
 * Variables requeridas antes de incluir este archivo:
 *   $tramite_slug     — string: slug del trámite (e.g. 'PMPNNA')
 *   $default_image    — string: ruta relativa a la imagen fallback (e.g. 'img/tram_serv_procuraduria.png')
 *
 * Requisitos: 8.4, 8.5
 */

require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'tramites';

// ── Consultar registro del trámite ──────────────────────────────────────────
$tramite = null;
$galeria_imgs = [];
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare('SELECT titulo, imagen_path, contenido FROM tramites WHERE slug = ?');
    $stmt->execute([$tramite_slug]);
    $tramite = $stmt->fetch();

    if ($tramite) {
        $tid = null;
        $ts = $pdo->prepare('SELECT id FROM tramites WHERE slug = ?');
        $ts->execute([$tramite_slug]);
        $tr = $ts->fetch();
        if ($tr) {
            $gs = $pdo->prepare('SELECT imagen_path FROM tramites_galeria WHERE tramite_id = ? AND activo = 1 ORDER BY orden ASC');
            $gs->execute([$tr['id']]);
            $galeria_imgs = $gs->fetchAll();
        }
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log("tramite {$tramite_slug} PDOException: " . $e->getMessage());
    }
}

// Determinar título, imagen y contenido con fallbacks
$titulo    = $tramite_slug;
$imagen    = null; // Sin imagen por defecto
$contenido = '';

if ($tramite) {
    if (!empty($tramite['titulo'])) {
        $titulo = $tramite['titulo'];
    }
    if (!empty($tramite['imagen_path'])) {
        $imagen = htmlspecialchars($base_path . $tramite['imagen_path'], ENT_QUOTES, 'UTF-8');
    }
    if (!empty($tramite['contenido'])) {
        $contenido = $tramite['contenido'];
    }
}

$page_title = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . ' — DIF San Mateo Atenco';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <!-- Blog Start-->
    <div class="container-fluid blog py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 600px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 8px auto 24px;"></div>
            </div>
            <div class="row g-5 justify-content-center">
                <div class="col-md-6 col-lg-6 col-xl-10 wow fadeIn" data-wow-delay="0.5s">
                    <div class="blog-item rounded-bottom">
                        <?php if ($imagen): ?>
                        <div class="overflow-hidden position-relative" style="background:#fff;">
                            <img src="<?= $imagen ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <?php endif; ?>
                        <div class="px-4 pb-4 <?= $imagen ? '' : 'pt-4' ?> rounded-bottom" style="background:rgba(255, 255, 255, 1);">
                            <div class="blog-text-inner" style="padding-top:1rem;">
                                <style>
                                    .blog-text-inner, .blog-text-inner * { color: rgb(107,98,90); !important; }
                                    .blog-text-inner p,
                                    .blog-text-inner td,
                                    .blog-text-inner div { text-align: justify !important; }
                                    .blog-text-inner h1,
                                    .blog-text-inner h2,
                                    .blog-text-inner h3,
                                    .blog-text-inner h4,
                                    .blog-text-inner h5,
                                    .blog-text-inner h6 { text-align: left !important; }
                                    /* Lista con viñeta imagen */
                                    .blog-text-inner ul {
                                        list-style: none !important;
                                        padding: 0 !important;
                                        margin-left: 0 !important;
                                    }
                                    .blog-text-inner ul li {
                                        display: grid !important;
                                        grid-template-columns: 26px 1fr !important;
                                        column-gap: 0.4rem !important;
                                        align-items: start !important;
                                        padding: 0 !important;
                                        margin: 0 0 0.3rem 0 !important;
                                        text-indent: 0 !important;
                                        text-align: justify !important;
                                    }
                                    .blog-text-inner ul li::before {
                                        content: '' !important;
                                        display: block !important;
                                        width: 20px !important;
                                        height: 20px !important;
                                        margin-top: 3px !important;
                                        background-image: url('<?= $base_path ?>img/botoncito.png') !important;
                                        background-size: contain !important;
                                        background-repeat: no-repeat !important;
                                        background-position: center top !important;
                                    }
                                </style>
                                <?php
                                // Limpiar &nbsp; al inicio de cada <li> que TinyMCE inyecta
                                $contenido_clean = preg_replace(
                                    '/(<li[^>]*>)(\s|&nbsp;)+/i',
                                    '$1',
                                    $contenido
                                );
                                echo $contenido_clean;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Blog End-->

    <!-- Galería -->
    <?php if (!empty($galeria_imgs)): ?>
    <div class="tram-gallery">
        <?php foreach ($galeria_imgs as $idx => $gi):
            $isWide = ($idx === 2);
        ?>
        <div class="tram-gallery-item<?= $isWide ? ' tram-gallery-wide' : '' ?>">
            <img src="<?= $base_path . htmlspecialchars($gi['imagen_path']) ?>"
                 alt="<?= htmlspecialchars($titulo) ?>"
                 class="tram-gallery-img"
                 data-src="<?= $base_path . htmlspecialchars($gi['imagen_path']) ?>"
                 loading="lazy">
        </div>
        <?php endforeach; ?>
    </div>
    <div id="tram-lightbox" role="dialog" aria-modal="true">
        <button id="tram-lb-close">&times;</button>
        <button id="tram-lb-prev">&#8249;</button>
        <button id="tram-lb-next">&#8250;</button>
        <div id="tram-lb-inner"><img id="tram-lb-img" src="" alt=""></div>
    </div>
    <style>
    .tram-gallery { display:flex; gap:4px; padding:0 clamp(12px,8vw,120px); height:160px; margin-bottom:2rem; }
    .tram-gallery-item { flex:0 0 18%; overflow:hidden; cursor:pointer; border-radius:4px; position:relative; }
    .tram-gallery-item.tram-gallery-wide { flex:0 0 28%; }
    .tram-gallery-img { width:100%; height:160px; object-fit:cover; display:block; transition:transform .3s,filter .3s; }
    .tram-gallery-item:hover .tram-gallery-img { transform:scale(1.07); filter:brightness(1.12); }
    .tram-gallery-item::after { content:''; position:absolute; inset:0; background:rgba(200,16,44,0); transition:background .3s; pointer-events:none; border-radius:4px; }
    .tram-gallery-item:hover::after { background:rgba(200,16,44,0.15); }
    #tram-lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:9999; align-items:center; justify-content:center; }
    #tram-lightbox.active { display:flex; }
    #tram-lb-inner { max-width:90vw; max-height:88vh; display:flex; align-items:center; justify-content:center; }
    #tram-lb-img { max-width:90vw; max-height:85vh; object-fit:contain; border-radius:6px; box-shadow:0 8px 40px rgba(0,0,0,0.6); }
    #tram-lb-close { position:fixed; top:16px; right:20px; background:none; border:none; color:#fff; font-size:2.4rem; cursor:pointer; opacity:.8; }
    #tram-lb-close:hover { opacity:1; }
    #tram-lb-prev,#tram-lb-next { position:fixed; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.12); border:none; color:#fff; font-size:2.5rem; cursor:pointer; padding:8px 16px; border-radius:4px; }
    #tram-lb-prev { left:12px; } #tram-lb-next { right:12px; }
    #tram-lb-prev:hover,#tram-lb-next:hover { background:rgba(200,16,44,0.7); }
    @media(max-width:767px) { .tram-gallery{flex-wrap:wrap;height:auto;padding:0;gap:6px;} .tram-gallery-item,.tram-gallery-item.tram-gallery-wide{flex:0 0 calc(50% - 3px)!important;} .tram-gallery-img{height:120px!important;} }
    </style>
    <script>
    (function(){
        var imgs=Array.from(document.querySelectorAll('.tram-gallery-img'));
        if(!imgs.length)return;
        var lb=document.getElementById('tram-lightbox'),lbImg=document.getElementById('tram-lb-img');
        var current=0;
        function open(i){current=i;lbImg.src=imgs[i].dataset.src;lb.classList.add('active');document.body.style.overflow='hidden';}
        function close(){lb.classList.remove('active');document.body.style.overflow='';lbImg.src='';}
        function go(d){current=(current+d+imgs.length)%imgs.length;lbImg.src=imgs[current].dataset.src;}
        imgs.forEach(function(img,i){img.parentElement.addEventListener('click',function(){open(i);});});
        document.getElementById('tram-lb-close').addEventListener('click',close);
        document.getElementById('tram-lb-prev').addEventListener('click',function(){go(-1);});
        document.getElementById('tram-lb-next').addEventListener('click',function(){go(1);});
        lb.addEventListener('click',function(e){if(e.target===lb)close();});
        document.addEventListener('keydown',function(e){if(!lb.classList.contains('active'))return;if(e.key==='Escape')close();if(e.key==='ArrowLeft')go(-1);if(e.key==='ArrowRight')go(1);});
    })();
    </script>
    <?php endif; ?>

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
