<?php
/**
 * includes/header.php
 *
 * Contiene el <head> completo, el <body>, el spinner, el logo principal
 * y el logo secundario (widget del clima, logo "Unidos con Amor", buscador).
 * NO incluye la navbar (ver navbar.php).
 *
 * Variables PHP aceptadas (deben definirse ANTES de hacer require_once):
 *   $base_path  (string) — prefijo de ruta relativa, p.ej. '../' para subdirectorios.
 *                          Por defecto '' (raíz del sitio).
 *   $page_title (string) — texto del <title>. Por defecto 'DIF San Mateo Atenco'.
 *
 * Ejemplo de uso desde una página en acerca-del-dif/:
 *   <?php $base_path = '../'; $page_title = 'Presidencia'; require_once '../includes/header.php'; ?>
 */

if (!isset($base_path))  $base_path  = '';
if (!isset($page_title)) $page_title = 'DIF San Mateo Atenco';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Montserrat:wght@200;400;500;600;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="icon" href="<?= $base_path ?>img/favicon_new.png" sizes="35x35">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="<?= $base_path ?>lib/animate/animate.min.css" rel="stylesheet">
    <link href="<?= $base_path ?>lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?= $base_path ?>css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="<?= $base_path ?>css/style.css?v=20" rel="stylesheet">

    <!-- Swiper CSS (bundle) -->
    <link rel="stylesheet" href="<?= $base_path ?>css/swiper-bundle.min.css" />

    <style>
        :root {
            --bg: #0f1724;
            --card-bg: #0b1220;
        }

        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 16px;
            overflow: hidden;
        }

        /* Swiper 3D coverflow */
        .swiper {
            width: 100%;
            padding: 30px 0 50px !important;
        }

        .swiper-slide {
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60%;
            max-width: 520px;
            transition: opacity .4s, transform .4s;
            opacity: 0.45;
            transform: scale(0.82);
        }

        .swiper-slide-active {
            opacity: 1;
            transform: scale(1);
        }

        .swiper-slide img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
            border-radius: 10px;
            user-select: none;
            -webkit-user-drag: none;
        }

        /* Pagination dots */
        .swiper-pagination-bullet {
            background: #aaa;
            opacity: 1;
            width: 10px;
            height: 10px;
            border-radius: 5px;
            transition: background .25s, width .25s;
        }

        .swiper-pagination-bullet-active {
            background: #c0392b;
            width: 24px;
        }

        @media (max-width: 768px) {
            .swiper-slide { width: 80%; }
        }

        @media (max-width: 480px) {
            .swiper-slide { width: 92%; opacity: 1; transform: scale(1); }
            .swiper-slide-active { transform: scale(1); }
        }
    </style>
    <style>
        :root {
            --gap: 12px;
            --nav-size: 40px;
            --dot-size: 10px;
            --accent: #ffffff;
        }

        * {
            box-sizing: border-box
        }

        .slider {
            position: relative;
            width: 100%;
            margin: 0;
            overflow: hidden;
            border-radius: 0;
            z-index: 1;
        }
        
        /* Contenedor del slider principal debe estar debajo del navbar */
        .container-fluid.border-bottom.bg-white .slider {
            z-index: 1;
        }

        .viewport {
            display: flex;
            transition: transform .5s ease;
        }

        /* responsive height */
        .slide {
            min-width: 100%;
            flex: 0 0 100%;
            position: relative;
        }

        .slide img {
            width: 100%;
            height: auto;
            max-height: 80vh;
            object-fit: cover;
            display: block;
        }

        @media (max-width: 768px) {
            .slide img { max-height: 50vw; }
        }

        @media (max-width: 480px) {
            .slide img { max-height: 60vw; }
        }

        /* captions (optional) */
        .caption {
            position: absolute;
            left: 16px;
            bottom: 16px;
            padding: 10px 14px;
            background: rgba(14, 13, 13, 0.45);
            color: var(--accent);
            border-radius: 6px;
            font-size: 14px;
            backdrop-filter: blur(4px)
        }

        /* dots */
        .dots {
            position: relative;
            left: auto;
            transform: none;
            bottom: auto;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
            background: transparent;
        }

        .dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: rgba(0,0,0,0.3);
            border-radius: 50%;
            cursor: pointer;
            border: none;
            padding: 0;
            transition: background .25s, transform .25s, width .25s;
        }

        .dot.active {
            background: rgb(200,16,44);
            width: 28px;
            border-radius: 5px;
            transform: none;
        }

        /* responsive tweaks */
        @media (max-width:420px) {
            .caption {
                font-size: 12px;
                padding: 8px 10px
            }
        }

        /* Global section rhythm */
        section, .container-fluid { scroll-margin-top: 70px; }
        .section-title { margin-bottom: 2rem; }

        /* Color de fuente principal al 80% negro — excluye navbar y footer */
        body {
            color: rgba(0, 0, 0, 0.8);
        }

        /* Notice slider arrows */
        /* Notice slider */
        .notice-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: #3f3e3e;
            color: #fff;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .2s, transform .2s;
            box-shadow: 0 4px 12px rgba(0,0,0,.2);
        }
        .notice-btn:hover { background: #868484; transform: scale(1.08); }
        .notice-slide {
            flex: 0 0 33.333%;
            max-width: 33.333%;
            box-sizing: border-box;
        }
        .notice-img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 10px;
            object-fit: contain;
        }
        .notice-swiper {
            width: 100%;
            padding-bottom: 8px;
        }
        .notice-swiper .swiper-slide {
            width: auto;
            opacity: 1;
            transform: none;
        }
        @media (max-width: 768px) {
            .notice-slide { flex: 0 0 50%; max-width: 50%; }
        }
        @media (max-width: 576px) {
            .notice-slide { flex: 0 0 100%; max-width: 100%; }
        }
    </style>
    <style>
        .navbar-nav {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .nav-item {
            flex: 1 0 calc(100% / 6);
            text-align: center;
            color: white;
            text-decoration: none;
        }

        /* Nav links principales — solo los que NO son dropdown-item */
        .navbar-nav .nav-link-green,
        .navbar-nav .nav-link-red,
        .navbar-nav .nav-link-yellow,
        .navbar-nav .nav-link-pink,
        .navbar-nav .nav-link-purple,
        .navbar-nav .nav-link-blue {
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            color: #fff !important;
        }
        /* Hover en nav-links principales */
        .navbar-nav .nav-link-green:hover,
        .navbar-nav .nav-link-red:hover,
        .navbar-nav .nav-link-yellow:hover,
        .navbar-nav .nav-link-pink:hover,
        .navbar-nav .nav-link-purple:hover,
        .navbar-nav .nav-link-blue:hover {
            color: var(--bs-primary) !important;
        }
        /* Dropdown items del NAVBAR — blancos */
        .navbar .dropdown-menu .dropdown-item {
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            color: #fff !important;
        }
        .navbar .dropdown-menu .dropdown-item:hover {
            color: rgba(0,0,0,0.8) !important;
        }

        /* Botones Ver Programas */
        .btn-ver-programas {
            width: 100%;
            border: none;
            background: transparent;
            padding: 0;
            cursor: pointer;
        }
        .btn-ver-programas img {
            transition: opacity .2s, filter .2s;
        }
        .btn-ver-programas:hover img {
            opacity: 0.85;
            filter: brightness(1.1);
        }
        /* Ocultar flecha dropdown en imagen */
        .btn-ver-programas.dropdown-toggle::after {
            display: none;
        }

        /* Accordion body */
        .accordion-body {
            color: rgba(0, 0, 0, 0.8);
        }

        /* Separador blanco en dropdown-items desde el segundo */
        .dropdown-menu .dropdown-item + .dropdown-item {
            border-top: 2px solid rgba(255,255,255,0.8);
        }

        /* Transparencia hover — tarjeta se levanta */
        .program-item {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .program-item:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 12px 28px rgba(0,0,0,0.15);
            z-index: 2;
            position: relative;
        }
        /* Evitar que overflow:hidden corte la imagen en hover */
        .program-item .overflow-hidden {
            overflow: visible !important;
        }
        .program-item .img-border {
            overflow: visible !important;
        }

        /* NAVBAR DROPDOWN ITEMS — FORZAR BLANCO */
        #navbarCollapse .dropdown-menu a.dropdown-item,
        #navbarCollapse .dropdown-menu a.dropdown-item:link,
        #navbarCollapse .dropdown-menu a.dropdown-item:visited {
            color: #ffffff !important;
        }

        /* Programas dropdown — acordeones en negro */
        .events-text .dropdown-menu .accordion-button,
        .events-text .dropdown-menu .accordion-body {
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            color: rgba(0, 0, 0, 0.8) !important;
        }

        /* Copyright bar */
        .copyright-bar {
            background:rgb(107,98,90);
            color: #fff;
            padding: 16px 0;
            text-align: center;
            font-size: 13px;
        }

        /* Mobile navbar dropdown fix — keep dropdowns inside collapsed menu */
        @media (max-width: 1199.98px) {
            #navbarCollapse .dropdown-menu {
                position: static !important;
                float: none;
                width: 100%;
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }
            #navbarCollapse .dropdown-menu .dropdown-item {
                padding: 8px 20px;
                font-size: 14px;
                white-space: normal;
                word-wrap: break-word;
            }
        }
    </style>
    <style>html, body { overflow-x: hidden; max-width: 100vw; }</style>
    <style>
        body { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
        h4.mb-1.d-inline-block { text-transform: uppercase; }
        /* Forzar dropdown siempre abajo del navbar desde el inicio */
        .navbar .nav-item { position: relative !important; }
        .navbar .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            bottom: auto !important;
            transform: none !important;
            display: none !important;
        }
        .navbar .nav-item:hover .dropdown-menu,
        .navbar .nav-item .dropdown-menu.show {
            display: block !important;
        }
    </style>
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show w-100 bg-white position-fixed d-flex align-items-center justify-content-center"
        style="top: 0; left: 0; right: 0; bottom: 0; z-index: 9998;">
        <div class="spinner-wrapper">
            <div class="spinner-ring"></div>
            <img src="<?= $base_path ?>img/logo_DIF.png" alt="Cargando..." class="spinner-logo">
        </div>
    </div>
    <!-- Spinner End -->
    <script>
    // Ocultar spinner cuando la pagina cargue (sin depender de jQuery)
    window.addEventListener('load', function() {
        var sp = document.getElementById('spinner');
        if (sp) { sp.classList.remove('show'); sp.style.display = 'none'; }
    });
    // Fallback: ocultar despues de 1 segundo si load no dispara (REDUCIDO)
    setTimeout(function() {
        var sp = document.getElementById('spinner');
        if (sp) { sp.classList.remove('show'); sp.style.display = 'none'; }
    }, 1000);
    </script>

    <!-- Logo Principal Start -->
    <div class="container d-flex justify-content-center pt-4 pb-0">
        <img src="<?= $base_path ?>img/logos.png" class="img-fluid" style="max-width:70%;" alt="DIF San Mateo Atenco">
    </div>
    <!-- Logo Principal End -->

    <!-- Logo secundario Start -->
    <div class="container pb-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 header-secondary">
            <!-- Widget del clima izquierda -->
            <div id="weather-widget" class="header-weather" style="min-width:240px; max-width:320px; font-family:'Montserrat',sans-serif; font-size:13px; text-align:center; color:rgba(0,0,0,0.8);">
                <div class="d-flex flex-column align-items-center gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <span id="weather-icon" style="font-size:28px;">⏳</span>
                        <div id="weather-temp" style="font-weight:600; font-size:16px; color:rgb(107,98,90);">--°C</div>
                    </div>
                    <div id="weather-desc" style="color:rgba(0,0,0,0.8); font-size:12px;">Cargando...</div>
                    <div style="color:rgb(107,98,90); font-size:12px; font-family:'Montserrat',sans-serif; font-weight:700;">San Mateo Atenco</div>
                </div>
            </div>
            <!-- Logo centrado -->
            <div class="d-flex justify-content-center header-unidos" style="flex:1;">
                <img src="<?= $base_path ?>img/UNIDOS.png" class="img-fluid" style="max-width:420px;width:100%;" alt="Unidos con Amor">
            </div>
            <!-- Buscador derecha -->
            <form class="d-flex align-items-center ms-auto header-search" role="search" onsubmit="doSearch(event)" style="min-width:220px; max-width:320px;">
                <div class="input-group">
                    <input id="siteSearchInput" type="search" class="form-control form-control-sm" placeholder="Buscar en la página..." aria-label="Buscar">
                    <button class="btn btn-sm btn-dark" type="submit" aria-label="Buscar">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <style>
    @media (max-width: 767px) {
        .header-secondary {
            flex-direction: column !important;
            align-items: center !important;
        }
        .header-unidos { order: 1 !important; flex: none !important; width: 100%; margin-bottom: 8px; }
        .header-unidos img { max-width: 300px !important; }
        .header-weather { order: 2 !important; min-width: auto !important; margin-bottom: 8px; }
        .header-search { order: 3 !important; min-width: 100% !important; max-width: 100% !important; margin: 0 !important; }
    }
    @media (min-width: 768px) and (max-width: 991px) {
        .header-unidos img { max-width: 280px !important; min-width: 200px !important; }
    }
    </style>
    <!-- Logo secundario End -->
