<?php
/**
 * admin/transparencia_dinamica.php — Gestión de secciones dinámicas de Transparencia
 * Permite crear nuevas secciones eligiendo una plantilla (SEAC, Cuenta Pública, etc.)
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

$plantillas = [
    'seac'              => ['nombre' => 'SEAC',              'desc' => 'Bloques por año → Conceptos → PDFs trimestrales (Q1-Q4)', 'icon' => 'bi-file-earmark-pdf'],
    'cuenta_publica'    => ['nombre' => 'Cuenta Pública',    'desc' => 'Bloques por año → Títulos/Módulos → Conceptos con PDF',   'icon' => 'bi-cash-stack'],
    'presupuesto_anual' => ['nombre' => 'Presupuesto Anual', 'desc' => 'Bloques por año → Conceptos → Sub-años con PDF',          'icon' => 'bi-wallet2'],
    'pae'               => ['nombre' => 'PAE',               'desc' => 'Títulos globales → PDFs por año',                          'icon' => 'bi-clipboard-data'],
    'matrices'          => ['nombre' => 'Matrices',          'desc' => 'Años con PDF directo (estructura más simple)',              'icon' => 'bi-bar-chart-line'],
    'conac'             => ['nombre' => 'CONAC',             'desc' => 'Bloques por año → Conceptos → PDFs trimestrales (Q1-Q4)', 'icon' => 'bi-bank'],
    'financiero'        => ['nombre' => 'Financiero',        'desc' => 'Bloques por año → Conceptos con PDF directo',              'icon' => 'bi-currency-dollar'],
];

// ── POST ────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';
    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.'; $_SESSION['flash_type'] = 'danger';
        header('Location: transparencia_dinamica.php'); exit;
    }

    if ($action === 'create') {
        $nombre    = trim($_POST['nombre'] ?? '');
        $plantilla = $_POST['plantilla'] ?? '';
        if (empty($nombre) || !isset($plantillas[$plantilla])) {
            $_SESSION['flash_message'] = 'Nombre y plantilla son obligatorios.'; $_SESSION['flash_type'] = 'warning';
            header('Location: transparencia_dinamica.php'); exit;
        }
        $slug = preg_replace('/[^a-z0-9]+/', '_', strtolower(trim(preg_replace('/[áéíóúñ]/u', '', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombre)))));
        $slug = trim($slug, '_');
        if (empty($slug)) $slug = 'seccion_' . time();
        // Verificar slug único
        $s = $pdo->prepare('SELECT id FROM trans_secciones WHERE slug = ?'); $s->execute([$slug]);
        if ($s->fetch()) { $slug .= '_' . time(); }
        try {
            $s = $pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM trans_secciones'); $s->execute();
            $ord = (int) $s->fetchColumn();
            $pdo->prepare('INSERT INTO trans_secciones (nombre, slug, plantilla, icono, orden) VALUES (?,?,?,?,?)')
                ->execute([$nombre, $slug, $plantilla, $plantillas[$plantilla]['icon'], $ord]);
            $_SESSION['flash_message'] = "Sección \"{$nombre}\" creada con plantilla {$plantillas[$plantilla]['nombre']}.";
            $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al crear.';
            $_SESSION['flash_type'] = 'danger';
        }
        header('Location: transparencia_dinamica.php'); exit;
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        try {
            // Obtener todos los PDFs para eliminar archivos
            $stmt = $pdo->prepare('SELECT pdf_path FROM trans_pdfs WHERE seccion_id = ? AND pdf_path IS NOT NULL');
            $stmt->execute([$id]); $pdfs = $stmt->fetchAll();
            $stmt = $pdo->prepare('SELECT pdf_path FROM trans_conceptos WHERE seccion_id = ? AND pdf_path IS NOT NULL');
            $stmt->execute([$id]); $cpdfs = $stmt->fetchAll();
            $pdo->prepare('DELETE FROM trans_secciones WHERE id = ?')->execute([$id]);
            foreach (array_merge($pdfs, $cpdfs) as $p) {
                $f = BASE_PATH . '/' . $p['pdf_path'];
                if (file_exists($f)) unlink($f);
            }
            $_SESSION['flash_message'] = 'Sección eliminada.'; $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error.';
            $_SESSION['flash_type'] = 'danger';
        }
        header('Location: transparencia_dinamica.php'); exit;
    }

    if ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        $pdo->prepare('UPDATE trans_secciones SET activo = NOT activo WHERE id = ?')->execute([$id]);
        $_SESSION['flash_message'] = 'Estado actualizado.'; $_SESSION['flash_type'] = 'success';
        header('Location: transparencia_dinamica.php'); exit;
    }
}

// ── Consultar secciones ─────────────────────────────────────────────────────────
$secciones = $pdo->query('SELECT * FROM trans_secciones ORDER BY orden ASC')->fetchAll();

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transparencia Dinámica — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
</head>
<body>
<div class="d-flex">
    <?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
    <div class="main-content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
            <span class="navbar-brand mb-0 h6">Transparencia — Secciones Dinámicas</span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </nav>
        <div class="container-fluid p-4">
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show">
                <?= htmlspecialchars($flashMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Crear sección -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header bg-danger text-white"><i class="bi bi-plus-circle me-1"></i> Crear nueva seccion</div>
                        <div class="card-body">
                            <form method="POST" action="transparencia_dinamica.php">
                                <input type="hidden" name="action" value="create">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nombre de la sección</label>
                                    <input type="text" class="form-control" name="nombre" required placeholder="Ej: Auditorías 2026">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Plantilla</label>
                                    <?php foreach ($plantillas as $key => $pl): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="plantilla" value="<?= $key ?>" id="pl_<?= $key ?>" <?= $key === 'seac' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="pl_<?= $key ?>">
                                            <i class="bi <?= $pl['icon'] ?> me-1"></i> <strong><?= $pl['nombre'] ?></strong>
                                            <br><small class="text-muted"><?= $pl['desc'] ?></small>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="submit" class="btn btn-danger w-100"><i class="bi bi-plus-circle me-1"></i> Crear seccion</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Listado -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header"><i class="bi bi-list-ul me-1"></i> Secciones creadas <span class="badge bg-secondary ms-1"><?= count($secciones) ?></span></div>
                        <div class="card-body p-0">
                            <?php if (empty($secciones)): ?>
                            <div class="text-center text-muted py-4"><i class="bi bi-folder-plus" style="font-size:2rem;"></i><p class="mt-2 mb-0">No hay secciones dinámicas. Cree una con el formulario.</p></div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light"><tr><th>Nombre</th><th>Plantilla</th><th>Estado</th><th>Acciones</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($secciones as $sec): ?>
                                    <tr>
                                        <td><i class="bi <?= htmlspecialchars($sec['icono']) ?> me-1"></i> <?= htmlspecialchars($sec['nombre']) ?></td>
                                        <td><span class="badge bg-danger"><?= htmlspecialchars($plantillas[$sec['plantilla']]['nombre'] ?? $sec['plantilla']) ?></span></td>
                                        <td>
                                            <form method="POST" action="transparencia_dinamica.php" class="d-inline">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?= (int)$sec['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                <button type="submit" class="btn btn-sm <?= $sec['activo'] ? 'btn-success' : 'btn-secondary' ?>">
                                                    <?= $sec['activo'] ? 'Activo' : 'Inactivo' ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="transparencia_seccion.php?id=<?= (int)$sec['id'] ?>" class="btn btn-sm btn-action-delete"><i class="bi bi-pencil"></i> Gestionar</a>
                                            <form method="POST" action="transparencia_dinamica.php" class="d-inline" onsubmit="return confirm('¿Eliminar esta sección y todo su contenido?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int)$sec['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                                <button type="submit" class="btn btn-sm btn-action-delete"><i class="bi bi-trash3"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
    document.getElementById('toggleSidebar').addEventListener('click', function() { sidebar.classList.toggle('collapsed'); });
    const closeBtn = document.getElementById('closeSidebar');
    if (closeBtn) closeBtn.addEventListener('click', function() { sidebar.classList.add('collapsed'); });
</script>
</body>
</html>


