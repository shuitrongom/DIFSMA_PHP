<?php
ob_start();
/**
 * admin/login.php — Panel de administración: inicio de sesión
 *
 * Requirements: 1.1, 1.2, 1.3, 1.6
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/csrf.php';

// Iniciar sesión si no está activa (csrf.php ya lo hace, pero por seguridad)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigir si ya está autenticado
if (($_SESSION['admin_logged'] ?? false) === true) {
    header('Location: dashboard');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token    = $_POST['csrf_token'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Validar token CSRF
    if (!csrf_validate($token)) {
        $error = 'Solicitud inválida. Por favor recarga la página e intenta de nuevo.';
    } else {
        // 2. Rate limiting: verificar intentos fallidos por IP en los últimos 15 minutos
        $pdo = null;
        $ip  = $_SERVER['REMOTE_ADDR'];
        try {
            $pdo = get_db();
            $stmtRate = $pdo->prepare(
                'SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)'
            );
            $stmtRate->execute([$ip]);
            $attempts = (int) $stmtRate->fetchColumn();

            if ($attempts >= 5) {
                $error = 'Demasiados intentos fallidos. Por favor espere 15 minutos antes de intentar de nuevo.';
            }
        } catch (PDOException $e) {
            // Si falla la consulta de rate limiting, permitir continuar (fail open)
        }

        // 3. Consultar admin por username con PDO prepare/execute (solo si no hay bloqueo)
        if ($error === '') try {
            if ($pdo === null) {
                $pdo = get_db();
            }
            $stmt = $pdo->prepare('SELECT id, username, password, rol, activo FROM admin WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            // 3. Verificar contraseña con password_verify()
            if ($admin && password_verify($password, $admin['password'])) {
                // Verificar que el usuario esté activo
                if (isset($admin['activo']) && !$admin['activo']) {
                    $error = 'Tu cuenta está desactivada. Contacta al administrador.';
                } else {
                    // Éxito: iniciar sesión y redirigir
                    session_regenerate_id(true);
                    $_SESSION['admin_logged'] = true;
                    $_SESSION['admin_id'] = (int) $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_rol'] = $admin['rol'] ?? 'admin';
                    // Registrar login en historial
                    try {
                        require_once __DIR__ . '/../includes/db.php';
                        require_once __DIR__ . '/historial_helper.php';
                        registrar_historial(get_db(), 'login', 'Sistema', 'Inicio de sesion: ' . $admin['username']);
                    } catch (Exception $e) {}
                    header('Location: dashboard');
                    exit;
                }
            } else {
                // 5. Fallo: registrar intento

                // Insertar en login_attempts
                $ins = $pdo->prepare('INSERT INTO login_attempts (ip, attempted_at) VALUES (?, NOW())');
                $ins->execute([$ip]);

                // Registrar en logs/login_attempts.log
                $logLine = sprintf(
                    "[%s] IP: %s — intento fallido para usuario: %s\n",
                    date('Y-m-d H:i:s'),
                    $ip,
                    $username
                );
                $logFile = defined('LOGS_PATH') ? LOGS_PATH . '/login_attempts.log' : __DIR__ . '/../logs/login_attempts.log';
                @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

                $error = 'Usuario o contraseña incorrectos';
            }
        } catch (PDOException $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                $error = 'Error de base de datos: ' . htmlspecialchars($e->getMessage());
            } else {
                $error = 'Error interno. Por favor intenta más tarde.';
            }
        }
    }
}

// Generar nuevo token CSRF para el formulario
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración — DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            background: #626268ff;
            position: relative;
            overflow: hidden;
        }

        /* Fondo con gradiente y patrón sutil */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(200,16,44,0.15) 0%, transparent 40%),
                radial-gradient(ellipse at 80% 20%, rgba(45,45,45,0.8) 0%, transparent 40%),
                linear-gradient(135deg, #1a1a2e 0%, #2d2d2d 50%, #1a1a2e 100%);
            z-index: 0;
        }

        /* Imagen institucional de fondo */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: url('../img/institucion.png') center center / cover no-repeat;
            opacity: 0.04;
            z-index: 0;
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* Card principal */
        .login-card {
            background: rgba(255,255,255,0.97);
            border-radius: 20px;
            box-shadow:
                0 25px 60px rgba(0,0,0,0.4),
                0 0 0 1px rgba(255,255,255,0.1);
            overflow: hidden;
        }

        /* Banda roja superior */
        .login-header {
            background: linear-gradient(135deg, rgb(200,16,44) 0%, rgb(160,10,35) 100%);
            padding: 2rem 2rem 3.5rem;
            text-align: center;
            position: relative;
        }
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 40px;
            background: rgba(255,255,255,0.97);
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }

        /* Logo circular */
        .login-logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            width: 100px;
            height: 100px;
            margin-bottom: 1rem;
            box-shadow:
                0 8px 32px rgba(0,0,0,0.2),
                inset 0 1px 0 rgba(255,255,255,0.2);
            transition: transform 0.3s ease;
        }
        .login-logo-wrap:hover { transform: scale(1.05); }
        .login-logo {
            max-height: 72px;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.3)) brightness(1.1);
        }
        .login-header h1 {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 4px;
            letter-spacing: 0.3px;
        }
        .login-header p {
            color: rgba(255,255,255,0.75);
            font-size: 0.78rem;
            margin: 0;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Cuerpo del formulario */
        .login-body {
            padding: 1.5rem 2rem 2rem;
        }

        .login-body .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            letter-spacing: 0.2px;
        }
        .login-body .form-control {
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.9rem;
            color: #111827;
            background: #f9fafb;
            transition: all 0.2s;
        }
        .login-body .form-control:focus {
            border-color: rgb(200,16,44);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(200,16,44,0.12);
            outline: none;
        }
        .login-body .form-control::placeholder {
            color: #9ca3af;
            font-style: italic;
        }

        /* Botón */
        .btn-login {
            background: linear-gradient(135deg, rgb(200,16,44) 0%, rgb(160,10,35) 100%);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.7rem;
            width: 100%;
            letter-spacing: 0.3px;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(200,16,44,0.3);
            cursor: pointer;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, rgb(180,14,40) 0%, rgb(140,8,30) 100%);
            box-shadow: 0 6px 20px rgba(200,16,44,0.45);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }

        /* Footer del login */
        .login-footer {
            text-align: center;
            padding: 0 2rem 1.5rem;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-wrapper { padding: 1rem; }
            .login-body { padding: 1.25rem 1.25rem 1.5rem; }
            .login-header { padding: 1.5rem 1.5rem 3rem; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Header rojo con logo -->
            <div class="login-header">
                <div class="login-logo-wrap mx-auto">
                    <img src="../img/escudo.png" alt="DIF San Mateo Atenco" class="login-logo">
                </div>
                <h1>Panel de Administración</h1>
                <p>DIF San Mateo Atenco</p>
            </div>

            <!-- Formulario -->
            <div class="login-body">

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert" style="font-size:0.85rem;border-radius:10px;">
                        <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['expired'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show py-2 mb-3" role="alert" style="font-size:0.85rem;border-radius:10px;">
                        <i class="bi bi-clock-history me-1"></i> Sesión expirada por inactividad.
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Ingresa tu usuario"
                               autocomplete="username" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Ingresa tu contraseña"
                               autocomplete="current-password" required>
                    </div>

                    <button type="submit" class="btn-login">
                        Iniciar sesión
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="olvide_password" style="font-size:0.8rem;color:#6b7280;text-decoration:none;">
                        <i class="bi bi-question-circle me-1"></i>¿Olvidaste tu contraseña?
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                &copy; <?= date('Y') ?> DIF San Mateo Atenco &mdash; Sistema de Gestión de Contenido
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
