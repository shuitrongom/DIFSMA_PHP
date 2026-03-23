<?php
/**
 * admin/dashboard.php — Panel de administración: dashboard principal
 *
 * Requirements: 1.2
 */

require_once __DIR__ . '/auth_guard.php';

// Definir las 12 secciones administrables
$sections = [
    [
        'title' => 'Slider Principal',
        'file'  => 'slider_principal.php',
        'icon'  => 'bi-images',
        'desc'  => 'Gestionar imágenes del carrusel principal del index.',
    ],
    [
        'title' => 'Slider DIF Comunica',
        'file'  => 'slider_comunica.php',
        'icon'  => 'bi-megaphone',
        'desc'  => 'Gestionar imágenes de la sección DIF Comunica.',
    ],
    [
        'title' => 'Noticias',
        'file'  => 'noticias.php',
        'icon'  => 'bi-newspaper',
        'desc'  => 'Gestionar imágenes de noticias por día.',
    ],
    [
        'title' => 'Presidencia',
        'file'  => 'presidencia.php',
        'icon'  => 'bi-person-badge',
        'desc'  => 'Gestionar imagen y datos de presidencia.',
    ],
    [
        'title' => 'Direcciones',
        'file'  => 'direcciones.php',
        'icon'  => 'bi-people',
        'desc'  => 'Gestionar imágenes y datos de direcciones por departamento.',
    ],
    [
        'title' => 'Organigrama',
        'file'  => 'organigrama.php',
        'icon'  => 'bi-diagram-3',
        'desc'  => 'Subir o reemplazar el PDF del organigrama.',
    ],
    [
        'title' => 'Trámites',
        'file'  => 'tramites.php',
        'icon'  => 'bi-file-earmark-text',
        'desc'  => 'Gestionar imagen y contenido de trámites y servicios.',
    ],
    [
        'title' => 'Galería',
        'file'  => 'galeria.php',
        'icon'  => 'bi-camera',
        'desc'  => 'Gestionar álbumes e imágenes de la galería fotográfica.',
    ],
    [
        'title' => 'SEAC',
        'file'  => 'seac.php',
        'icon'  => 'bi-file-earmark-pdf',
        'desc'  => 'Gestionar bloques SEAC por año, trimestre y PDFs.',
    ],
    [
        'title' => 'Cuenta Pública',
        'file'  => 'cuenta_publica.php',
        'icon'  => 'bi-cash-stack',
        'desc'  => 'Gestionar bloques de Cuenta Pública por año, trimestre y PDFs.',
    ],
    [
        'title' => 'Presupuesto Anual',
        'file'  => 'presupuesto_anual.php',
        'icon'  => 'bi-wallet2',
        'desc'  => 'Gestionar bloques de Presupuesto Anual por año, conceptos y PDFs.',
    ],
    [
        'title' => 'PAE',
        'file'  => 'pae.php',
        'icon'  => 'bi-clipboard-data',
        'desc'  => 'Gestionar PDFs del Programa Anual de Evaluación por título y año.',
    ],
    [
        'title' => 'Matrices',
        'file'  => 'matrices_indicadores.php',
        'icon'  => 'bi-bar-chart-line',
        'desc'  => 'Gestionar PDFs de Matrices de Indicadores por año.',
    ],
    [
        'title' => 'CONAC',
        'file'  => 'conac.php',
        'icon'  => 'bi-bank',
        'desc'  => 'Gestionar bloques CONAC por año, trimestre y PDFs.',
    ],
    [
        'title' => 'Financiero',
        'file'  => 'financiero.php',
        'icon'  => 'bi-currency-dollar',
        'desc'  => 'Gestionar bloques Financiero por año con conceptos y PDFs.',
    ],
    [
        'title' => 'Avisos Privacidad',
        'file'  => 'avisos_privacidad.php',
        'icon'  => 'bi-shield-exclamation',
        'desc'  => 'Gestionar texto y botones de Avisos de Privacidad con PDFs.',
    ],
    [
        'title' => 'Programas',
        'file'  => 'programas.php',
        'icon'  => 'bi-grid-3x3-gap',
        'desc'  => 'Gestionar tarjetas de "Nuestros Programas".',
    ],
    [
        'title' => 'Transparencia',
        'file'  => 'transparencia.php',
        'icon'  => 'bi-shield-check',
        'desc'  => 'Gestionar enlaces de la sección Transparencia.',
    ],
    [
        'title' => 'Imagen Institucional',
        'file'  => 'institucion.php',
        'icon'  => 'bi-card-image',
        'desc'  => 'Cambiar la imagen institucional de la página principal.',
    ],
    [
        'title' => 'Footer',
        'file'  => 'footer.php',
        'icon'  => 'bi-layout-text-window-reverse',
        'desc'  => 'Editar textos, contacto y redes sociales del footer.',
    ],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .dashboard-welcome {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #fff;
            border-radius: 14px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .dashboard-welcome h4 { font-weight: 700; margin-bottom: 0.3rem; }
        .dashboard-welcome p { opacity: 0.8; margin-bottom: 0; font-size: 0.95rem; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar d-flex flex-column">
            <div class="sidebar-header d-flex align-items-center justify-content-between">
                <span><img src="../img/escudo.png" alt="DIF" style="height:28px;margin-right:6px;vertical-align:middle;"> Admin DIF</span>
                <button class="btn btn-sm btn-outline-light d-md-none" id="closeSidebar" aria-label="Cerrar menú">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <ul class="nav flex-column mt-2">
                <?php foreach ($sections as $s): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars($s['file']) ?>">
                            <i class="bi <?= htmlspecialchars($s['icon']) ?>"></i>
                            <?= htmlspecialchars($s['title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="mt-auto p-3 border-top border-secondary">
                <a href="logout.php" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                </a>
            </div>
        </nav>

        <!-- Main content -->
        <div class="main-content">
            <!-- Top bar -->
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h6">Panel de Administración</span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </nav>

            <!-- Dashboard cards -->
            <div class="container-fluid p-4">
                <div class="dashboard-welcome">
                    <h4><i class="bi bi-speedometer2 me-2"></i>Panel de Administración</h4>
                    <p>Gestiona el contenido del sitio DIF San Mateo Atenco</p>
                </div>
                <div class="row g-4">
                    <?php foreach ($sections as $s): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="<?= htmlspecialchars($s['file']) ?>" class="text-decoration-none">
                                <div class="card card-section h-100 text-center p-3">
                                    <div class="card-body">
                                        <div class="card-icon">
                                            <i class="bi <?= htmlspecialchars($s['icon']) ?>"></i>
                                        </div>
                                        <h6 class="card-title"><?= htmlspecialchars($s['title']) ?></h6>
                                        <p class="card-text text-muted small"><?= htmlspecialchars($s['desc']) ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
        const closeBtn = document.getElementById('closeSidebar');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                sidebar.classList.add('collapsed');
            });
        }
    </script>
</body>
</html>
