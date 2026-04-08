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
        header('Location: footer');
        exit;
    }

    $post_action = $_POST['action'] ?? 'config';

    // ── CRUD de Footer Links ───────────────────────────────────────────────────
    if ($post_action === 'link_create') {
        $titulo   = trim($_POST['link_titulo'] ?? '');
        $url      = trim($_POST['link_url'] ?? '#');
        $nueva_tab = isset($_POST['link_nueva_tab']) ? 1 : 0;
        $orden    = (int) ($_POST['link_orden'] ?? 0);

        if (empty($titulo)) {
            $_SESSION['flash_message'] = 'El título del enlace es obligatorio.';
            $_SESSION['flash_type']    = 'warning';
        } else {
            try {
                if ($orden <= 0) {
                    $stmt = $pdo->query('SELECT COALESCE(MAX(orden), 0) + 1 FROM footer_links');
                    $orden = (int) $stmt->fetchColumn();
                }
                $stmt = $pdo->prepare('INSERT INTO footer_links (titulo, url, nueva_tab, orden) VALUES (?, ?, ?, ?)');
                $stmt->execute([$titulo, $url, $nueva_tab, $orden]);
                $_SESSION['flash_message'] = 'Enlace creado correctamente.';
                $_SESSION['flash_type']    = 'success';
            } catch (PDOException $e) {
                $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al guardar.';
                $_SESSION['flash_type']    = 'danger';
            }
        }
        header('Location: footer');
        exit;
    }

    if ($post_action === 'link_edit') {
        $id       = (int) ($_POST['link_id'] ?? 0);
        $titulo   = trim($_POST['link_titulo'] ?? '');
        $url      = trim($_POST['link_url'] ?? '#');
        $nueva_tab = isset($_POST['link_nueva_tab']) ? 1 : 0;
        $orden    = (int) ($_POST['link_orden'] ?? 0);

        if ($id > 0 && !empty($titulo)) {
            try {
                $stmt = $pdo->prepare('UPDATE footer_links SET titulo = ?, url = ?, nueva_tab = ?, orden = ? WHERE id = ?');
                $stmt->execute([$titulo, $url, $nueva_tab, $orden, $id]);
                $_SESSION['flash_message'] = 'Enlace actualizado.';
                $_SESSION['flash_type']    = 'success';
            } catch (PDOException $e) {
                $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al actualizar.';
                $_SESSION['flash_type']    = 'danger';
            }
        }
        header('Location: footer');
        exit;
    }

    if ($post_action === 'link_delete') {
        $id = (int) ($_POST['link_id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare('DELETE FROM footer_links WHERE id = ?');
                $stmt->execute([$id]);
                $_SESSION['flash_message'] = 'Enlace eliminado.';
                $_SESSION['flash_type']    = 'success';
            } catch (PDOException $e) {
                $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al eliminar.';
                $_SESSION['flash_type']    = 'danger';
            }
        }
        header('Location: footer');
        exit;
    }

    // ── Config del footer (acción por defecto) ─────────────────────────────────
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
        header('Location: footer');
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
    header('Location: footer');
    exit;
}

// ── Consultar registro actual ──────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT * FROM footer_config WHERE id = 1 LIMIT 1');
$stmt->execute();
$footerData = $stmt->fetch();

// ── Consultar footer_links ─────────────────────────────────────────────────────
$footerLinks = [];
try {
    $stmt = $pdo->query('SELECT * FROM footer_links ORDER BY orden ASC, id ASC');
    $footerLinks = $stmt->fetchAll();
} catch (PDOException $e) {}

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
    <link rel="stylesheet" href="../css/admin.css?v=7">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/sidebar_sections.php';
require_once __DIR__ . '/page_help.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

        <!-- Main content -->
        <div class="main-content">
            <!-- Top bar -->
            <nav class="navbar navbar-light bg-white shadow-sm px-3">
                <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h6">Configuración del Footer</span>
                <a href="logout" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </nav>

            <div class="container-fluid p-4">
                <?php page_help('footer'); ?>
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
                                <form method="POST" action="footer">
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

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- ENLACES DE NAVEGACIÓN DEL FOOTER                           -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div class="row g-4 mt-2">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-link-45deg me-1"></i> Agregar enlace
                            </div>
                            <div class="card-body">
                                <form method="POST" action="footer">
                                    <input type="hidden" name="action" value="link_create">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Título</label>
                                        <input type="text" class="form-control" name="link_titulo" required placeholder="Ej: Inicio">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">URL de destino</label>
                                        <select class="form-select mb-2" onchange="if(this.value!=='__custom__'){document.getElementById('link_url_new').value=this.value;}else{document.getElementById('link_url_new').value='';}">
                                            <option value="__custom__">-- Escribir URL manualmente --</option>
                                            <optgroup label="Paginas del sitio">
                                                <option value="index">Inicio</option>
                                                <option value="acerca-del-dif/presidencia">Presidencia</option>
                                                <option value="acerca-del-dif/direcciones">Direcciones</option>
                                                <option value="acerca-del-dif/organigrama">Organigrama</option>
                                                <option value="comunicacion-social/noticias">Noticias</option>
                                                <option value="comunicacion-social/galeria">Galeria</option>
                                                <option value="voluntariado">Voluntariado</option>
                                            </optgroup>
                                            <optgroup label="Transparencia">
                                                <option value="transparencia/SEAC">SEAC</option>
                                                <option value="transparencia/cuenta_publica">Cuenta Publica</option>
                                                <option value="transparencia/presupuesto_anual">Presupuesto Anual</option>
                                                <option value="transparencia/pae">PAE</option>
                                                <option value="transparencia/matrices_indicadores">Matrices de Indicadores</option>
                                                <option value="transparencia/conac">CONAC</option>
                                                <option value="transparencia/financiero">Financiero</option>
                                                <option value="transparencia/avisos_privacidad">Avisos de Privacidad</option>
                                            </optgroup>
                                            <optgroup label="Especiales">
                                                <option value="__ubicacion__">Ubicacion en Google Maps</option>
                                                <option value="#">Sin enlace (#)</option>
                                            </optgroup>
                                        </select>
                                        <input type="text" class="form-control" id="link_url_new" name="link_url" value="#" placeholder="index.php o https://...">
                                        <div class="form-text">Selecciona una seccion del sitio o escribe la URL manualmente.</div>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" name="link_nueva_tab" id="linkNuevaTab">
                                        <label class="form-check-label" for="linkNuevaTab">Abrir en nueva pestaña</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Orden</label>
                                        <input type="number" class="form-control" name="link_orden" value="0" min="0">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-circle me-1"></i> Crear enlace</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-list-ul me-1"></i> Enlaces del footer
                                <span class="badge bg-secondary ms-1"><?= count($footerLinks) ?></span>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($footerLinks)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-link-45deg" style="font-size:2rem;"></i>
                                    <p class="mt-2 mb-0">No hay enlaces. Use el formulario para agregar.</p>
                                </div>
                                <?php else: ?>
                                <div class="footer-links-wrapper">
                                    <table class="table table-hover align-middle mb-0 footer-links-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:50px;">Orden</th>
                                                <th>Título</th>
                                                <th>URL</th>
                                                <th style="width:70px;">Nueva tab</th>
                                                <th style="width:160px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($footerLinks as $fl): ?>
                                            <tr>
                                                <td data-label="Orden" class="text-center"><?= (int) $fl['orden'] ?></td>
                                                <td data-label="Título"><strong><?= htmlspecialchars($fl['titulo']) ?></strong></td>
                                                <td data-label="URL" class="small text-muted">
                                                    <?php if ($fl['url'] === '__ubicacion__'): ?>
                                                        <span class="badge bg-info text-dark">📍 Ubicación Maps</span>
                                                    <?php elseif ($fl['url'] === '#'): ?>
                                                        <span class="badge bg-secondary">Sin enlace</span>
                                                    <?php else: ?>
                                                        <span class="d-block text-truncate" style="max-width:180px;" title="<?= htmlspecialchars($fl['url']) ?>"><?= htmlspecialchars($fl['url']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Nueva tab" class="text-center"><?= $fl['nueva_tab'] ? '<span class="badge bg-success">Sí</span>' : '<span class="text-muted small">No</span>' ?></td>
                                                <td data-label="Acciones">
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#editLink<?= (int)$fl['id'] ?>">
                                                            <i class="bi bi-pencil"></i> Editar
                                                        </button>
                                                        <button class="btn btn-sm btn-action-delete" data-bs-toggle="modal" data-bs-target="#delLink<?= (int)$fl['id'] ?>">
                                                            <i class="bi bi-trash3"></i> Eliminar
                                                        </button>
                                                    </div>
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

                <!-- Modales de edición/eliminación de links -->
                <?php foreach ($footerLinks as $fl): ?>
                <div class="modal fade" id="editLink<?= (int)$fl['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="footer">
                                <input type="hidden" name="action" value="link_edit">
                                <input type="hidden" name="link_id" value="<?= (int)$fl['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Editar enlace</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Título</label>
                                        <input type="text" class="form-control" name="link_titulo" value="<?= htmlspecialchars($fl['titulo']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">URL</label>
                                        <select class="form-select mb-2" onchange="if(this.value!=='__custom__'){document.getElementById('link_url_<?= (int)$fl['id'] ?>').value=this.value;}else{document.getElementById('link_url_<?= (int)$fl['id'] ?>').value='';}">
                                            <option value="__custom__">-- Escribir URL manualmente --</option>
                                            <optgroup label="Paginas del sitio">
                                                <option value="index" <?= $fl['url']==='index.php'?'selected':'' ?>>Inicio</option>
                                                <option value="acerca-del-dif/presidencia" <?= $fl['url']==='acerca-del-dif/presidencia.php'?'selected':'' ?>>Presidencia</option>
                                                <option value="acerca-del-dif/direcciones" <?= $fl['url']==='acerca-del-dif/direcciones.php'?'selected':'' ?>>Direcciones</option>
                                                <option value="acerca-del-dif/organigrama" <?= $fl['url']==='acerca-del-dif/organigrama.php'?'selected':'' ?>>Organigrama</option>
                                                <option value="comunicacion-social/noticias" <?= $fl['url']==='comunicacion-social/noticias.php'?'selected':'' ?>>Noticias</option>
                                                <option value="comunicacion-social/galeria" <?= $fl['url']==='comunicacion-social/galeria.php'?'selected':'' ?>>Galeria</option>
                                                <option value="voluntariado" <?= $fl['url']==='voluntariado.php'?'selected':'' ?>>Voluntariado</option>
                                            </optgroup>
                                            <optgroup label="Transparencia">
                                                <option value="transparencia/SEAC" <?= $fl['url']==='transparencia/SEAC.php'?'selected':'' ?>>SEAC</option>
                                                <option value="transparencia/cuenta_publica" <?= $fl['url']==='transparencia/cuenta_publica.php'?'selected':'' ?>>Cuenta Publica</option>
                                                <option value="transparencia/presupuesto_anual" <?= $fl['url']==='transparencia/presupuesto_anual.php'?'selected':'' ?>>Presupuesto Anual</option>
                                                <option value="transparencia/pae" <?= $fl['url']==='transparencia/pae.php'?'selected':'' ?>>PAE</option>
                                                <option value="transparencia/matrices_indicadores" <?= $fl['url']==='transparencia/matrices_indicadores.php'?'selected':'' ?>>Matrices de Indicadores</option>
                                                <option value="transparencia/conac" <?= $fl['url']==='transparencia/conac.php'?'selected':'' ?>>CONAC</option>
                                                <option value="transparencia/financiero" <?= $fl['url']==='transparencia/financiero.php'?'selected':'' ?>>Financiero</option>
                                                <option value="transparencia/avisos_privacidad" <?= $fl['url']==='transparencia/avisos_privacidad.php'?'selected':'' ?>>Avisos de Privacidad</option>
                                            </optgroup>
                                            <optgroup label="Especiales">
                                                <option value="__ubicacion__" <?= $fl['url']==='__ubicacion__'?'selected':'' ?>>Ubicacion en Google Maps</option>
                                                <option value="#" <?= $fl['url']==='#'?'selected':'' ?>>Sin enlace (#)</option>
                                            </optgroup>
                                        </select>
                                        <input type="text" class="form-control" id="link_url_<?= (int)$fl['id'] ?>" name="link_url" value="<?= htmlspecialchars($fl['url']) ?>" placeholder="index.php o https://...">
                                        <div class="form-text">Selecciona una seccion del sitio o escribe la URL manualmente.</div>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" name="link_nueva_tab" id="editNT<?= (int)$fl['id'] ?>"<?= $fl['nueva_tab'] ? ' checked' : '' ?>>
                                        <label class="form-check-label" for="editNT<?= (int)$fl['id'] ?>">Abrir en nueva pestaña</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Orden</label>
                                        <input type="number" class="form-control" name="link_orden" value="<?= (int)$fl['orden'] ?>" min="0">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning"><i class="bi bi-pencil me-1"></i> Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="delLink<?= (int)$fl['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="footer">
                                <input type="hidden" name="action" value="link_delete">
                                <input type="hidden" name="link_id" value="<?= (int)$fl['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Eliminar enlace</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Eliminar <strong><?= htmlspecialchars($fl['titulo']) ?></strong>?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i> Eliminar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=13"></script>
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

