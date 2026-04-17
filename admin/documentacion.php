<?php
/**
 * admin/documentacion.php — Visor de documentación del sistema
 */
require_once __DIR__ . '/auth_guard.php';

$doc = $_GET['doc'] ?? null;
$allowed_docs = [
    'manual'  => [
        'title'       => 'Manual de Usuario',
        'file'        => '../docs/Manual_Usuario_DIF.html',
        'pdf_name'    => 'Manual de Usuario — Sistema CMS DIF San Mateo Atenco.pdf',
        'filename'    => 'Manual_Usuario_DIF.pdf',
        'descripcion' => 'El Manual de Usuario del Sistema de Gestión de Contenido (CMS) del DIF San Mateo Atenco es una guía completa dirigida al personal administrativo autorizado. Cubre todos los módulos del panel de administración con instrucciones paso a paso, capturas de pantalla y recomendaciones de uso.',
        'icon'        => 'bi-journal-text',
    ],
    'tecnico' => [
        'title'       => 'Documento Técnico',
        'file'        => '../docs/Documento_Tecnico_DIF.html',
        'pdf_name'    => 'Documento Técnico  DIF San Mateo Atenco CMS.pdf',
        'filename'    => 'Documento_Tecnico_DIF.pdf',
        'descripcion' => 'El Documento Técnico describe la arquitectura del sistema, la estructura de la base de datos, los mecanismos de seguridad, la API interna, los módulos implementados y las guías de instalación y despliegue del CMS. Dirigido al equipo de desarrollo y soporte técnico.',
        'icon'        => 'bi-file-earmark-code',
    ],
];

// ── Enviar documento por correo ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'enviar_correo') {
    require_once __DIR__ . '/csrf.php';
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_message'] = 'Token inválido.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: documentacion'); exit;
    }

    $doc_key    = $_POST['doc_key'] ?? '';
    $destinatario = trim($_POST['destinatario'] ?? '');
    $nombre_dest  = trim($_POST['nombre_dest'] ?? '');
    $mensaje_extra = trim($_POST['mensaje_extra'] ?? '');

    if (!isset($allowed_docs[$doc_key]) || !filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = 'Datos inválidos. Verifica el correo destinatario.';
        $_SESSION['flash_type']    = 'warning';
        header('Location: documentacion'); exit;
    }

    $info    = $allowed_docs[$doc_key];
    $pdfPath = __DIR__ . '/../docs/pdf/' . $info['pdf_name'];

    if (!file_exists($pdfPath)) {
        $_SESSION['flash_message'] = 'El PDF no está disponible aún. Sube el archivo primero.';
        $_SESSION['flash_type']    = 'warning';
        header('Location: documentacion'); exit;
    }

    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../config.php';

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
        $mail->addAddress($destinatario, $nombre_dest ?: $destinatario);
        $mail->addAttachment($pdfPath, $info['filename']);
        $mail->isHTML(true);
        $mail->Subject = $info['title'] . ' — DIF San Mateo Atenco';

        $msg_extra_html = $mensaje_extra
            ? '<div style="background:#f8f9fa;border-left:4px solid #C8102C;border-radius:0 8px 8px 0;padding:14px 18px;margin:20px 0;font-size:13px;color:#374151;"><strong>Mensaje adicional:</strong><br>' . nl2br(htmlspecialchars($mensaje_extra)) . '</div>'
            : '';

        $remitente = htmlspecialchars($_SESSION['admin_username'] ?? 'El administrador');

        $mail->Body = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:\'Segoe UI\',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
  <!-- Header -->
  <tr><td style="background:linear-gradient(135deg,#1a2332 0%,#2d2d2d 60%,#C8102C 100%);padding:36px 40px;text-align:center;">
    <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;letter-spacing:-0.3px;">DIF San Mateo Atenco</h1>
    <p style="color:rgba(255,255,255,0.75);margin:8px 0 0;font-size:13px;">Sistema de Gestión de Contenido — Documentación Oficial</p>
  </td></tr>
  <!-- Body -->
  <tr><td style="padding:36px 40px;">
    <p style="color:#374151;font-size:15px;margin:0 0 18px;">Estimado/a ' . htmlspecialchars($nombre_dest ?: $destinatario) . ',</p>
    <p style="color:#374151;font-size:14px;line-height:1.7;margin:0 0 20px;">
      ' . $remitente . ' le hace llegar el siguiente documento oficial del <strong>Sistema de Gestión de Contenido del DIF San Mateo Atenco</strong>, Estado de México:
    </p>
    <!-- Doc info box -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
      <tr><td style="padding:20px 24px;">
        <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#C8102C;text-transform:uppercase;letter-spacing:1px;">Documento adjunto</p>
        <p style="margin:0 0 10px;font-size:17px;font-weight:800;color:#1a2332;">' . htmlspecialchars($info['title']) . '</p>
        <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.6;">' . htmlspecialchars($info['descripcion']) . '</p>
      </td></tr>
    </table>
    ' . $msg_extra_html . '
    <div style="background:#eff6ff;border-left:4px solid #3b82f6;border-radius:0 8px 8px 0;padding:12px 18px;margin-bottom:24px;">
      <p style="color:#1e40af;font-size:13px;margin:0;"><strong>📎 El documento se encuentra adjunto</strong> en formato PDF a este correo. Puede abrirlo con cualquier lector de PDF estándar.</p>
    </div>
    <p style="color:#9ca3af;font-size:12px;margin:0;">Este correo fue enviado desde el Panel de Administración del DIF San Mateo Atenco. Si recibió este mensaje por error, por favor ignórelo.</p>
  </td></tr>
  <!-- Footer -->
  <tr><td style="background:#1a2332;padding:22px 40px;text-align:center;">
    <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">
      Sistema CMS — DIF San Mateo Atenco &nbsp;|&nbsp; Estado de México, México<br>
      Uso interno — Confidencial &nbsp;|&nbsp; &copy; ' . date('Y') . ' DIF San Mateo Atenco
    </p>
  </td></tr>
</table>
</td></tr></table>
</body></html>';

        $mail->AltBody = "Estimado/a {$nombre_dest},\n\n{$remitente} le hace llegar el documento: {$info['title']}\n\n{$info['descripcion']}\n\nEl documento se adjunta en formato PDF.\n\n— DIF San Mateo Atenco";
        $mail->send();

        $_SESSION['flash_message'] = "Documento \"" . $info['title'] . "\" enviado correctamente a {$destinatario}.";
        $_SESSION['flash_type']    = 'success';
    } catch (\Exception $e) {
        $_SESSION['flash_message'] = 'Error al enviar el correo: ' . $e->getMessage();
        $_SESSION['flash_type']    = 'danger';
    }
    header('Location: documentacion'); exit;
}

// ── Descarga PDF pre-generado ──────────────────────────────────────────────────
if ($doc && isset($allowed_docs[$doc]) && isset($_GET['download'])) {
    $pdfs = [
        'manual'  => __DIR__ . '/../docs/pdf/Manual de Usuario — Sistema CMS DIF San Mateo Atenco.pdf',
        'tecnico' => __DIR__ . '/../docs/pdf/Documento Técnico  DIF San Mateo Atenco CMS.pdf',
    ];
    $pdfPath = $pdfs[$doc];
    if (file_exists($pdfPath)) {
        $filename = ($doc === 'manual') ? 'Manual_Usuario_DIF.pdf' : 'Documento_Tecnico_DIF.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: no-cache');
        readfile($pdfPath);
        exit;
    } else {
        $_SESSION['flash_message'] = 'El PDF aún no está disponible. Por favor contacte al administrador.';
        $_SESSION['flash_type'] = 'warning';
        header('Location: documentacion');
        exit;
    }
}

// ── Ver documento HTML ─────────────────────────────────────────────────────────
if ($doc && isset($allowed_docs[$doc])) {
    $path = __DIR__ . '/' . $allowed_docs[$doc]['file'];
    if (file_exists($path)) {
        header('Content-Type: text/html; charset=UTF-8');
        $html = file_get_contents($path);
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
        }
        echo $html;
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación — Panel de Administración DIF</title>
    <link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=7">
    <style>
        .doc-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: box-shadow .2s, transform .2s;
            background: #fff;
            text-decoration: none;
            color: #1a2332;
            display: block;
        }
        .doc-card:hover {
            box-shadow: 0 8px 32px rgba(200,16,44,0.13);
            transform: translateY(-4px);
            color: #1a2332;
        }
        .doc-card .doc-icon {
            font-size: 3.5rem;
            color: #C8102C;
            margin-bottom: 1rem;
        }
        .doc-card h5 { font-weight: 700; margin-bottom: .5rem; color: #1a2332; }
        .doc-card p  { font-size: .9rem; color: #6b7280; margin: 0; }
        .page-header {
            background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 40%, rgb(200,16,44) 100%);
            color: #ffffffff;
            border-radius: 14px;
            padding: 1.8rem 2rem;
            margin-bottom: 2rem;
        }
        .page-header h4 { font-weight: 700; margin: 0; color: #fff !important; }
    </style>
</head>
<body>
<div class="d-flex">
    <?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

    <div class="main-content">
        <nav class="navbar navbar-dark px-3">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar" aria-label="Abrir/cerrar menú">
                <i class="bi bi-list"></i>
            </button>
            <span class="navbar-brand mb-0 h6">Documentación</span>
            <a href="logout" class="btn btn-sm btn-outline-danger ms-auto">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
        </nav>

        <div class="container-fluid p-4">
            <div class="page-header">
                <h4><i class="bi bi-book me-2"></i>Documentación del Sistema</h4>
            </div>

            <?php
            require_once __DIR__ . '/csrf.php';
            $token = csrf_token();
            $flash = $_SESSION['flash_message'] ?? '';
            $ftype = $_SESSION['flash_type'] ?? '';
            unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            ?>
            <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($ftype) ?> alert-dismissible fade show mb-4">
                <?= htmlspecialchars($flash) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row g-4 justify-content-center">
                <?php foreach ($allowed_docs as $key => $info): ?>
                <div class="col-sm-6 col-md-4">
                    <a href="documentacion?doc=<?= $key ?>" target="_blank" class="doc-card">
                        <div class="doc-icon"><i class="bi <?= $info['icon'] ?>"></i></div>
                        <h5><?= htmlspecialchars($info['title']) ?></h5>
                        <p><?= htmlspecialchars($info['descripcion']) ?></p>
                    </a>
                    <div class="d-flex gap-2 mt-2">
                        <a href="documentacion?doc=<?= $key ?>&download=1" target="_blank"
                           class="btn btn-outline-danger flex-fill no-pdf-viewer"
                           onclick="window.open(this.href,'_blank');return false;">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Descargar
                        </a>
                        <button type="button" class="btn btn-outline-primary flex-fill"
                                data-bs-toggle="modal" data-bs-target="#modalEnviar<?= $key ?>">
                            <i class="bi bi-envelope me-1"></i> Enviar
                        </button>
                    </div>
                </div>

                <!-- Modal envío correo -->
                <div class="modal fade" id="modalEnviar<?= $key ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <form method="POST" action="documentacion">
                        <input type="hidden" name="action" value="enviar_correo">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                        <input type="hidden" name="doc_key" value="<?= $key ?>">
                        <div class="modal-header" style="background:linear-gradient(135deg,#1a2332,#C8102C);color:#fff;border-radius:0;">
                          <h5 class="modal-title fw-700">
                            <i class="bi bi-envelope me-2"></i>Enviar por correo
                          </h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                          <div class="d-flex align-items-center gap-3 mb-4 p-3" style="background:#f8f9fa;border-radius:10px;border:1px solid #e2e8f0;">
                            <i class="bi <?= $info['icon'] ?>" style="font-size:2rem;color:#C8102C;flex-shrink:0;"></i>
                            <div>
                              <div class="fw-bold" style="color:#1a2332;"><?= htmlspecialchars($info['title']) ?></div>
                              <div style="font-size:.8rem;color:#6b7280;"><?= htmlspecialchars(substr($info['descripcion'], 0, 90)) ?>...</div>
                              <div style="font-size:.75rem;color:#9ca3af;margin-top:3px;"><i class="bi bi-paperclip me-1"></i><?= htmlspecialchars($info['filename']) ?></div>
                            </div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre del destinatario</label>
                            <div class="input-group">
                              <span class="input-group-text bg-white"><i class="bi bi-person text-secondary"></i></span>
                              <input type="text" class="form-control" name="nombre_dest" placeholder="Ej: María García López">
                            </div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
                            <div class="input-group">
                              <span class="input-group-text bg-white"><i class="bi bi-envelope text-secondary"></i></span>
                              <input type="email" class="form-control" name="destinatario" required placeholder="correo@ejemplo.com">
                            </div>
                          </div>
                          <div class="mb-1">
                            <label class="form-label fw-semibold">Mensaje adicional <span class="text-muted fw-normal">(opcional)</span></label>
                            <textarea class="form-control" name="mensaje_extra" rows="3"
                                      placeholder="Escribe un mensaje personalizado que aparecerá en el cuerpo del correo..."
                                      style="resize:none;"></textarea>
                          </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                          <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Enviar documento
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
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
    if (closeBtn) closeBtn.addEventListener('click', function () { sidebar.classList.add('collapsed'); });
</script>
</body>
</html>
