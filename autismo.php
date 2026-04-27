<?php
/**
 * autismo.php — Unidad Municipal de Autismo — DIF San Mateo Atenco
 */
require_once __DIR__ . '/includes/db.php';

$base_path   = '';
$active_page = 'autismo';
$page_title  = 'Unidad Municipal de Autismo — DIF San Mateo Atenco';

$config = null;
try {
    $pdo    = get_db();
    $stmt   = $pdo->query('SELECT * FROM autismo_config LIMIT 1');
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('autismo.php: ' . $e->getMessage());
    // Si hay error en la BD, continuar sin config
    $config = [];
}

// Redirigir a mantenimiento si está activo (valor 1)
if (isset($config['en_mantenimiento']) && $config['en_mantenimiento'] == 1) {
    require_once __DIR__ . '/mantenimiento.php';
    exit;
}

// Si no hay config, usar valores por defecto
if (empty($config)) {
    $config = [
        'logo_path' => '',
        'texto_derecha' => '',
        'texto_centro' => '',
        'texto_inferior' => '',
        'imagen_centro_path' => '',
        'imagen_inferior_path' => ''
    ];
}

$logo_path      = !empty($config['logo_path'])            ? htmlspecialchars($config['logo_path'])            : 'img/UMA_SMA.png';
$texto_derecha  = isset($config['texto_derecha'])  ? $config['texto_derecha']  : '';
$texto_centro   = isset($config['texto_centro'])   ? $config['texto_centro']   : '';
$texto_inferior = isset($config['texto_inferior']) ? $config['texto_inferior'] : '';
$img_centro     = !empty($config['imagen_centro_path'])   ? htmlspecialchars($config['imagen_centro_path'])   : 'img/front-view-boy-playing-memory-game.jpg';
$img_inferior   = !empty($config['imagen_inferior_path']) ? htmlspecialchars($config['imagen_inferior_path']) : 'img/top-view-kid-playing-with-colorful-game.jpg';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid px-0" style="background:#f5f5f5;overflow-x:hidden;">

    <!-- ── Título centrado ── -->
    <div class="container px-0">
        <div class="text-center pt-4 pb-0 wow fadeIn aut-titulo" data-wow-delay="0.1s">
            <h4 style="font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:2px;color:rgb(107,98,90);margin:0;">UNIDAD MUNICIPAL DE AUTISMO</h4>
            <h5 style="font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:2px;color:rgb(107,98,90);margin:4px 0 0;font-size:1.5rem;">SAN MATEO ATENCO</h5>
            <div style="height:16px;background:rgb(199,14,44);width:130px;margin:10px auto 0;"></div>
        </div>
    </div>

    <!-- ── Plastas superiores ── -->
    <div class="container px-0">
    <div class="row g-0" style="overflow:visible;">
        <div class="col-6 px-0" style="overflow:visible;">
            <img src="img/plasta_amarilla.png" class="aut-plasta-amarilla" alt="">
        </div>
        <div class="col-6 px-0 d-flex justify-content-end" style="overflow:visible;">
            <img src="img/plasta_azul.png" class="aut-plasta-azul" alt="">
        </div>
    </div>
    </div>

        <!-- ── Fila logo + texto ── -->
    <div class="container px-0">
        <div class="row g-0 align-items-center pb-4 aut-logo-row">
            <div class="col-md-6 text-center px-4">
                <img src="<?= $logo_path ?>" class="aut-logo-img" alt="Unidad Municipal de Autismo">
            </div>
            <div class="col-md-6 px-4 px-md-5 pt-4">
                <div class="aut-texto aut-texto-derecha"><?= nl2br(htmlspecialchars($texto_derecha)) ?></div>
            </div>
        </div>
    </div>

    <!-- ── Contenido central ── -->
    <div class="container py-2" style="margin-bottom: var(--contenido-mb, -180px);">

        <!-- texto izquierda + imagen derecha -->
        <div class="row align-items-center mb-3 wow fadeIn aut-content-row" data-wow-delay="0.3s">
            <div class="col-md-6 mb-4 mb-md-0 px-4 px-md-5">
                <div class="aut-texto aut-texto-centro"><?= nl2br(htmlspecialchars($texto_centro)) ?></div>
            </div>
            <div class="col-md-6 text-center px-4">
                <img src="<?= $img_centro ?>" class="aut-img-centro" alt="Autismo">
            </div>
        </div>

        <!-- imagen izquierda + texto derecha -->
        <div class="row align-items-center mb-3 wow fadeIn aut-inferior-row" data-wow-delay="0.4s">
            <div class="col-md-6 text-center mb-4 mb-md-0 px-4">
                <img src="<?= $img_inferior ?>" class="aut-img-inferior" alt="Autismo">
            </div>
            <div class="col-md-6 px-4 px-md-5">
                <div class="aut-texto aut-texto-inferior"><?= nl2br(htmlspecialchars($texto_inferior)) ?></div>
            </div>
        </div>

    </div>

    <!-- ── Contacto con plastas inferiores ── -->
    <div class="container px-0 wow fadeIn" data-wow-delay="0.5s">
        <div class="row g-0">
            <div class="col-3 aut-plasta-col-bl">
                <img src="img/plasta_rosa.png" class="aut-plasta-bl" alt="">
            </div>
            <div class="col-6 col-sm-6 col-xs-12 d-flex align-items-center justify-content-center aut-contacto-col">
                <div class="text-center py-5 px-3 aut-contacto-inner">
                    <h5 style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(107,98,90);letter-spacing:1px;margin:0;">SERVICIOS MÉDICOS</h5>
                    <h5 style="font-family:'Montserrat',sans-serif;font-weight:800;color:rgb(107,98,90);letter-spacing:1px;margin:0 0 8px;">CLASES Y TALLERES</h5>
                    <p class="aut-contacto-txt">
                        Mariano Matamoros 310, Barrio de la Concepción CP 52105,<br>
                        San Mateo Atenco, Méx.<br>
                        Teléfono: 722 970 77 86<br>
                        Horario de Lunes a Viernes<br>
                        8:00 am a 3:30 pm<br>
                        correo: adultomayor@difsanmateoatenco.gob.mx
                    </p>
                </div>
            </div>
            <div class="col-3 d-flex justify-content-end aut-plasta-col-br">
                <img src="img/plasta_verde.png" class="aut-plasta-br" alt="">
            </div>
        </div>
        <!-- Fila solo móvil: plastas abajo -->
        <div class="row g-0 d-none aut-plastas-movil">
            <div class="col-6">
                <img src="img/plasta_rosa.png" class="aut-plasta-bl" alt="">
            </div>
            <div class="col-6 d-flex justify-content-end">
                <img src="img/plasta_verde.png" class="aut-plasta-br" alt="">
            </div>
        </div>
</div>

</div>

<div style="height:3rem;background:#f5f5f5;"></div>
<div class="pleca"><img src="img/pleca.png" alt="pleca"></div>

<style>
/*
 * ── PLASTAS — ajusta estas variables para mover/redimensionar ──────────────
 * translate(X, Y): positivo = derecha/abajo, negativo = izquierda/arriba
 */
:root {
    /* Plasta amarilla (superior izquierda) — NO MOVER */
    --pa-w: clamp(160px, 26vw, 332px);
    --pa-max: 600px;
    --pa-x: 39px;
    --pa-y: -47px;

    /* Plasta azul (superior derecha) — NO MOVER */
    --pb-w: clamp(150px, 36vw, 512px);
    --pb-max: 600px;
    --pb-x: -19px;
    --pb-y: -93px;

    /* Plasta rosa (inferior izquierda) — mismos valores que amarilla */
    --pr-w: clamp(160px, 45vw, 553px);
    --pr-max: 600px;
    --pr-x: 9px;
    --pr-y: 72px;

    /* Plasta verde (inferior derecha) — mismos valores que azul */
    --pv-w: clamp(150px, 45vw, 480px);
    --pv-max: 600px;
    --pv-x: -2px;
    --pv-y: 72px;

    /* Logo */
    --logo-w: 100%;
    --logo-max-w: 590px;
    --logo-h: 400px;
    --logo-x: 0px;
    --logo-y: -182px;

    /* Imagen central (foto niño con bloques) */
    --img-centro-w: 100%;
    --img-centro-max-w: 590px;
    --img-centro-h: 400px;
    --img-centro-x: 0px;
    --img-centro-y: -200px;

    /* Imagen inferior (foto niño en alfombra) */
    --img-inferior-w: 100%;
    --img-inferior-max-w: 590px;
    --img-inferior-h: 400px;
    --img-inferior-x: 0px;
    --img-inferior-y: -175px;

    /* Textos — margen superior para subir/bajar */
    --texto-derecha-y: -170px;
    --texto-centro-y: -222px;
    --texto-inferior-y: -200px;

    /* Bloque de contacto (SERVICIOS MÉDICOS / CLASES Y TALLERES) */
    --contacto-x: 0px;
    --contacto-y: 0px;

    /* Espacio entre contenido y contacto — ajusta si hay espacio en blanco */
    --contenido-mb: -210px;

    /* ── Tamaño y posición X de plastas (ajusta a tu gusto) ── */
    --pa-size: 100%;        /* amarilla: tamaño (% de su col) */
    --pa-max-w: 332px;      /* amarilla: máximo en px */
    --pa-offset-x: -65px;     /* amarilla: mover horizontal (+ derecha, - izquierda) */
    --pb-size: 100%;        /* azul: tamaño */
    --pb-max-w: 490px;      /* azul: máximo en px */
    --pb-offset-x: 91px;     /* azul: mover horizontal */
    --pb-offset-y: -68px;      /* azul: mover vertical (+ abajo, - arriba) */
    --pr-size: clamp(120px,24.4vw,332px); /* rosa: tamaño */
    --pr-max-w: 332px;      /* rosa: máximo en px */
    --pr-offset-x: 0px;     /* rosa: mover horizontal */
    --pr-offset-y: 0px;     /* rosa: mover vertical */
    --pv-size: clamp(160px,36vw,490px);   /* verde: tamaño */
    --pv-max-w: 490px;      /* verde: máximo en px */
    --pv-offset-x: 0px;     /* verde: mover horizontal */
    --pv-offset-y: 0px;     /* verde: mover vertical */
}
.aut-plasta-amarilla { display:block; width:var(--pa-size); max-width:var(--pa-max-w); height:auto; transform:translateX(var(--pa-offset-x)); }
.aut-plasta-azul { display:block; width:var(--pb-size); max-width:var(--pb-max-w); height:auto; margin-left:auto; transform:translate(var(--pb-offset-x),var(--pb-offset-y)); }
.aut-contacto-wrap {
    overflow: hidden;
}
.aut-plasta-bl { display:block; width:var(--pr-size); max-width:var(--pr-max-w, 100%); height:auto; transform:translate(var(--pr-offset-x), var(--pr-offset-y)); }
.aut-plasta-br { display:block; width:var(--pv-size); max-width:var(--pv-max-w, 100%); height:auto; margin-left:auto; transform:translate(var(--pv-offset-x), var(--pv-offset-y)); }
.aut-contacto-wrap .text-center { position: relative; z-index: 2; }
.aut-texto {
    font-family: 'Montserrat', sans-serif;
    font-size: 16px; font-weight: 500;
    color: rgb(107,98,90); line-height: 1.8; text-align: center;
}
/* Logo e imágenes con variables de posición y tamaño */
.aut-logo-img {
    width: var(--logo-w);
    max-width: var(--logo-max-w);
    height: var(--logo-h);
    object-fit: contain;
    display: block;
    margin: 0 auto;
    transform: translate(var(--logo-x), var(--logo-y));
}
.aut-img-centro {
    width: var(--img-centro-w);
    max-width: var(--img-centro-max-w);
    height: var(--img-centro-h);
    object-fit: cover;
    display: block;
    margin: 0 auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translate(var(--img-centro-x), var(--img-centro-y));
}
.aut-img-inferior {
    width: var(--img-inferior-w);
    max-width: var(--img-inferior-max-w);
    height: var(--img-inferior-h);
    object-fit: cover;
    display: block;
    margin: 0 auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translate(var(--img-inferior-x), var(--img-inferior-y));
}
.aut-texto-derecha  { transform: translateY(var(--texto-derecha-y)); }
.aut-texto-centro   { transform: translateY(var(--texto-centro-y)); }
.aut-texto-inferior { transform: translateY(var(--texto-inferior-y)); }
.aut-contacto-inner { transform: translate(var(--contacto-x), var(--contacto-y)); }
.aut-contacto-txt {
    font-family: 'Montserrat', sans-serif;
    font-size: 14px; color: rgb(107,98,90); line-height: 1.9;
}

/* ── Desktop normal (992px – 1399px): laptop 15" ── */
@media (min-width: 992px) and (max-width: 1399px) {
    :root {
        --pa-size: 100%; --pa-max-w: 332px; --pa-offset-x: -65px;
        --pb-size: 100%; --pb-max-w: 490px; --pb-offset-x: 87px; --pb-offset-y: -78px;
        --pr-size: 200%; --pr-max-w: 540px; --pr-offset-x: -95px; --pr-offset-y: 21px;
        --pv-size: 175%; --pv-max-w: 500px; --pv-offset-x: 75px; --pv-offset-y: 23px;
    }
}

/* ── Pantallas grandes (≥ 1400px): monitor ── */
@media (min-width: 1400px) {
    :root {
        --pa-size: 100%; --pa-max-w: 332px; --pa-offset-x: -41px;
        --pb-size: 100%; --pb-max-w: 490px; --pb-offset-x: 74px; --pb-offset-y: -78px;
        --pr-size: 400%; --pr-max-w: 630px; --pr-offset-x: -79px; --pr-offset-y: 8px;
        --pv-size: 400%; --pv-max-w: 614px; --pv-offset-x: 67px; --pv-offset-y: 14px;
    }
}

/* ── Tablet (768px – 991px) ── */
@media (min-width: 768px) and (max-width: 991px) {
    :root {
        --pa-size: 100%; --pa-max-w: 260px; --pa-offset-x: -33px;
        --pb-size: 100%; --pb-max-w: 290px; --pb-offset-x: 23px; --pb-offset-y: -29px;
        --pr-size: 300%; --pr-max-w: 323px; --pr-offset-x: -58px; --pr-offset-y: 142px;
        --pv-size: 300%; --pv-max-w: 323px; --pv-offset-x: 50px; --pv-offset-y: 41px;
    }
    /* Reset transforms */
    .aut-logo-img    { transform: none !important; height: auto !important; max-width: 280px !important; }
    .aut-img-centro  { transform: none !important; height: 240px !important; }
    .aut-img-inferior{ transform: none !important; height: 240px !important; }
    .aut-texto-derecha, .aut-texto-centro, .aut-texto-inferior { transform: none !important; }
    .aut-contacto-inner { transform: none !important; }
    /* Sin margin negativo */
    .aut-logo-row { margin-top: 0 !important; }
    .container[style*="contenido-mb"] { margin-bottom: 0 !important; }
    /* Orden: logo → texto → imagen → texto → imagen → texto */
    .aut-logo-row { display: flex !important; flex-direction: column !important; }
    .aut-logo-row .col-md-6:first-child { order: 1; width: 100% !important; text-align: center !important; padding: 1rem !important; }
    .aut-logo-row .col-md-6:last-child  { order: 2; width: 100% !important; padding: 1rem 2rem !important; }
    .aut-content-row { display: flex !important; flex-direction: column !important; }
    .aut-content-row .col-md-6:first-child { order: 2; width: 100% !important; padding: 1rem 2rem !important; }
    .aut-content-row .col-md-6:last-child  { order: 1; width: 100% !important; padding: 1rem !important; }
    .aut-inferior-row { display: flex !important; flex-direction: column !important; }
    .aut-inferior-row .col-md-6:first-child { order: 1; width: 100% !important; padding: 1rem !important; }
    .aut-inferior-row .col-md-6:last-child  { order: 2; width: 100% !important; padding: 1rem 2rem !important; }
}

/* ── Móvil (< 768px) ── */
@media (max-width: 767px) {
    :root {
        --pa-size: 100%; --pa-max-w: 214px; --pa-offset-x: -33px;
        --pb-size: 100%; --pb-max-w: 280px; --pb-offset-x: 26px; --pb-offset-y: -28px;
        --pr-size: 300%; --pr-max-w: 260px; --pr-offset-x: -38px; --pr-offset-y: -50px;
        --pv-size: 300%; --pv-max-w: 260px; --pv-offset-x: 37px; --pv-offset-y: -44px;
    }
    /* Reset transforms */
    .aut-logo-img    { transform: none !important; height: auto !important; max-width: 220px !important; }
    .aut-img-centro  { transform: none !important; height: 200px !important; }
    .aut-img-inferior{ transform: none !important; height: 200px !important; }
    .aut-texto-derecha, .aut-texto-centro, .aut-texto-inferior { transform: none !important; }
    .aut-contacto-inner { transform: none !important; }
    /* Sin margin negativo */
    .aut-logo-row { margin-top: 0 !important; }
    .container[style*="contenido-mb"] { margin-bottom: 0 !important; }
    /* Título */
    .aut-titulo h4 { font-size: 1rem !important; letter-spacing: 1px !important; }
    .aut-titulo h5 { font-size: 0.85rem !important; }
    /* Orden: logo → texto → imagen → texto → imagen → texto */
    .aut-logo-row { display: flex !important; flex-direction: column !important; }
    .aut-logo-row .col-md-6:first-child { order: 1; width: 100% !important; text-align: center !important; padding: 0.5rem 1rem !important; }
    .aut-logo-row .col-md-6:last-child  { order: 2; width: 100% !important; padding: 0.5rem 1rem !important; }
    .aut-content-row { display: flex !important; flex-direction: column !important; }
    .aut-content-row .col-md-6:first-child { order: 2; width: 100% !important; padding: 0.5rem 1rem !important; }
    .aut-content-row .col-md-6:last-child  { order: 1; width: 100% !important; padding: 0.5rem 1rem !important; }
    .aut-inferior-row { display: flex !important; flex-direction: column !important; }
    .aut-inferior-row .col-md-6:first-child { order: 1; width: 100% !important; padding: 0.5rem 1rem !important; }
    .aut-inferior-row .col-md-6:last-child  { order: 2; width: 100% !important; padding: 0.5rem 1rem !important; }
    .aut-contacto-wrap { min-height: 200px; }
    .aut-contacto-txt { font-size: 13px; }
    /* Móvil: ocultar plastas del row, mostrar contacto full width */
    .aut-contacto-wrap .row { display: block !important; overflow: hidden !important; }
    .aut-contacto-wrap .col-3 { display: none !important; }
    .aut-contacto-wrap .col-6, .aut-contacto-col { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; text-align: center !important; padding: 0 1rem !important; }
    .aut-contacto-inner { text-align: center !important; margin: 0 auto !important; width: 100% !important; }
    .aut-contacto-inner h5, .aut-contacto-inner p { text-align: center !important; }
    .aut-contacto-wrap .col-6 > div { margin: 0 auto !important; text-align: center !important; display: flex !important; flex-direction: column !important; align-items: center !important; }
    .aut-plasta-bl, .aut-plasta-br { display: none !important; }
    /* Ocultar plastas del col-3 en row principal */
    .aut-plasta-col-bl, .aut-plasta-col-br { display: none !important; }
    /* Mostrar fila extra de plastas abajo */
    .aut-plastas-movil { display: flex !important; }
    .aut-plastas-movil .aut-plasta-bl,
    .aut-plastas-movil .aut-plasta-br { display: block !important; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
