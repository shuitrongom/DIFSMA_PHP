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
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
            $_SESSION['flash_message'] = 'La contrasena debe tener al menos 8 caracteres, una mayuscula, una minuscula, un numero y un simbolo.';
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
        if (strlen($newPass) < 8 || !preg_match('/[A-Z]/', $newPass) || !preg_match('/[a-z]/', $newPass) || !preg_match('/[0-9]/', $newPass) || !preg_match('/[\W_]/', $newPass)) {
            $_SESSION['flash_message'] = 'La contrasena debe tener al menos 8 caracteres, una mayuscula, una minuscula, un numero y un simbolo.';
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

<!-- Modal advertencia campos obligatorios -->
<div class="modal fade" id="modalPassWeak" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow">
      <div class="modal-body text-center p-4">
        <div id="modalWarnIcon" style="font-size:3rem;color:#dc3545;"><i class="bi bi-exclamation-circle"></i></div>
        <h5 class="mt-2 mb-1" id="modalWarnTitle">Campo requerido</h5>
        <p class="text-muted small mb-3" id="modalWarnMsg">Por favor completa este campo antes de continuar.</p>
        <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal" id="modalWarnBtn">
          <i class="bi bi-arrow-left me-1"></i> Corregir
        </button>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
<!-- Crear usuario -->
<div class="col-lg-5">
<div class="card shadow-sm border-0">
  <div class="card-header text-white" style="background:rgb(200,16,44);">
    <i class="bi bi-person-plus me-1"></i> Nuevo usuario
  </div>
  <div class="card-body p-4">
<form method="POST" action="usuarios.php" id="formCrearUsuario" novalidate>
<input type="hidden" name="action" value="create">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">

<div class="mb-3">
  <label class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
  <div class="input-group">
    <span class="input-group-text bg-white"><i class="bi bi-person text-secondary"></i></span>
    <input type="text" class="form-control" name="username" required placeholder="Ej: juan.perez" autocomplete="off">
  </div></div>

<div class="mb-3">
  <label class="form-label fw-semibold">Nombre completo</label>
  <div class="input-group">
    <span class="input-group-text bg-white"><i class="bi bi-card-text text-secondary"></i></span>
    <input type="text" class="form-control" name="nombre" placeholder="Ej: Juan Pérez López">
  </div>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
  <div class="input-group">
    <span class="input-group-text bg-white"><i class="bi bi-lock text-secondary"></i></span>
    <input type="password" class="form-control" name="password" id="new_password" required minlength="8"
           placeholder="Crea una contraseña segura" autocomplete="new-password"
           oninput="checkReqs(this,'new_reqs','new_strength')">
    <button type="button" class="btn btn-outline-secondary" tabindex="-1"
            onclick="toggleVer('new_password',this)"
            title="Mostrar contraseña"
            data-tooltip="Mostrar/ocultar contraseña">
      <i class="bi bi-eye"></i>
    </button>
  </div>

  <!-- Barra de fortaleza -->
  <div id="new_strength" class="mt-2"></div>

  <!-- Checklist de requisitos -->
  <div class="rounded p-2 mt-2" style="background:#f8f9fa;border:1px solid #e9ecef;">
    <ul id="new_reqs" class="list-unstyled mb-0" style="font-size:12.5px;line-height:1.9;">
      <li data-req="len" class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Mínimo 8 caracteres</li>
      <li data-req="up"  class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 mayúscula (A-Z)</li>
      <li data-req="low" class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 minúscula (a-z)</li>
      <li data-req="num" class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 número (0-9)</li>
      <li data-req="sym" class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 símbolo (!@#$%...)</li>
    </ul>
  </div>

  <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-2"
          onclick="generarPassword('new_password','new_strength','new_reqs')">
    <i class="bi bi-magic me-1"></i> Generar contraseña segura automáticamente
  </button>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Secciones permitidas</label>
  <div style="max-height:220px;overflow-y:auto;border:1px solid #dee2e6;border-radius:6px;padding:10px;background:#fff;">
    <?php $lastGroup = ''; foreach ($all_sections as $sec):
        if ($sec['group'] !== $lastGroup) { $lastGroup = $sec['group']; ?>
        <div class="fw-bold small text-muted mt-2 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">
          <i class="bi bi-folder2-open me-1"></i><?= htmlspecialchars($sec['group']) ?>
        </div>
    <?php } ?>
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="secciones[]"
             value="<?= htmlspecialchars($sec['file']) ?>" id="new_<?= htmlspecialchars($sec['file']) ?>">
      <label class="form-check-label small" for="new_<?= htmlspecialchars($sec['file']) ?>">
        <?= htmlspecialchars($sec['title']) ?>
      </label>
    </div>
    <?php endforeach; ?>
  </div>
  <small class="text-muted">Selecciona las secciones que este usuario podrá ver</small>
</div>

<button type="submit" class="btn w-100 text-white fw-semibold" style="background:rgb(200,16,44);">
  <i class="bi bi-person-check me-1"></i> Crear usuario
</button>
</form>
</div></div></div>

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
<div class="d-flex gap-1 flex-wrap justify-content-end">
<button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#permsModal<?= (int)$usr['id'] ?>" title="Gestionar permisos">
  <i class="bi bi-shield-check"></i> Permisos
</button>
<button class="btn btn-sm btn-action-key" data-bs-toggle="modal" data-bs-target="#passModal<?= (int)$usr['id'] ?>" title="Cambiar contraseña">
  <i class="bi bi-key"></i> Contraseña
</button>
<form method="POST" action="usuarios.php" class="d-inline">
  <input type="hidden" name="action" value="toggle">
  <input type="hidden" name="user_id" value="<?= (int)$usr['id'] ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
  <button type="submit" class="btn btn-sm <?= $usr['activo'] ? 'btn-action-pause' : 'btn-action-play' ?>" title="<?= $usr['activo'] ? 'Desactivar usuario' : 'Activar usuario' ?>">
    <i class="bi bi-<?= $usr['activo'] ? 'pause-circle' : 'play-circle' ?>"></i> <?= $usr['activo'] ? 'Desactivar' : 'Activar' ?>
  </button>
</form>
<form method="POST" action="usuarios.php" class="d-inline" onsubmit="return confirm('¿Eliminar usuario <?= htmlspecialchars($usr['username']) ?>? Esta acción no se puede deshacer.')">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="user_id" value="<?= (int)$usr['id'] ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
  <button type="submit" class="btn btn-sm btn-action-delete" title="Eliminar usuario">
    <i class="bi bi-trash3"></i> Eliminar
  </button>
</form>
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
<div class="modal-body">
<div class="mb-3"><label class="form-label">Nueva contraseña</label>
<div style="position:relative;">
    <input type="password" class="form-control pe-5 pass-input" name="new_password" required minlength="8" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
    <button type="button" tabindex="-1" onclick="toggleVer(this.previousElementSibling,this)"
            title="Mostrar contraseña"
            style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;padding:0;line-height:1;font-size:0.85rem;outline:none;box-shadow:none;cursor:pointer;">
      <i class="bi bi-eye"></i>
    </button>
</div>
<div class="strength-bar mt-1"></div>
<ul class="pass-reqs list-unstyled mt-2 mb-1" style="font-size:12px;">
    <li data-req="len"  style="color:#adb5bd;"><i class="bi bi-x-circle-fill me-1"></i>Mínimo 8 caracteres</li>
    <li data-req="up"   style="color:#adb5bd;"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 mayúscula</li>
    <li data-req="low"  style="color:#adb5bd;"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 minúscula</li>
    <li data-req="num"  style="color:#adb5bd;"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 número</li>
    <li data-req="sym"  style="color:#adb5bd;"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 símbolo (!@#$...)</li>
</ul>
<button type="button" class="btn btn-sm btn-outline-secondary w-100 btn-gen-modal">
    <i class="bi bi-magic me-1"></i> Generar contraseña segura
</button>
</div></div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-info text-white"><i class="bi bi-check-lg me-1"></i> Cambiar</button></div>
</form></div></div></div>
<?php endforeach; ?>
<?php endif; ?>
</div></div></div></div>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var sb=document.getElementById('sidebar');if(window.innerWidth<=768)sb.classList.add('collapsed');document.getElementById('toggleSidebar').addEventListener('click',function(){sb.classList.toggle('collapsed');});var cb=document.getElementById('closeSidebar');if(cb)cb.addEventListener('click',function(){sb.classList.add('collapsed');});

function generarPasswordFuerte() {
    var u='ABCDEFGHIJKLMNOPQRSTUVWXYZ', l='abcdefghijklmnopqrstuvwxyz', d='0123456789', s='!@#$%^&*()-_=+[]{}|;:,.<>?';
    var all=u+l+d+s;
    var pwd=[u[r(u.length)],u[r(u.length)],l[r(l.length)],l[r(l.length)],d[r(d.length)],d[r(d.length)],s[r(s.length)],s[r(s.length)]];
    for(var i=0;i<4;i++) pwd.push(all[r(all.length)]);
    return pwd.sort(function(){return Math.random()-.5;}).join('');
}
function r(n){return Math.floor(Math.random()*n);}

function checkReqs(inp, reqsId, strengthId) {
    var v = inp.value;
    var rules = {
        len: v.length >= 8,
        up:  /[A-Z]/.test(v),
        low: /[a-z]/.test(v),
        num: /[0-9]/.test(v),
        sym: /[\W_]/.test(v)
    };
    var list = reqsId ? document.getElementById(reqsId) : inp.closest('.mb-3').querySelector('.pass-reqs, [id$="_reqs"]');
    if (list) {
        list.querySelectorAll('li').forEach(function(li) {
            var ok = rules[li.dataset.req];
            li.style.color = ok ? '#198754' : '#adb5bd';
            li.querySelector('i').className = ok ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1';
        });
    }
    var score = Object.values(rules).filter(Boolean).length;
    var colors = ['#dc3545','#fd7e14','#ffc107','#20c997','#198754'];
    var labels = ['Muy débil','Débil','Regular','Fuerte','Muy fuerte'];
    var barEl = strengthId ? document.getElementById(strengthId) : inp.closest('.mb-3').querySelector('.strength-bar, [id$="_strength"]');
    if (barEl) {
        if (v.length === 0) { barEl.innerHTML=''; return; }
        var idx = Math.max(0, score-1);
        barEl.innerHTML = '<div style="height:5px;background:#e9ecef;border-radius:3px;overflow:hidden;"><div style="width:'+Math.round(score/5*100)+'%;height:100%;background:'+colors[idx]+';transition:width .3s;border-radius:3px;"></div></div><small style="color:'+colors[idx]+';font-size:11px;">'+labels[idx]+'</small>';
    }
}

function allReqsMet(inp) {
    var v = inp.value;
    return v.length>=8 && /[A-Z]/.test(v) && /[a-z]/.test(v) && /[0-9]/.test(v) && /[\W_]/.test(v);
}

function generarPassword(inputId, strengthId, reqsId) {
    var pwd = generarPasswordFuerte();
    var inp = document.getElementById(inputId);
    inp.value = pwd;
    inp.type = 'text';
    checkReqs(inp, reqsId);
    setTimeout(function(){ inp.type='password'; }, 3000);
}

function toggleVer(inp, btn) {
    if (typeof inp === 'string') inp = document.getElementById(inp);
    inp.type = inp.type==='password' ? 'text' : 'password';
    btn.querySelector('i').className = inp.type==='text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}

// Mostrar modal de advertencia con mensaje dinámico
function showWarn(icon, title, msg, focusEl) {
    document.getElementById('modalWarnIcon').innerHTML = '<i class="bi bi-'+icon+'"></i>';
    document.getElementById('modalWarnTitle').textContent = title;
    document.getElementById('modalWarnMsg').textContent   = msg;
    var m = new bootstrap.Modal(document.getElementById('modalPassWeak'));
    document.getElementById('modalPassWeak').addEventListener('hidden.bs.modal', function handler() {
        if (focusEl) focusEl.focus();
        document.getElementById('modalPassWeak').removeEventListener('hidden.bs.modal', handler);
    });
    m.show();
}

// Quitar is-invalid al corregir
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('is-invalid')) e.target.classList.remove('is-invalid');
    if (e.target.name === 'password') checkReqs(e.target, 'new_reqs', 'new_strength');
    if (e.target.classList.contains('pass-input')) checkReqs(e.target, null, null);
});

// Botón generar en modales
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-gen-modal');
    if (!btn) return;
    var body = btn.closest('.modal-body');
    var inp  = body.querySelector('.pass-input');
    var pwd  = generarPasswordFuerte();
    inp.value = pwd;
    inp.type  = 'text';
    checkReqs(inp, null);
    setTimeout(function(){ inp.type='password'; }, 3000);
});

// Validar antes de submit — formulario crear
var formCrear = document.getElementById('formCrearUsuario');
if (formCrear) {
    formCrear.addEventListener('submit', function(e) {
        var username = this.querySelector('[name="username"]');
        var nombre   = this.querySelector('[name="nombre"]');
        var password = this.querySelector('[name="password"]');

        if (!username.value.trim()) {
            e.preventDefault();
            username.classList.add('is-invalid');
            showWarn('person-x', 'Usuario requerido',
                'El campo "Usuario" es obligatorio. Ingresa un nombre de usuario único.', username);
            return;
        }
        if (!nombre.value.trim()) {
            e.preventDefault();
            nombre.classList.add('is-invalid');
            showWarn('card-text', 'Nombre requerido',
                'El campo "Nombre completo" es obligatorio. Ingresa el nombre del usuario.', nombre);
            return;
        }
        if (!password.value) {
            e.preventDefault();
            password.classList.add('is-invalid');
            showWarn('lock', 'Contraseña requerida',
                'Debes ingresar una contraseña para el nuevo usuario.', password);
            return;
        }
        if (!allReqsMet(password)) {
            e.preventDefault();
            password.classList.add('is-invalid');
            checkReqs(password, 'new_reqs', 'new_strength');
            showWarn('shield-exclamation', 'Contraseña insegura',
                'La contraseña no cumple todos los requisitos de seguridad. Revisa los puntos marcados en rojo.', password);
            return;
        }
    });
}
</script>
</body></html>
