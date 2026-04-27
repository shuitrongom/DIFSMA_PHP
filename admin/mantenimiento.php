<?php
/**
 * admin/mantenimiento.php — Gestión centralizada de Mantenimiento
 *
 * - Editar contenido de la página de mantenimiento
 * - Activar/desactivar mantenimiento por página con toggles
 * - Sincroniza automáticamente trámites, programas y secciones dinámicas de transparencia
 */

require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Asegurar que las tablas existan ────────────────────────────────────────────
try {
    $pdo->query('SELECT 1 FROM mantenimiento_config LIMIT 1');
} catch (PDOException $e) {
    $sql = file_get_contents(__DIR__ . '/../database/dif_cms_mantenimiento_config.sql');
    if ($sql) { $pdo->exec($sql); }
}

// ── Sincronizar páginas dinámicas (trámites, programas, secciones transparencia) ──
try {
    // Trámites
    $tramites = $pdo->query('SELECT slug, titulo FROM tramites ORDER BY id ASC')->fetchAll();
    foreach ($tramites as $t) {
        $key = 'tramite_' . $t['slug'];
        $nombre = 'Trámite: ' . $t['titulo'];
        $pdo->prepare('INSERT INTO mantenimiento_paginas (pagina_key, pagina_nombre, grupo, en_mantenimiento) VALUES (?,?,?,0) ON DUPLICATE KEY UPDATE pagina_nombre=VALUES(pagina_nombre), grupo=VALUES(grupo)')
            ->execute([$key, $nombre, 'Servicios']);
    }

    // Programas (secciones)
    $programas = $pdo->query('SELECT ps.slug, ps.titulo, p.nombre FROM programas_secciones ps JOIN programas p ON p.id = ps.programa_id WHERE ps.slug IS NOT NULL ORDER BY p.orden ASC, ps.orden ASC')->fetchAll();
    foreach ($programas as $pr) {
        $key = 'programa_' . $pr['slug'];
        $nombre = 'Programa: ' . $pr['titulo'];
        $pdo->prepare('INSERT INTO mantenimiento_paginas (pagina_key, pagina_nombre, grupo, en_mantenimiento) VALUES (?,?,?,0) ON DUPLICATE KEY UPDATE pagina_nombre=VALUES(pagina_nombre), grupo=VALUES(grupo)')
            ->execute([$key, $nombre, 'Programas']);
    }

    // Secciones dinámicas de transparencia
    $secciones = $pdo->query('SELECT slug, nombre FROM trans_secciones WHERE activo = 1 ORDER BY orden ASC')->fetchAll();
    foreach ($secciones as $s) {
        $key = 'trans_' . $s['slug'];
        $nombre = 'Transparencia: ' . $s['nombre'];
        $pdo->prepare('INSERT INTO mantenimiento_paginas (pagina_key, pagina_nombre, grupo, en_mantenimiento) VALUES (?,?,?,0) ON DUPLICATE KEY UPDATE pagina_nombre=VALUES(pagina_nombre), grupo=VALUES(grupo)')
            ->execute([$key, $nombre, 'Transparencia']);
    }
} catch (PDOException $e) {
    // Silenciar si alguna tabla no existe aún
}

// ── Procesamiento POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF inválido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: mantenimiento');
        exit;
    }

    if ($action === 'save_contenido') {
        $titulo       = trim($_POST['titulo'] ?? 'Sitio en Mantenimiento');
        $descripcion  = trim($_POST['descripcion'] ?? '');
        $correo       = trim($_POST['correo_contacto'] ?? '');
        $t1_titulo    = trim($_POST['tarjeta1_titulo'] ?? '');
        $t1_texto     = trim($_POST['tarjeta1_texto'] ?? '');
        $t2_titulo    = trim($_POST['tarjeta2_titulo'] ?? '');
        $t2_texto     = trim($_POST['tarjeta2_texto'] ?? '');
        $t3_titulo    = trim($_POST['tarjeta3_titulo'] ?? '');
        $t3_texto     = trim($_POST['tarjeta3_texto'] ?? '');

        try {
            $stmt = $pdo->query('SELECT id FROM mantenimiento_config LIMIT 1');
            $current = $stmt->fetch();
            if ($current) {
                $pdo->prepare('UPDATE mantenimiento_config SET titulo=?, descripcion=?, correo_contacto=?, tarjeta1_titulo=?, tarjeta1_texto=?, tarjeta2_titulo=?, tarjeta2_texto=?, tarjeta3_titulo=?, tarjeta3_texto=?, updated_at=NOW() WHERE id=?')
                    ->execute([$titulo, $descripcion, $correo, $t1_titulo, $t1_texto, $t2_titulo, $t2_texto, $t3_titulo, $t3_texto, $current['id']]);
            } else {
                $pdo->prepare('INSERT INTO mantenimiento_config (titulo, descripcion, correo_contacto, tarjeta1_titulo, tarjeta1_texto, tarjeta2_titulo, tarjeta2_texto, tarjeta3_titulo, tarjeta3_texto) VALUES (?,?,?,?,?,?,?,?,?)')
                    ->execute([$titulo, $descripcion, $correo, $t1_titulo, $t1_texto, $t2_titulo, $t2_texto, $t3_titulo, $t3_texto]);
            }
            $_SESSION['flash_message'] = 'Contenido de mantenimiento actualizado.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = 'Error al guardar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: mantenimiento');
        exit;
    }

    if ($action === 'save_paginas') {
        $paginas_activas = $_POST['paginas'] ?? [];
        try {
            $pdo->exec('UPDATE mantenimiento_paginas SET en_mantenimiento = 0');
            if (!empty($paginas_activas)) {
                $placeholders = implode(',', array_fill(0, count($paginas_activas), '?'));
                $pdo->prepare("UPDATE mantenimiento_paginas SET en_mantenimiento = 1 WHERE pagina_key IN ($placeholders)")
                    ->execute($paginas_activas);
            }
            $_SESSION['flash_message'] = 'Estado de mantenimiento actualizado.';
            $_SESSION['flash_type']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = 'Error al guardar.';
            $_SESSION['flash_type']    = 'danger';
        }
        header('Location: mantenimiento');
        exit;
    }
}

// ── Consultar datos ────────────────────────────────────────────────────────────
$config = [];
try {
    $stmt = $pdo->query('SELECT * FROM mantenimiento_config LIMIT 1');
    $config = $stmt->fetch() ?: [];
} catch (PDOException $e) {}

$paginas = [];
try {
    $stmt = $pdo->query('SELECT * FROM mantenimiento_paginas ORDER BY grupo ASC, id ASC');
    $paginas = $stmt->fetchAll();
} catch (PDOException $e) {}

// Agrupar páginas por grupo
$grupos = [];
foreach ($paginas as $p) {
    $g = $p['grupo'] ?? 'Otros';
    $grupos[$g][] = $p;
}

// Orden de grupos como el menú
$grupo_orden = ['Inicio', 'Acerca del DIF', 'Servicios', 'Programas', 'Comunicación Social', 'Voluntariado', 'Transparencia', 'Otros'];
$grupos_ordenados = [];
foreach ($grupo_orden as $go) {
    if (isset($grupos[$go])) {
        $grupos_ordenados[$go] = $grupos[$go];
    }
}
// Agregar grupos que no estén en el orden
foreach ($grupos as $g => $items) {
    if (!isset($grupos_ordenados[$g])) {
        $grupos_ordenados[$g] = $items;
    }
}

$activas = 0;
foreach ($paginas as $p) {
    if ($p['en_mantenimiento'] == 1) $activas++;
}

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();

// Iconos por grupo
$grupo_iconos = [
    'Inicio' => 'bi-house-door',
    'Acerca del DIF' => 'bi-info-circle',
    'Servicios' => 'bi-file-earmark-text',
    'Programas' => 'bi-grid-3x3-gap',
    'Comunicación Social' => 'bi-megaphone',
    'Voluntariado' => 'bi-heart',
    'Transparencia' => 'bi-shield-check',
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
    <style>
        /* Estilos personalizados para el acordeón de mantenimiento */
        .accordion-btn-custom {
            background: rgb(107,98,90) !important;
            color: #fff !important;
            padding: 0.75rem 1rem;
            font-weight: 500;
        }
        .accordion-btn-custom:not(.collapsed) {
            background: rgb(107,98,90) !important;
            color: #fff !important;
            box-shadow: none;
        }
        .accordion-btn-custom.collapsed {
            background: rgb(107,98,90) !important;
            color: #fff !important;
        }
        .accordion-btn-custom:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }
        .accordion-btn-custom::after {
            filter: brightness(0) invert(1);
        }
        .accordion-btn-custom:hover {
            background: rgb(97,88,80) !important;
            color: #fff !important;
        }
        .accordion-item {
            border: 1px solid rgba(0,0,0,.125);
            border-radius: 0.375rem !important;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php require_once __DIR__ . '/sidebar_sections.php';
require_once __DIR__ . '/page_help.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

        <div class="main-content">
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h6"><i class="bi bi-tools me-1"></i> Gestión de Mantenimiento</span>
                <a href="logout" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </nav>

            <div class="container-fluid p-4">
                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($flashMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <!-- Resumen -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-tools text-danger" style="font-size:2rem;"></i>
                                <h5 class="mt-2 mb-0"><?= $activas ?> / <?= count($paginas) ?></h5>
                                <small class="text-muted">Páginas en mantenimiento</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle text-success" style="font-size:2rem;"></i>
                                <h5 class="mt-2 mb-0"><?= count($paginas) - $activas ?></h5>
                                <small class="text-muted">Páginas activas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <a href="../mantenimiento" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-eye me-1"></i> Ver página de mantenimiento
                                </a>
                                <br><small class="text-muted mt-1 d-block">Vista previa en nueva pestaña</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- ═══ COLUMNA IZQUIERDA: Toggles por grupo ═══ -->
                    <div class="col-lg-5">
                        <form method="POST" action="mantenimiento">
                            <input type="hidden" name="action" value="save_paginas">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">

                            <div class="accordion" id="accordionMantenimiento">
                                <?php 
                                $index = 0;
                                foreach ($grupos_ordenados as $grupo => $items): 
                                    $collapseId = 'collapse' . $index;
                                    $isFirst = ($index === 0);
                                ?>
                                <div class="accordion-item mb-2 shadow-sm">
                                    <h2 class="accordion-header" id="heading<?= $index ?>">
                                        <button class="accordion-button accordion-btn-custom <?= !$isFirst ? 'collapsed' : '' ?>" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" 
                                                aria-expanded="<?= $isFirst ? 'true' : 'false' ?>" 
                                                aria-controls="<?= $collapseId ?>">
                                            <i class="bi <?= $grupo_iconos[$grupo] ?? 'bi-folder' ?> me-2"></i>
                                            <?= htmlspecialchars($grupo) ?>
                                            <span class="badge bg-light text-dark ms-2"><?= count($items) ?></span>
                                        </button>
                                    </h2>
                                    <div id="<?= $collapseId ?>" 
                                         class="accordion-collapse collapse <?= $isFirst ? 'show' : '' ?>" 
                                         aria-labelledby="heading<?= $index ?>" 
                                         data-bs-parent="#accordionMantenimiento">
                                        <div class="accordion-body py-2 px-3">
                                            <?php foreach ($items as $p): ?>
                                            <div class="d-flex align-items-center justify-content-between py-2 <?= $p !== end($items) ? 'border-bottom' : '' ?>">
                                                <span style="font-size:0.85rem;"><?= htmlspecialchars($p['pagina_nombre']) ?></span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="paginas[]"
                                                           value="<?= htmlspecialchars($p['pagina_key']) ?>"
                                                           style="width:2.2rem;height:1.1rem;cursor:pointer;"
                                                           <?= $p['en_mantenimiento'] == 1 ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                $index++;
                                endforeach; 
                                ?>
                            </div>

                            <button type="submit" class="btn btn-danger w-100 mt-3 mb-3">
                                <i class="bi bi-save me-1"></i> Guardar estado de páginas
                            </button>
                        </form>
                    </div>

                    <!-- ═══ COLUMNA DERECHA: Editar contenido ═══ -->
                    <div class="col-lg-7">
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <i class="bi bi-pencil-square me-1"></i> Contenido de la Página de Mantenimiento
                            </div>
                            <div class="card-body">
                                <form method="POST" action="mantenimiento">
                                    <input type="hidden" name="action" value="save_contenido">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">

                                    <div class="mb-3">
                                        <label for="titulo" class="form-label fw-semibold">Título principal</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo"
                                               value="<?= htmlspecialchars($config['titulo'] ?? 'Sitio en Mantenimiento') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($config['descripcion'] ?? '') ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="correo_contacto" class="form-label fw-semibold">Correo de contacto</label>
                                        <input type="email" class="form-control" id="correo_contacto" name="correo_contacto"
                                               value="<?= htmlspecialchars($config['correo_contacto'] ?? 'presidencia@difsanmateoatenco.gob.mx') ?>">
                                    </div>

                                    <hr>
                                    <h6 class="fw-bold mb-3"><i class="bi bi-card-text me-1"></i> Tarjetas informativas</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Tarjeta 1 — Título</label>
                                            <input type="text" class="form-control form-control-sm" name="tarjeta1_titulo"
                                                   value="<?= htmlspecialchars($config['tarjeta1_titulo'] ?? 'Tiempo estimado') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Tarjeta 1 — Texto</label>
                                            <input type="text" class="form-control form-control-sm" name="tarjeta1_texto"
                                                   value="<?= htmlspecialchars($config['tarjeta1_texto'] ?? 'Breve interrupción') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Tarjeta 2 — Título</label>
                                            <input type="text" class="form-control form-control-sm" name="tarjeta2_titulo"
                                                   value="<?= htmlspecialchars($config['tarjeta2_titulo'] ?? 'Mejoras de seguridad') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Tarjeta 2 — Texto</label>
                                            <input type="text" class="form-control form-control-sm" name="tarjeta2_texto"
                                                   value="<?= htmlspecialchars($config['tarjeta2_texto'] ?? 'Actualizaciones del sistema') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Tarjeta 3 — Título</label>
                                            <input type="text" class="form-control form-control-sm" name="tarjeta3_titulo"
                                                   value="<?= htmlspecialchars($config['tarjeta3_titulo'] ?? 'Nuevas funciones') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Tarjeta 3 — Texto</label>
                                            <input type="text" class="form-control form-control-sm" name="tarjeta3_texto"
                                                   value="<?= htmlspecialchars($config['tarjeta3_texto'] ?? 'Próximamente disponibles') ?>">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-warning w-100 mt-3">
                                        <i class="bi bi-save me-1"></i> Guardar contenido
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
