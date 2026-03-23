<?php
/**
 * acerca-del-dif/organigrama.php — Página de Organigrama del DIF San Mateo Atenco
 *
 * Consulta la tabla `organigrama` para obtener el PDF y título.
 * Si hay PDF, lo renderiza con <iframe>/<embed>.
 * Fallback a ../img/organigrama_dif_sma.jpg si no hay PDF en DB.
 *
 * Requisitos: 7.3, 7.4
 */

require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'acerca';
$page_title  = 'Organigrama — DIF San Mateo Atenco';

// ── Consultar registro de organigrama ────────────────────────────────────────
$organigrama = null;
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare('SELECT pdf_path, titulo FROM organigrama LIMIT 1');
    $stmt->execute();
    $organigrama = $stmt->fetch();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('organigrama.php PDOException: ' . $e->getMessage());
    }
}

// Determinar título y si hay PDF disponible
$titulo   = 'Organigrama 2025-2027';
$pdf_path = null;

if ($organigrama) {
    if (!empty($organigrama['titulo'])) {
        $titulo = $organigrama['titulo'];
    }
    if (!empty($organigrama['pdf_path'])) {
        $pdf_path = $organigrama['pdf_path'];
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <!-- Organigrama Start -->
    <div class="container py-5">
        <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
            <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgba(0,0,0,0.8);">
                Organigrama</h4>
            <div style="height:16px; background:rgb(200,16,44); border-radius:3px; width:23%; margin: 4px auto 24px;"></div>
        </div>
        <?php if ($pdf_path): ?>
            <!-- PDF embebido -->
            <div style="border:12px solid rgb(200,16,44);border-radius:0;overflow:hidden;padding:10px;background:#fff;">
                <div class="ratio ratio-16x9">
                    <iframe
                        src="<?= htmlspecialchars($base_path . $pdf_path, ENT_QUOTES, 'UTF-8') ?>"
                        type="application/pdf"
                        title="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            <noscript>
                <embed
                    src="<?= htmlspecialchars($base_path . $pdf_path, ENT_QUOTES, 'UTF-8') ?>"
                    type="application/pdf"
                    width="100%"
                    height="600px"
                    title="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>"
                />
            </noscript>
            <div class="text-center mt-3">
                <a href="<?= htmlspecialchars($base_path . $pdf_path, ENT_QUOTES, 'UTF-8') ?>"
                   class="btn btn-primary"
                   target="_blank"
                   rel="noopener noreferrer">
                    <i class="fas fa-download me-2"></i>Descargar PDF
                </a>
            </div>
        <?php else: ?>
            <!-- Fallback: imagen estática -->
            <div style="border:12px solid rgb(200,16,44);border-radius:0;overflow:hidden;padding:10px;background:#fff;">
                <img src="<?= $base_path ?>img/organigrama_dif_sma.jpg"
                     class="img-fluid w-100"
                     alt="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        <?php endif; ?>
    </div>
    <!-- Organigrama End -->

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
