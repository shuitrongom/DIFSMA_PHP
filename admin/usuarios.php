<?php
/**
 * admin/usuarios.php - Gestion de usuarios (solo admin)
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../includes/db.php';

// Solo el admin puede acceder
if (($_SESSION['admin_rol'] ?? '') !== 'admin') {
    header('Location: dashboard.php'); exit;
}

$pdo = get_db();

// Obtener todas las secciones del sidebar para asignar permisos
require_once __DIR__ . '/sidebar_sections.php';
$all_sections = [];
foreach ($sidebar_groups as $group) {
    foreach ($group['items'] as $item) {
        $all_sections[] = ['file' => $item['file'], 'title' => $item['title'], 'group' => $group['group']];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_validate($token)) {
        $_SESSION['flash_message'] = 'Token CSRF invalido.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: usuarios.php'); exit;
    }

    // CREAR USUARIO
    if ($action === 'create') {
        $username = trim($_POST['username'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $password = $_POST['password'] ?? '';
        $secciones = $_POST['secciones'] ?? [];
        if (empty($username) || empty($password)) {
            $_SESSION['flash_message'] = 'Usuario y contrasena son obligatorios.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: usuarios.php'); exit;
        }
        if (strlen($password) < 6) {
            $_SESSION['flash_message'] = 'La contrasena debe tener al menos 6 caracteres.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: usuarios.php'); exit;
        }
        $s = $pdo->prepare('SELECT id FROM admin WHERE username = ?'); $s->execute([$username]);
        if ($s->fetch()) {
            $_SESSION['flash_message'] = 'El usuario ya existe.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: usuarios.php'); exit;
        }
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO admin (username, nombre, password, rol, activo) VALUES (?,?,?,?,1)')
                ->execute([$username, $nombre, $hash, 'usuario']);
            $userId = (int) $pdo->lastInsertId();
            // Asignar permisos
            $stmtP = $pdo->prepare('INSERT INTO admin_permisos (user_id, seccion_file) VALUES (?,?)');
            foreach ($secciones as $sec) { $stmtP->execute([$userId, $sec]); }
            $_SESSION['flash_message'] = "Usuario \"{$username}\" creado con " . count($secciones) . " secciones.";
            $_SESSION['flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : 'Error al crear.';
            $_SESSION['flash_type'] = 'danger';
        }
        header('Location: usuarios.php'); exit;
    }

    // RESET PASSWORD
    if ($action === 'reset_password') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $newPass = $_POST['new_password'] ?? '';
        if (strlen($newPass) < 6) {
            $_SESSION['flash_message'] = 'La contrasena debe tener al menos 6 caracteres.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: usuarios.php'); exit;
        }
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE admin SET password = ? WHERE id = ? AND rol = ?')->execute([$hash, $userId, 'usuario']);
        $_SESSION['flash_message'] = 'Contrasena actualizada.';
        $_SESSION['flash_type'] = 'success';
        header('Location: usuarios.php'); exit;
    }

    // UPDATE PERMISOS
    if ($action === 'update_permisos') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $secciones = $_POST['secciones'] ?? [];
        $pdo->prepare('DELETE FROM admin_permisos WHERE user_id = ?')->execute([$userId]);
        $stmtP = $pdo->prepare('INSERT INTO admin_permisos (user_id, seccion_file) VALUES (?,?)');
        foreach ($secciones as $sec) { $stmtP->execute([$userId, $sec]); }
        $_SESSION['flash_message'] = 'Permisos actualizados (' . count($secciones) . ' secciones).';
        $_SESSION['flash_type'] = 'success';
        header('Location: usuarios.php'); exit;
    }

    // TOGGLE ACTIVO
    if ($action === 'toggle') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $pdo->prepare('UPDATE admin SET activo = NOT activo WHERE id = ? AND rol = ?')->execute([$userId, 'usuario']);
        $_SESSION['flash_message'] = 'Estado actualizado.';
        $_SESSION['flash_type'] = 'success';
        header('Location: usuarios.php'); exit;
    }

    // DELETE
    if ($action === 'delete') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $pdo->prepare('DELETE FROM admin WHERE id = ? AND rol = ?')->execute([$userId, 'usuario']);
        $_SESSION['flash_message'] = 'Usuario eliminado.';
        $_SESSION['flash_type'] = 'success';
        header('Location: usuarios.php'); exit;
    }
}

// Consultar usuarios
$usuarios = $pdo->query("SELECT * FROM admin WHERE rol = 'usuario' ORDER BY id DESC")->fetchAll();
// Cargar permisos de cada usuario
$permisosMap = [];
$stmtP = $pdo->query('SELECT user_id, seccion_file FROM admin_permisos');
while ($r = $stmtP->fetch()) { $permisosMap[(int)$r['user_id']][] = $r['seccion_file']; }

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
$token = csrf_token();
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Usuarios - Admin DIF</title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35"><link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="../css/admin.css?v=7">
</head><body><div class="d-flex">
<?php render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
<div class="main-content">
<nav class="navbar navbar-light bg-white shadow-sm px-3">
<button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
<span class="navbar-brand mb-0 h6"><i class="bi bi-people me-1"></i> Gestion de Usuarios</span>
<a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
</nav>
<div class="container-fluid p-4">
<?php if ($flashMessage): ?><div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show"><?= htmlspecialchars($flashMessage) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row g-4">
<!-- Crear usuario -->
<div class="col-lg-5">
<div class="card"><div class="card-header bg-primary text-white"><i class="bi bi-person-plus me-1"></i> Crear usuario</div>
<div class="card-body">
<form method="POST" action="usuarios.php">
<input type="hidden" name="action" value="create"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="mb-3"><label class="form-label">Usuario</label><input type="text" class="form-control" name="username" required placeholder="Ej: juan.perez" autocomplete="off"></div>
<div class="mb-3"><label class="form-label">Nombre completo</label><input type="text" class="form-control" name="nombre" placeholder="Ej: Juan Perez Lopez"></div>
<div class="mb-3"><label class="form-label">Contrasena</label><input type="password" class="form-control" name="password" required minlength="6" placeholder="Minimo 6 caracteres" autocomplete="new-password"></div>
<div class="mb-3"><label class="form-label">Secciones permitidas</label>
<div style="max-height:250px;overflow-y:auto;border:1px solid #dee2e6;border-radius:6px;padding:8px;">
<?php $lastGroup = ''; foreach ($all_sections as $sec):
    if ($sec['group'] !== $lastGroup) { $lastGroup = $sec['group']; ?>
    <div class="fw-bold small text-muted mt-2 mb-1"><i class="bi bi-folder me-1"></i><?= htmlspecialchars($sec['group']) ?></div>
<?php } ?>
<div class="form-check"><input class="form-check-input" type="checkbox" name="secciones[]" value="<?= htmlspecialchars($sec['file']) ?>" id="new_<?= htmlspecialchars($sec['file']) ?>"><label class="form-check-label small" for="new_<?= htmlspecialchars($sec['file']) ?>"><?= htmlspecialchars($sec['title']) ?></label></div>
<?php endforeach; ?>
</div>
<small class="text-muted">Selecciona las secciones que este usuario podra ver</small>
</div>
<button type="submit" class="btn btn-primary w-100"><i class="bi bi-person-plus me-1"></i> Crear usuario</button>
</form></div></div></div>

<!-- Listado de usuarios -->
<div class="col-lg-7">
<div class="card"><div class="card-header"><i class="bi bi-people me-1"></i> Usuarios <span class="badge bg-secondary ms-1"><?= count($usuarios) ?></span></div>
<div class="card-body p-0">
<?php if (empty($usuarios)): ?><div class="text-center text-muted py-4"><i class="bi bi-people" style="font-size:2rem;"></i><p class="mt-2">No hay usuarios creados.</p></div>
<?php else: ?>
<?php foreach ($usuarios as $usr): $uPerms = $permisosMap[(int)$usr['id']] ?? []; ?>
<div class="border-bottom p-3">
<div class="d-flex justify-content-between align-items-start">
<div>
<h6 class="mb-1"><i class="bi bi-person me-1"></i> <?= htmlspecialchars($usr['username']) ?>
<?php if ($usr['activo']): ?><span class="badge bg-success ms-1">Activo</span><?php else: ?><span class="badge bg-secondary ms-1">Inactivo</span><?php endif; ?>
</h6>
<?php if (!empty($usr['nombre'])): ?><small class="text-muted"><?= htmlspecialchars($usr['nombre']) ?></small><br><?php endif; ?>
<small class="text-muted"><?= count($uPerms) ?> secciones asignadas</small>
</div>
<div class="btn-group btn-group-sm">
<button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#permsModal<?= (int)$usr['id'] ?>" title="Permisos"><i class="bi bi-shield-check"></i></button>
<button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#passModal<?= (int)$usr['id'] ?>" title="Reset password"><i class="bi bi-key"></i></button>
<form method="POST" action="usuarios.php" class="d-inline"><input type="hidden" name="action" value="toggle"><input type="hidden" name="user_id" value="<?= (int)$usr['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-outline-<?= $usr['activo'] ? 'secondary' : 'success' ?>" title="<?= $usr['activo'] ? 'Desactivar' : 'Activar' ?>"><i class="bi bi-<?= $usr['activo'] ? 'pause' : 'play' ?>"></i></button></form>
<form method="POST" action="usuarios.php" class="d-inline" onsubmit="return confirm('Eliminar usuario?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="<?= (int)$usr['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"><button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button></form>
</div></div></div>

<!-- Modal Permisos -->
<div class="modal fade" id="permsModal<?= (int)$usr['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
<form method="POST" action="usuarios.php"><input type="hidden" name="action" value="update_permisos"><input type="hidden" name="user_id" value="<?= (int)$usr['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="modal-header"><h5 class="modal-title"><i class="bi bi-shield-check me-1"></i> Permisos: <?= htmlspecialchars($usr['username']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body" style="max-height:400px;overflow-y:auto;">
<?php $lastG=''; foreach ($all_sections as $sec): if($sec['group']!==$lastG){$lastG=$sec['group'];?><div class="fw-bold small text-muted mt-2 mb-1"><i class="bi bi-folder me-1"></i><?= htmlspecialchars($sec['group']) ?></div><?php } ?>
<div class="form-check"><input class="form-check-input" type="checkbox" name="secciones[]" value="<?= htmlspecialchars($sec['file']) ?>" id="p<?= (int)$usr['id'] ?>_<?= htmlspecialchars($sec['file']) ?>" <?= in_array($sec['file'], $uPerms) ? 'checked' : '' ?>><label class="form-check-label small" for="p<?= (int)$usr['id'] ?>_<?= htmlspecialchars($sec['file']) ?>"><?= htmlspecialchars($sec['title']) ?></label></div>
<?php endforeach; ?>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i> Guardar permisos</button></div>
</form></div></div></div>

<!-- Modal Reset Password -->
<div class="modal fade" id="passModal<?= (int)$usr['id'] ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
<form method="POST" action="usuarios.php"><input type="hidden" name="action" value="reset_password"><input type="hidden" name="user_id" value="<?= (int)$usr['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<div class="modal-header"><h5 class="modal-title"><i class="bi bi-key me-1"></i> Nueva contrasena: <?= htmlspecialchars($usr['username']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body"><div class="mb-3"><label class="form-label">Nueva contrasena</label><input type="password" class="form-control" name="new_password" required minlength="6" placeholder="Minimo 6 caracteres" autocomplete="new-password"></div></div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-info text-white"><i class="bi bi-check-lg me-1"></i> Cambiar</button></div>
</form></div></div></div>
<?php endforeach; ?>
<?php endif; ?>
</div></div></div></div>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>var sb=document.getElementById('sidebar');if(window.innerWidth<=768)sb.classList.add('collapsed');document.getElementById('toggleSidebar').addEventListener('click',function(){sb.classList.toggle('collapsed');});var cb=document.getElementById('closeSidebar');if(cb)cb.addEventListener('click',function(){sb.classList.add('collapsed');});</script>
</body></html>
