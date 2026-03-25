<?php
/**
 * admin/dashboard.php — Panel de administración: dashboard principal
 *
 * Requirements: 1.2
 */

require_once __DIR__ . '/auth_guard.php';

// Definir las 12 secciones administrables
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
    <link rel="stylesheet" href="../css/admin.css?v=5">
    <style>
        .dashboard-welcome {
            background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 40%, rgb(200,16,44) 100%);
            color: #fff !important;
            border-radius: 14px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .dashboard-welcome h4 { font-weight: 700; margin-bottom: 0.3rem; color: #fff !important; }
        .dashboard-welcome p { margin-bottom: 0; font-size: 0.95rem; color: rgba(255,255,255,0.85) !important; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

        <!-- Main content -->
        <div class="main-content">
            <!-- Top bar -->
            <nav class="navbar navbar-dark px-3">
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
                    <?php foreach ($sidebar_groups as $group): ?>
                        <?php foreach ($group['items'] as $s): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="<?= htmlspecialchars($s['file']) ?>" class="text-decoration-none">
                                <div class="card card-section h-100 text-center p-3">
                                    <div class="card-body">
                                        <div class="card-icon">
                                            <i class="bi <?= htmlspecialchars($s['icon']) ?>"></i>
                                        </div>
                                        <h6 class="card-title"><?= htmlspecialchars($s['title']) ?></h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=5"></script>
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
