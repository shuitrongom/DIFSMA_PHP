<?php
/**
 * admin/cambiar_password.php — Cambio obligatorio de contraseña
 */
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

// Debe estar autenticado
if (($_SESSION['admin_logged'] ?? false) !== true) {
    header('Location: login'); exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/csrf.php';

$pdo      = get_db();
$user_id  = (int)($_SESSION['admin_id'] ?? 0);
$motivo   = $_GET['motivo'] ?? '';
$error    = '';
$success  = '';

// Obtener datos del usuario
$stmt = $pdo->prepare('SELECT username, nombre, email FROM admin WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) { header('Location: logout'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token    = $_POST['csrf_token'] ?? '';
    $current  = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!csrf_validate($token)) {
        $error = 'Token inválido. Recarga la página.';
    } elseif (empty($current) || empty($new_pass) || empty($confirm)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($new_pass !== $confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($new_pass) < 8 || !preg_match('/[A-Z]/', $new_pass) || !preg_match('/[a-z]/', $new_pass) || !preg_match('/[0-9]/', $new_pass) || !preg_match('/[\W_]/', $new_pass)) {
        $error = 'La contraseña no cumple los requisitos de seguridad.';
    } else {
        // Verificar contraseña actual
        $stmt2 = $pdo->prepare('SELECT password FROM admin WHERE id = ?');
        $stmt2->execute([$user_id]);
        $row = $stmt2->fetch();
        if (!$row || !password_verify($current, $row['password'])) {
            $error = 'La contraseña actual es incorrecta.';
        } else {
            // Actualizar contraseña
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE admin SET password = ?, password_changed_at = NOW() WHERE id = ?')
                ->execute([$hash, $user_id]);

            // Enviar correos
            try {
                require_once __DIR__ . '/../vendor/autoload.php';
                require_once __DIR__ . '/../config.php';

                // Obtener email del admin principal
                $stmt_adm = $pdo->prepare("SELECT email FROM admin WHERE rol = 'admin' AND id != ? LIMIT 1");
                $stmt_adm->execute([$user_id]);
                $admin_row = $stmt_adm->fetch();
                $admin_email = $admin_row['email'] ?? MAIL_FROM;

                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = MAIL_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = MAIL_USER;
                $mail->Password   = MAIL_PASS;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = MAIL_PORT;
                $mail->CharSet    = 'UTF-8';
                $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);

                $html_body = '
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:\'Segoe UI\',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
<tr><td style="background:linear-gradient(135deg,#1a2332 0%,#2d2d2d 60%,#C8102C 100%);padding:32px 40px;text-align:center;">
  <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;">DIF San Mateo Atenco</h1>
  <p style="color:rgba(255,255,255,0.75);margin:6px 0 0;font-size:13px;">Actualización de Contraseña</p>
</td></tr>
<tr><td style="padding:36px 40px;">
  <p style="color:#374151;font-size:15px;margin:0 0 16px;">Hola <strong>' . htmlspecialchars($user['nombre'] ?: $user['username']) . '</strong>,</p>
  <p style="color:#374151;font-size:14px;line-height:1.6;margin:0 0 20px;">Tu contraseña del Panel de Administración ha sido actualizada exitosamente el <strong>' . date('d/m/Y H:i') . '</strong>.</p>
  <div style="background:#f0fdf4;border-left:4px solid #22c55e;border-radius:0 8px 8px 0;padding:14px 18px;margin-bottom:20px;">
    <p style="color:#166534;font-size:13px;margin:0;"><strong>✅ Contraseña actualizada correctamente.</strong><br>Si no realizaste este cambio, contacta al administrador de inmediato.</p>
  </div>
</td></tr>
<tr><td style="background:#1a2332;padding:20px 40px;text-align:center;">
  <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">Sistema CMS — DIF San Mateo Atenco &nbsp;|&nbsp; Uso interno</p>
</td></tr>
</table></td></tr></table>
</body></html>';

                // Correo al usuario
                if (!empty($user['email'])) {
                    $mail->clearAddresses();
                    $mail->addAddress($user['email'], $user['nombre'] ?: $user['username']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Contraseña actualizada — DIF San Mateo Atenco';
                    $mail->Body    = $html_body;
                    $mail->AltBody = "Tu contraseña fue actualizada el " . date('d/m/Y H:i') . ". Si no realizaste este cambio, contacta al administrador.";
                    $mail->send();
                }

                // Correo al admin
                if ($admin_email && $admin_email !== ($user['email'] ?? '')) {
                    $mail->clearAddresses();
                    $mail->addAddress($admin_email, 'Administrador');
                    $mail->Subject = 'Aviso: ' . htmlspecialchars($user['username']) . ' actualizó su contraseña';
                    $mail->Body    = str_replace(
                        'Actualización de Contraseña',
                        'Aviso de Cambio de Contraseña',
                        $html_body
                    );
                    $mail->send();
                }
            } catch (\Exception $e) {
                // El correo falló pero la contraseña sí se cambió
            }

            header('Location: dashboard?pass_updated=1'); exit;
        }
    }
}

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Actualizar Contraseña — DIF Admin</title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/admin.css?v=7">
<style>
body { background:#f0f0f0; display:flex; align-items:center; justify-content:center; min-height:100vh; }
.card-pass { max-width:480px; width:100%; border-radius:16px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.12); }
.card-header-pass { background:linear-gradient(135deg,#1a2332 0%,#2d2d2d 60%,#C8102C 100%); padding:28px 32px; text-align:center; color:#fff; }
.req-item { font-size:12.5px; line-height:1.9; color:#adb5bd; }
.req-item.ok { color:#198754; }
</style>
</head>
<body>
<div class="card-pass bg-white">
  <div class="card-header-pass">
    <img src="../img/escudo.png" alt="DIF" style="height:52px;margin-bottom:10px;">
    <h5 class="mb-1 fw-700">
      <?= $motivo === 'expiracion' ? '⚠️ Contraseña Expirada' : 'Actualizar Contraseña' ?>
    </h5>
    <p class="mb-0" style="font-size:13px;opacity:.8;">
      <?= $motivo === 'expiracion'
        ? 'Tu contraseña tiene más de 90 días. Debes actualizarla para continuar.'
        : 'Ingresa tu contraseña actual y elige una nueva.' ?>
    </p>
  </div>
  <div class="p-4">
    <?php if ($error): ?>
      <div class="alert alert-danger py-2" style="font-size:.85rem;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="cambiar_password<?= $motivo ? '?motivo='.$motivo : '' ?>" id="formPass" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">

      <div class="mb-3">
        <label class="form-label fw-semibold">Contraseña actual <span class="text-danger">*</span></label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-lock text-secondary"></i></span>
          <input type="password" class="form-control" name="current_password" id="current_password" required placeholder="Tu contraseña actual">
          <button type="button" class="btn btn-outline-secondary" onclick="toggleVer('current_password',this)"><i class="bi bi-eye"></i></button>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Nueva contraseña <span class="text-danger">*</span></label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-lock-fill text-secondary"></i></span>
          <input type="password" class="form-control" name="new_password" id="new_password" required
                 placeholder="Mínimo 8 caracteres" oninput="checkReqs(this)">
          <button type="button" class="btn btn-outline-secondary" onclick="toggleVer('new_password',this)"><i class="bi bi-eye"></i></button>
        </div>
        <div id="strength_bar" class="mt-1"></div>
        <div class="rounded p-2 mt-2" style="background:#f8f9fa;border:1px solid #e9ecef;">
          <ul id="reqs" class="list-unstyled mb-0">
            <li data-req="len"  class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Mínimo 8 caracteres</li>
            <li data-req="up"   class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 mayúscula (A-Z)</li>
            <li data-req="low"  class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 minúscula (a-z)</li>
            <li data-req="num"  class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 número (0-9)</li>
            <li data-req="sym"  class="req-item"><i class="bi bi-x-circle-fill me-1"></i>Al menos 1 símbolo (!@#$%...)</li>
          </ul>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-2" onclick="genPass()">
          <i class="bi bi-magic me-1"></i> Generar contraseña segura
        </button>
      </div>

      <div class="mb-4">
        <label class="form-label fw-semibold">Confirmar nueva contraseña <span class="text-danger">*</span></label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-lock-fill text-secondary"></i></span>
          <input type="password" class="form-control" name="confirm_password" id="confirm_password" required placeholder="Repite la nueva contraseña">
          <button type="button" class="btn btn-outline-secondary" onclick="toggleVer('confirm_password',this)"><i class="bi bi-eye"></i></button>
        </div>
      </div>

      <button type="submit" class="btn w-100 text-white fw-semibold" style="background:rgb(200,16,44);">
        <i class="bi bi-shield-check me-1"></i> Actualizar contraseña
      </button>
    </form>

    <div class="text-center mt-3">
      <a href="logout" class="text-muted small"><i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleVer(id, btn) {
    var inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.querySelector('i').className = inp.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}
function checkReqs(inp) {
    var v = inp.value;
    var rules = { len: v.length>=8, up:/[A-Z]/.test(v), low:/[a-z]/.test(v), num:/[0-9]/.test(v), sym:/[\W_]/.test(v) };
    document.querySelectorAll('#reqs li').forEach(function(li) {
        var ok = rules[li.dataset.req];
        li.className = 'req-item' + (ok ? ' ok' : '');
        li.querySelector('i').className = ok ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1';
    });
    var score = Object.values(rules).filter(Boolean).length;
    var colors = ['#dc3545','#fd7e14','#ffc107','#20c997','#198754'];
    var labels = ['Muy débil','Débil','Regular','Fuerte','Muy fuerte'];
    var bar = document.getElementById('strength_bar');
    if (!v.length) { bar.innerHTML=''; return; }
    var idx = Math.max(0, score-1);
    bar.innerHTML = '<div style="height:5px;background:#e9ecef;border-radius:3px;overflow:hidden;"><div style="width:'+Math.round(score/5*100)+'%;height:100%;background:'+colors[idx]+';transition:width .3s;border-radius:3px;"></div></div><small style="color:'+colors[idx]+';font-size:11px;">'+labels[idx]+'</small>';
}
function genPass() {
    var u='ABCDEFGHIJKLMNOPQRSTUVWXYZ',l='abcdefghijklmnopqrstuvwxyz',d='0123456789',s='!@#$%^&*()-_=+';
    var all=u+l+d+s, pwd=[u[r(u.length)],l[r(l.length)],d[r(d.length)],s[r(s.length)]];
    for(var i=0;i<8;i++) pwd.push(all[r(all.length)]);
    pwd = pwd.sort(function(){return Math.random()-.5;}).join('');
    var inp = document.getElementById('new_password');
    inp.value = pwd; inp.type = 'text';
    checkReqs(inp);
    setTimeout(function(){ inp.type='password'; }, 3000);
}
function r(n){ return Math.floor(Math.random()*n); }
</script>
</body>
</html>
