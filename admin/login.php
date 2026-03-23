<?php
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
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
        }
        .login-logo {
            max-height: 80px;
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
                    <small class="text-muted">DIF Municipal</small>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
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
</body>
</html>
