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
    header('Location: dashboard.php');
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
            $stmt = $pdo->prepare('SELECT id, username, password FROM admin WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            // 3. Verificar contraseña con password_verify()
            if ($admin && password_verify($password, $admin['password'])) {
                // 4. Éxito: iniciar sesión y redirigir
                session_regenerate_id(true);
                $_SESSION['admin_logged'] = true;
                header('Location: dashboard.php');
                exit;
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
    <link rel="stylesheet" href="../css/admin.css?v=6">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #7a7777ff;
            position: relative;
            overflow: hidden;
        }
        /* Imagen institucional de fondo centrada y transparente */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: url('../img/institucion.png') center center / contain no-repeat;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
            position: relative;
            z-index: 1;
        }
        .login-card .card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        .login-logo {
            max-height: 80px;
        }
        .login-card .card-title {
            color: #2d2d2d;
            font-weight: 700;
        }
        .login-card .form-control:focus {
            border-color: rgb(200,16,44);
            box-shadow: 0 0 0 3px rgba(200,16,44,0.15);
        }
        .login-card .btn-primary {
            background: rgb(200,16,44) !important;
            border-color: rgb(200,16,44) !important;
            font-weight: 600;
            padding: 0.6rem;
            border-radius: 10px;
            transition: all 0.2s;
        }
        .login-card .btn-primary:hover {
            background: rgb(160,10,35) !important;
            border-color: rgb(160,10,35) !important;
            box-shadow: 0 4px 16px rgba(200,16,44,0.35);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <img src="../img/escudo.png" alt="DIF" class="login-logo mb-2">
                    <h5 class="card-title mb-0">Panel de Administración</h5>
                    <small class="text-muted">DIF SAN MATEO ATENCO</small>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['expired'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-clock-history me-1"></i> Tu sesión expiró por inactividad. Inicia sesión de nuevo.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input
                            type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=13"></script>
</body>
</html>
