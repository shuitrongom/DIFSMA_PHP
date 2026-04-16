<?php
/**
 * admin/documentacion.php — Visor de documentación del sistema
 */
require_once __DIR__ . '/auth_guard.php';

$doc = $_GET['doc'] ?? null;
$allowed_docs = [
    'manual'  => ['title' => 'Manual de Usuario',  'file' => '../docs/Manual_Usuario_DIF.html'],
    'tecnico' => ['title' => 'Documento Técnico',  'file' => '../docs/Documento_Tecnico_DIF.html'],
];

// ── Descarga PDF pre-generado ──────────────────────────────────────────────────
if ($doc && isset($allowed_docs[$doc]) && isset($_GET['download'])) {
    $pdfs = [
        'manual'  => __DIR__ . '/../docs/pdf/Manual de Usuario — Sistema CMS DIF San Mateo Atenco.pdf',
        'tecnico' => __DIR__ . '/../docs/pdf/Documento Técnico  DIF San Mateo Atenco CMS.pdf',
    ];
    $pdfPath = $pdfs[$doc];
    if (file_exists($pdfPath)) {
        $filename = ($doc === 'manual') ? 'Manual_Usuario_DIF.pdf' : 'Documento_Tecnico_DIF.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: no-cache');
        readfile($pdfPath);
        exit;
    } else {
        // PDF no disponible aún
        $_SESSION['flash_message'] = 'El PDF aún no está disponible. Por favor contacte al administrador.';
        $_SESSION['flash_type'] = 'warning';
        header('Location: documentacion');
        exit;
    }
}

// ── Ver documento HTML ─────────────────────────────────────────────────────────
if ($doc && isset($allowed_docs[$doc])) {
    $path = __DIR__ . '/' . $allowed_docs[$doc]['file'];
    if (file_exists($path)) {
        header('Content-Type: text/html; charset=UTF-8');
        $html = file_get_contents($path);
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
        }
        echo $html;
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
    <style>
        .doc-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: box-shadow .2s, transform .2s;
            background: #fff;
            text-decoration: none;
            color: #1a2332;
            display: block;
        }
        .doc-card:hover {
            box-shadow: 0 8px 32px rgba(200,16,44,0.13);
            transform: translateY(-4px);
            color: #1a2332;
        }
        .doc-card .doc-icon {
            font-size: 3.5rem;
            color: #C8102C;
            margin-bottom: 1rem;
        }
        .doc-card h5 { font-weight: 700; margin-bottom: .5rem; color: #1a2332; }
        .doc-card p  { font-size: .9rem; color: #6b7280; margin: 0; }
        .page-header {
            background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 40%, rgb(200,16,44) 100%);
            color: #ffffffff;
            border-radius: 14px;
            padding: 1.8rem 2rem;
            margin-bottom: 2rem;
        }
        .page-header h4 { font-weight: 700; margin: 0; color: #fff !important; }
    </style>
</head>
<body>
<div class="d-flex">
    <?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

    <div class="main-content">
        <nav class="navbar navbar-dark px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                <i class="bi bi-list"></i>
            </button>
            <span class="navbar-brand mb-0 h6">Documentación</span>
            <a href="logout" class="btn btn-sm btn-outline-danger ms-auto">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
        </nav>

        <div class="container-fluid p-4">
            <div class="page-header">
                <h4><i class="bi bi-book me-2"></i>Documentación del Sistema</h4>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-sm-6 col-md-4">
                    <a href="documentacion?doc=manual" target="_blank" class="doc-card">
                        <div class="doc-icon"><i class="bi bi-journal-text"></i></div>
                        <h5>Manual de Usuario</h5>
                        <p>Guía paso a paso para el uso del panel de administración</p>
                    </a>
                    <a href="documentacion?doc=manual&download=1" target="_blank" class="btn btn-outline-danger w-100 mt-2 no-pdf-viewer" onclick="window.open(this.href,'_blank');return false;">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
                    </a>
                </div>
                <div class="col-sm-6 col-md-4">
                    <a href="documentacion?doc=tecnico" target="_blank" class="doc-card">
                        <div class="doc-icon"><i class="bi bi-file-earmark-code"></i></div>
                        <h5>Documento Técnico</h5>
                        <p>Arquitectura, base de datos, seguridad y especificaciones del sistema</p>
                    </a>
                    <a href="documentacion?doc=tecnico&download=1" target="_blank" class="btn btn-outline-danger w-100 mt-2 no-pdf-viewer" onclick="window.open(this.href,'_blank');return false;">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
                    </a>
                </div>
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
    if (closeBtn) closeBtn.addEventListener('click', function () { sidebar.classList.add('collapsed'); });
</script>
</body>
</html>
