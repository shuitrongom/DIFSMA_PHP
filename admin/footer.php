<?php
/**
 * admin/footer.php — Gestión de configuración del Footer
 *
 * Requisitos: 14.1, 14.2
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Campos editables del footer ────────────────────────────────────────────────
$fields = [
    'texto_inst'    => ['label' => 'Texto institucional',   'type' => 'textarea'],
    'horario'       => ['label' => 'Horario',               'type' => 'text'],
    'direccion'     => ['label' => 'Dirección',             'type' => 'textarea'],
    'telefono'      => ['label' => 'Teléfono',              'type' => 'text'],
    'email'         => ['label' => 'Correo electrónico',    'type' => 'email'],
    'url_facebook'  => ['label' => 'URL de Facebook',       'type' => 'url'],
    'url_twitter'   => ['label' => 'URL de Twitter / X',    'type' => 'url'],
    'url_instagram' => ['label' => 'URL de Instagram',      'type' => 'url'],
];

$urlFields = ['url_facebook', 'url_twitter', 'url_instagram'];
$maxLength = 500;

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido. Intente de nuevo.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: footer.php');
        exit;
    }

    $data   = [];
    $errors = [];

    foreach ($fields as $key => $meta) {
        $value = trim($_POST[$key] ?? '');

        // Validar longitud máxima
        if (mb_strlen($value) > $maxLength) {
            $errors[] = $meta['label'] . ' no debe exceder ' . $maxLength . ' caracteres.';
        }

        // Validar URLs
        if (in_array($key, $urlFields) && $value !== '') {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $errors[] = $meta['label'] . ' no es una URL válida.';
            }
        }

        // Validar email
        if ($key === 'email' && $value !== '') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El correo electrónico no es válido.';
            }
        }

        $data[$key] = $value;
    }

    if (!empty($errors)) {
        $_SESSION['flash_message'] = implode('<br>', $errors);
        $_SESSION['flash_type']    = 'warning';
        $_SESSION['footer_form_data'] = $data;
        header('Location: footer.php');
        exit;
    }

    try {
        // Verificar si existe registro id=1
        $stmt = $pdo->prepare('SELECT id FROM footer_config WHERE id = 1 LIMIT 1');
        $stmt->execute();
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $pdo->prepare(
                'UPDATE footer_config SET texto_inst = ?, horario = ?, direccion = ?, telefono = ?, email = ?, url_facebook = ?, url_twitter = ?, url_instagram = ? WHERE id = 1'
            );
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO footer_config (texto_inst, horario, direccion, telefono, email, url_facebook, url_twitter, url_instagram) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
        }

        $stmt->execute([
            $data['texto_inst'],
            $data['horario'],
            $data['direccion'],
            $data['telefono'],
            $data['email'],
            $data['url_facebook'],
            $data['url_twitter'],
            $data['url_instagram'],
        ]);

        $_SESSION['flash_message'] = 'Configuración del footer actualizada correctamente.';
        $_SESSION['flash_type']    = 'success';
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar en la base de datos.';
        $_SESSION['flash_type']    = 'danger';
    }

    unset($_SESSION['footer_form_data']);
    header('Location: footer.php');
    exit;
}

// ── Consultar registro actual ──────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT * FROM footer_config WHERE id = 1 LIMIT 1');
$stmt->execute();
$footerData = $stmt->fetch();

// Si hay datos de formulario guardados por error de validación, usarlos
$formData = $_SESSION['footer_form_data'] ?? null;
unset($_SESSION['footer_form_data']);

// ── Flash messages ─────────────────────────────────────────────────────────────
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Generar token CSRF
$token = csrf_token();

// Secciones del sidebar
$sections = [
    ['title' => 'Slider Principal',   'file' => 'slider_principal.php', 'icon' => 'bi-images'],
    ['title' => 'Slider DIF Comunica','file' => 'slider_comunica.php',  'icon' => 'bi-megaphone'],
    ['title' => 'Noticias',           'file' => 'noticias.php',         'icon' => 'bi-newspaper'],
    ['title' => 'Presidencia',        'file' => 'presidencia.php',      'icon' => 'bi-person-badge'],
    ['title' => 'Direcciones',        'file' => 'direcciones.php',      'icon' => 'bi-people'],
    ['title' => 'Organigrama',        'file' => 'organigrama.php',      'icon' => 'bi-diagram-3'],
    ['title' => 'Trámites',           'file' => 'tramites.php',         'icon' => 'bi-file-earmark-text'],
    ['title' => 'Galería',            'file' => 'galeria.php',          'icon' => 'bi-camera'],
    ['title' => 'SEAC',               'file' => 'seac.php',             'icon' => 'bi-file-earmark-pdf'],
    ['title' => 'Cuenta Pública',     'file' => 'cuenta_publica.php',   'icon' => 'bi-cash-stack'],
    ['title' => 'Presupuesto Anual',  'file' => 'presupuesto_anual.php', 'icon' => 'bi-wallet2'],
    ['title' => 'PAE',               'file' => 'pae.php',              'icon' => 'bi-clipboard-data'],
    ['title' => 'Matrices',          'file' => 'matrices_indicadores.php', 'icon' => 'bi-bar-chart-line'],
    ['title' => 'CONAC',             'file' => 'conac.php',             'icon' => 'bi-bank'],
    ['title' => 'Financiero',        'file' => 'financiero.php',       'icon' => 'bi-currency-dollar'],
    ['title' => 'Avisos Privacidad', 'file' => 'avisos_privacidad.php','icon' => 'bi-shield-exclamation'],
    ['title' => 'Programas',          'file' => 'programas.php',        'icon' => 'bi-grid-3x3-gap'],
    ['title' => 'Transparencia',      'file' => 'transparencia.php',    'icon' => 'bi-shield-check'],
    ['title' => 'Imagen Institucional','file' => 'institucion.php',     'icon' => 'bi-card-image'],
    ['title' => 'Footer',             'file' => 'footer.php',           'icon' => 'bi-layout-text-window-reverse'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar d-flex flex-column">
            <div class="sidebar-header d-flex align-items-center justify-content-between">
                <a href="dashboard.php" class="text-white text-decoration-none">
                    <img src="../img/escudo.png" alt="DIF" style="height:28px;margin-right:6px;vertical-align:middle;"> Admin DIF
                </a>
                <button class="btn btn-sm btn-outline-light d-md-none" id="closeSidebar" aria-label="Cerrar menú">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <ul class="nav flex-column mt-2">
                <?php foreach ($sections as $s): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $s['file'] === 'footer.php' ? ' active' : '' ?>" href="<?= htmlspecialchars($s['file']) ?>">
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
                <span class="navbar-brand mb-0 h6">Configuración del Footer</span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </nav>

            <div class="container-fluid p-4">
                <!-- Flash message -->
                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                        <?= $flashMessage ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <!-- Vista previa actual -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-eye me-1"></i> Vista previa actual
                            </div>
                            <div class="card-body">
                                <?php if ($footerData): ?>
                                    <dl class="mb-0">
                                        <?php foreach ($fields as $key => $meta): ?>
                                            <dt class="small text-muted"><?= htmlspecialchars($meta['label']) ?></dt>
                                            <dd class="mb-2">
                                                <?php
                                                    $val = $footerData[$key] ?? '';
                                                    if ($val === '') {
                                                        echo '<span class="text-muted fst-italic">— vacío —</span>';
                                                    } elseif (in_array($key, $urlFields)) {
                                                        echo '<a href="' . htmlspecialchars($val) . '" target="_blank" class="text-break">' . htmlspecialchars($val) . '</a>';
                                                    } else {
                                                        echo nl2br(htmlspecialchars($val));
                                                    }
                                                ?>
                                            </dd>
                                        <?php endforeach; ?>
                                    </dl>
                                    <?php if (!empty($footerData['updated_at'])): ?>
                                        <hr>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Última actualización: <?= htmlspecialchars($footerData['updated_at']) ?>
                                        </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-muted py-4 text-center">
                                        <i class="bi bi-layout-text-window-reverse" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay configuración de footer registrada. Use el formulario para agregar.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de edición -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-pencil-square me-1"></i>
                                <?= $footerData ? 'Editar configuración del footer' : 'Registrar configuración del footer' ?>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="footer.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">

                                    <?php foreach ($fields as $key => $meta): ?>
                                        <?php
                                            // Prioridad: datos de formulario (error de validación) > datos de DB > vacío
                                            $currentVal = '';
                                            if ($formData !== null && isset($formData[$key])) {
                                                $currentVal = $formData[$key];
                                            } elseif ($footerData && isset($footerData[$key])) {
                                                $currentVal = $footerData[$key] ?? '';
                                            }
                                        ?>
                                        <div class="mb-3">
                                            <label for="<?= $key ?>" class="form-label">
                                                <?= htmlspecialchars($meta['label']) ?>
                                                <small class="text-muted">(máx. <?= $maxLength ?> caracteres)</small>
                                            </label>
                                            <?php if ($meta['type'] === 'textarea'): ?>
                                                <textarea class="form-control" id="<?= $key ?>" name="<?= $key ?>"
                                                          rows="3" maxlength="<?= $maxLength ?>"
                                                          placeholder="<?= htmlspecialchars($meta['label']) ?>"><?= htmlspecialchars($currentVal) ?></textarea>
                                            <?php else: ?>
                                                <input type="<?= $meta['type'] === 'url' ? 'url' : ($meta['type'] === 'email' ? 'email' : 'text') ?>"
                                                       class="form-control" id="<?= $key ?>" name="<?= $key ?>"
                                                       value="<?= htmlspecialchars($currentVal) ?>"
                                                       maxlength="<?= $maxLength ?>"
                                                       placeholder="<?= htmlspecialchars($meta['label']) ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save me-1"></i>
                                        <?= $footerData ? 'Guardar cambios' : 'Registrar' ?>
                                    </button>
                                </form>
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
