<?php
/**
 * transparencia/cuenta_publica.php —  Cuenta Pública
 *
 * Estructura: Bloques (año) →  Títulos (módulos) →  Conceptos con PDF directo
 */

require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'transparencia';
$page_title  = 'Cuenta Pública —  DIF San Mateo Atenco';

//  "? "? Consultar datos  "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "? "?
$bloques       = [];
$titulos_map   = [];
$conceptos_map = [];

try {
    $pdo = get_db();

    $stmt = $pdo->query('SELECT id, anio, orden FROM cp_bloques ORDER BY anio DESC');
    $bloques = $stmt->fetchAll();

    $stmt = $pdo->query('SELECT id, bloque_id, nombre, orden FROM cp_titulos ORDER BY orden ASC');
    while ($row = $stmt->fetch()) {
        $titulos_map[(int)$row['bloque_id']][] = $row;
    }

    $stmt = $pdo->query('SELECT id, titulo_id, numero, nombre, pdf_path, orden FROM cp_conceptos ORDER BY orden ASC');
    while ($row = $stmt->fetch()) {
        $conceptos_map[(int)$row['titulo_id']][] = $row;
    }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('cuenta_publica.php PDOException: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <link rel="stylesheet" href="<?= $base_path ?>css/acordeon.css">
    <link rel="stylesheet" href="<?= $base_path ?>css/tablas.css">
    <link rel="stylesheet" href="<?= $base_path ?>css/botonarriba.css">

    <!-- Cuenta Pública Content Start -->
    <div class="container-fluid service py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    Cuenta Pública</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>
            <div class="row g-5">
                <section id="team" class="dark">
                    <br>
                    <div class="container mx-auto text-center wow fadeIn">
                        <div class="overflow-x:scroll">

<?php if (empty($bloques)): ?>
                            <p class="text-muted">No hay bloques de Cuenta Pública disponibles.</p>
<?php else: ?>
            <!-- Bloques como botones en grid de 3 columnas -->
            <div class="cp-buttons-grid">
    <?php foreach ($bloques as $bloque): ?>
                <div class="cp-btn-wrapper">
                    <div class="cp-year-btn" data-target="cpPanel<?= (int)$bloque['id'] ?>">
                        <span class="cp-year-text"><?= htmlspecialchars($bloque['anio'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
    <?php endforeach; ?>
            </div>

            <!-- Paneles expandibles (uno por bloque) -->
    <?php foreach ($bloques as $bloque): ?>
            <div class="cp-panel" id="cpPanel<?= (int)$bloque['id'] ?>" style="display:none;">
                <div class="question-text-div" style="background:#ccc;color:#333;">Cuenta Pública <?= htmlspecialchars($bloque['anio'], ENT_QUOTES, 'UTF-8') ?></div>
                <br>
        <?php
            $bloque_titulos = $titulos_map[(int)$bloque['id']] ?? [];
            if (empty($bloque_titulos)):
        ?>
                <p class="text-muted py-3">No hay módulos registrados para este año.</p>
        <?php else: ?>
            <?php foreach ($bloque_titulos as $titulo): ?>
                <div class="question-text-div" style="background:#333;"><?= htmlspecialchars($titulo['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                <br>
                <?php
                    $titulo_conceptos = $conceptos_map[(int)$titulo['id']] ?? [];
                    if (!empty($titulo_conceptos)):
                ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align:left;padding-left:0.5rem;">Concepto</th>
                                <th scope="col" style="width:100px;text-align:center;padding-right:2.5rem;">PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php foreach ($titulo_conceptos as $concepto): ?>
                            <tr>
                                <td style="text-align:left;padding-left:0.5rem;"><?= htmlspecialchars($concepto['numero'] . '.- ' . $concepto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="text-align:right;padding-right:2.5rem;">
                        <?php if (!empty($concepto['pdf_path'])): ?>
                                    <img src="<?= $base_path ?>img/pdf-download2.jpg" alt="Ver PDF"
                                        class="img-thumbnail pdf-trigger" style="cursor:pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#pdfModal"
                                        data-pdf="<?= htmlspecialchars("{$base_path}{$concepto['pdf_path']}", ENT_QUOTES, 'UTF-8') ?>">
                        <?php else: ?>
                                    &nbsp;
                        <?php endif; ?>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <br>
            <?php endforeach; ?>
        <?php endif; ?>
            </div>
    <?php endforeach; ?>
<?php endif; ?>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- Cuenta Pública Content End -->

    <!-- Pleca Start -->
    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>
    <!-- Pleca End -->

    <!-- Modal para mostrar PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-height:95vh;">
            <div class="modal-content" style="height:90vh;">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Documento PDF</h5>
                    <span id="pdfPageInfo" style="margin-left:auto;margin-right:1rem;font-size:.85rem;color:#888;"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="pdfContainer" style="overflow-y:auto;padding:8px;background:#525659;text-align:center;"></div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <style>
    /* Grid de botones de año: 3 columnas (igual que SEAC) */
    .cp-buttons-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 22px;
        margin-bottom: 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    .cp-btn-wrapper { width: calc(33.333% - 15px); }
    @media (max-width: 768px) {
        .cp-btn-wrapper { width: calc(50% - 11px); }
        .cp-year-btn { min-height: 70px; }
        .cp-year-text { font-size: clamp(15px, 2.5vw, 22px); }
    }
    @media (max-width: 576px) {
        .cp-buttons-grid { max-width: 100%; padding: 0; }
        .cp-btn-wrapper { width: 100%; margin: 0 auto; padding-left: 15%; }
        .cp-year-btn { min-height: 55px; background-position: left center; }
        .cp-year-text { font-size: clamp(16px, 5vw, 22px); width: 55%; padding-left: 0; }
        .cp-panel .table { font-size: 11px; }
        .cp-panel .table th p,
        .cp-panel .table th { margin: 0; font-size: 10px; }
        .cp-panel .table .img-thumbnail { max-width: 30px; padding: 2px; }
        .cp-panel .question-text-div { font-size: 13px; padding: 8px; }
    }
    .cp-year-btn {
        background: url(<?= $base_path ?>img/bloque_botone_dif.png) no-repeat left center;
        background-size: contain;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        cursor: pointer;
        min-height: 100px;
        aspect-ratio: 4 / 1;
    }
    .cp-year-btn:hover,
    .cp-year-btn.active {
        /* Sin hover ni sombra */
    }
    .cp-year-text {
        font-family: "Montserrat", sans-serif;
        font-weight: 700;
        font-size: clamp(18px, 2.2vw, 30px);
        color: #fff;
        text-align: center;
        width: 42%;
        padding-left: 3%;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }
    .cp-panel { margin-bottom: 20px; }
    @media print { body.pdf-modal-open * { display:none!important; visibility:hidden!important; } }
    body.pdf-modal-open { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
    #pdfContainer canvas { display:block; margin:6px auto; box-shadow:0 1px 4px rgba(0,0,0,.3); max-width:900px; }
    #pdfContainer { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
    </style>

    <script src="<?= $base_path ?>lib/pdfjs/pdf.min.js"></script>
    <script>
    (function () {
        pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= $base_path ?>lib/pdfjs/pdf.worker.min.js';

        // ── Acordeón: click en botón de año abre/cierra panel ──
        var yearBtns = document.querySelectorAll('.cp-year-btn');
        yearBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-target');
                var panel = document.getElementById(targetId);
                var isOpen = btn.classList.contains('active');

                // Cerrar todos
                yearBtns.forEach(function (b) {
                    b.classList.remove('active');
                    var p = document.getElementById(b.getAttribute('data-target'));
                    if (p) p.style.display = 'none';
                });

                // Si no estaba abierto, abrir este
                if (!isOpen && panel) {
                    btn.classList.add('active');
                    panel.style.display = 'block';
                }
            });
        });

        var pdfModal = document.getElementById('pdfModal');
        if (!pdfModal) return;
        var container = document.getElementById('pdfContainer');
        var pageInfo = document.getElementById('pdfPageInfo');

        pdfModal.addEventListener('shown.bs.modal', function (event) {
            var url = pdfModal._pdfUrl;
            if (!url) return;
            document.body.classList.add('pdf-modal-open');
            container.innerHTML = '<p style="color:#fff;padding:2rem;">Cargando PDF...</p>';
            var loadingTask = pdfjsLib.getDocument(url);
            pdfModal._loadingTask = loadingTask;
            loadingTask.promise.then(function(pdf) {
                container.innerHTML = '';
                pageInfo.textContent = 'Páginas: ' + pdf.numPages;
                var maxW = Math.min(container.clientWidth - 16, 900);
                var chain = Promise.resolve();
                for (var i = 1; i <= pdf.numPages; i++) {
                    (function(num) {
                        chain = chain.then(function() {
                            return pdf.getPage(num).then(function(page) {
                                var scale = maxW / page.getViewport({scale:1}).width;
                                var vp = page.getViewport({scale: scale});
                                var c = document.createElement('canvas');
                                c.width = Math.floor(vp.width);
                                c.height = Math.floor(vp.height);
                                container.appendChild(c);
                                return page.render({canvasContext: c.getContext('2d'), viewport: vp}).promise;
                            });
                        });
                    })(i);
                }
            }).catch(function(err) {
                console.error('PDF error:', err);
                container.innerHTML = '<p style="color:#fff;padding:2rem;">Error al cargar el PDF: ' + err.message + '</p>';
            });
        });

        pdfModal.addEventListener('show.bs.modal', function (event) {
            pdfModal._pdfUrl = event.relatedTarget.getAttribute('data-pdf');
        });

        pdfModal.addEventListener('hidden.bs.modal', function () {
            container.innerHTML = '';
            pageInfo.textContent = '';
            if (pdfModal._loadingTask) {
                pdfModal._loadingTask.destroy();
                pdfModal._loadingTask = null;
            }
            document.body.classList.remove('pdf-modal-open');
        });

        document.addEventListener('keydown', function (e) {
            if (!document.body.classList.contains('pdf-modal-open')) return;
            if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 's' || e.key === 'c' || e.key === 'a')) {
                e.preventDefault(); e.stopImmediatePropagation(); return false;
            }
            if (e.key === 'PrintScreen') {
                e.preventDefault(); try { navigator.clipboard.writeText(''); } catch(err) {} return false;
            }
        }, true);
        document.addEventListener('keyup', function (e) {
            if (!document.body.classList.contains('pdf-modal-open') || e.key !== 'PrintScreen') return;
            try { navigator.clipboard.writeText(''); } catch(err) {}
        }, true);
        document.addEventListener('contextmenu', function (e) {
            if (document.body.classList.contains('pdf-modal-open')) { e.preventDefault(); return false; }
        });
        container.addEventListener('dragstart', function(e) { e.preventDefault(); });
        container.addEventListener('selectstart', function(e) { e.preventDefault(); });
    })();
    </script>
