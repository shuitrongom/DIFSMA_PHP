<?php
/**
 * admin/olvide_password.php — Solicitud de recuperación de contraseña
 * Notifica al administrador para que comparta la nueva contraseña.
 */
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

// Si ya está logueado, redirigir
if (($_SESSION['admin_logged'] ?? false) === true) {
    header('Location: dashboard'); exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/csrf.php';

$pdo     = get_db();
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token    = $_POST['csrf_token'] ?? '';
    $username = trim($_POST['username'] ?? '');

    if (!csrf_validate($token)) {
        $error = 'Token inválido. Recarga la página.';
    } elseif (empty($username)) {
        $error = 'Ingresa tu nombre de usuario.';
    } else {
        // Buscar usuario
        $stmt = $pdo->prepare("SELECT id, username, nombre, email FROM admin WHERE username = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Siempre mostrar éxito (no revelar si el usuario existe)
        $success = 'Si el usuario existe, se ha notificado al administrador. Espera que te contacte con tus nuevas credenciales.';

        if ($user) {
            try {
                require_once __DIR__ . '/../vendor/autoload.php';
                require_once __DIR__ . '/../config.php';

                // Obtener email del admin principal
                $stmt_adm = $pdo->prepare("SELECT email, nombre FROM admin WHERE rol = 'admin' LIMIT 1");
                $stmt_adm->execute();
                $admin = $stmt_adm->fetch();
                $admin_email = $admin['email'] ?? MAIL_FROM;

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
                $mail->addAddress($admin_email, $admin['nombre'] ?? 'Administrador');
                $mail->isHTML(true);
                $mail->Subject = '⚠️ Solicitud de recuperación de contraseña — ' . htmlspecialchars($username);
                $mail->Body = '
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:\'Segoe UI\',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
<tr><td style="background:linear-gradient(135deg,#1a2332 0%,#2d2d2d 60%,#C8102C 100%);padding:32px 40px;text-align:center;">
  <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;">DIF San Mateo Atenco</h1>
  <p style="color:rgba(255,255,255,0.75);margin:6px 0 0;font-size:13px;">Solicitud de Recuperación de Contraseña</p>
</td></tr>
<tr><td style="padding:36px 40px;">
  <p style="color:#374151;font-size:15px;margin:0 0 16px;">Hola <strong>' . htmlspecialchars($admin['nombre'] ?? 'Administrador') . '</strong>,</p>
  <p style="color:#374151;font-size:14px;line-height:1.6;margin:0 0 20px;">El usuario <strong>' . htmlspecialchars($user['username']) . '</strong>' . ($user['nombre'] ? ' (' . htmlspecialchars($user['nombre']) . ')' : '') . ' ha solicitado recuperar su contraseña el <strong>' . date('d/m/Y H:i') . '</strong>.</p>
  <div style="background:#fff8e1;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:14px 18px;margin-bottom:24px;">
    <p style="color:#92400e;font-size:13px;margin:0;line-height:1.6;"><strong>⚠️ Acción requerida:</strong><br>Por favor ingresa al panel de administración, ve a <strong>Sistema → Usuarios</strong>, restablece la contraseña del usuario y compártela con él de forma segura.</p>
  </div>
  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
    <tr><td align="center">
      <a href="' . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . htmlspecialchars($_SERVER['HTTP_HOST']) . '/admin/usuarios" style="display:inline-block;background:#C8102C;color:#fff;text-decoration:none;padding:12px 32px;border-radius:8px;font-weight:700;font-size:14px;">Ir a Gestión de Usuarios</a>
    </td></tr>
  </table>
</td></tr>
<tr><td style="background:#1a2332;padding:20px 40px;text-align:center;">
  <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">Sistema CMS — DIF San Mateo Atenco &nbsp;|&nbsp; Uso interno</p>
</td></tr>
</table></td></tr></table>
</body></html>';
                $mail->AltBody = "El usuario {$username} solicitó recuperar su contraseña el " . date('d/m/Y H:i') . ". Por favor restablécela desde el panel de administración.";
                $mail->send();
            } catch (\Exception $e) {
                // Fallo silencioso — no revelar al usuario
            }
        }
    }
}

$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Contraseña — DIF Admin</title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/admin.css?v=7">
<style>
body { background:#626268ff; display:flex; align-items:center; justify-content:center; min-height:100vh; }
body::before { content:''; position:fixed; inset:0; background:radial-gradient(ellipse at 20% 50%,rgba(200,16,44,.15) 0%,transparent 40%),linear-gradient(135deg,#1a1a2e 0%,#2d2d2d 50%,#1a1a2e 100%); z-index:0; }
.wrap { width:100%; max-width:420px; padding:1.5rem; position:relative; z-index:1; }
.card-r { background:rgba(255,255,255,.97); border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,.4); overflow:hidden; }
.hdr { background:linear-gradient(135deg,rgb(200,16,44) 0%,rgb(160,10,35) 100%); padding:2rem 2rem 3.5rem; text-align:center; position:relative; }
.hdr::after { content:''; position:absolute; bottom:-1px; left:0; right:0; height:40px; background:rgba(255,255,255,.97); border-radius:50% 50% 0 0/100% 100% 0 0; }
.hdr h1 { color:#fff; font-size:1.1rem; font-weight:700; margin:0 0 4px; }
.hdr p  { color:rgba(255,255,255,.75); font-size:.78rem; margin:0; }
.logo-w { display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,.15); border:2px solid rgba(255,255,255,.3); border-radius:50%; width:90px; height:90px; margin-bottom:1rem; }
.btn-send { background:linear-gradient(135deg,rgb(200,16,44) 0%,rgb(160,10,35) 100%); border:none; border-radius:10px; color:#fff; font-weight:600; font-size:.95rem; padding:.7rem; width:100%; cursor:pointer; }
</style>
</head>
<body>
<div class="wrap">
  <div class="card-r">
    <div class="hdr">
      <div class="logo-w mx-auto">
        <img src="../img/escudo.png" alt="DIF" style="max-height:62px;">
      </div>
      <h1>Recuperar Contraseña</h1>
      <p>DIF San Mateo Atenco</p>
    </div>
    <div class="p-4">
      <?php if ($success): ?>
        <div class="alert alert-success py-2 mb-3" style="font-size:.85rem;">
          <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($success) ?>
        </div>
        <div class="text-center">
          <a href="login" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver al login
          </a>
        </div>
      <?php else: ?>
        <?php if ($error): ?>
          <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <p class="text-muted mb-3" style="font-size:.88rem;">Ingresa tu nombre de usuario y notificaremos al administrador para que te comparta una nueva contraseña.</p>
        <form method="POST" action="olvide_password">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem;">Usuario</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-person text-secondary"></i></span>
              <input type="text" class="form-control" name="username" placeholder="Tu nombre de usuario" required autofocus>
            </div>
          </div>
          <button type="submit" class="btn-send mb-3">
            <i class="bi bi-envelope me-1"></i> Solicitar recuperación
          </button>
        </form>
        <div class="text-center">
          <a href="login" style="font-size:.8rem;color:#6b7280;text-decoration:none;">
            <i class="bi bi-arrow-left me-1"></i> Volver al login
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
