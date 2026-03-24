<?php
/**
 * transparencia/conac.php — CONAC
 * Bloques por año → Conceptos → PDFs por trimestre (misma estructura que SEAC)
 */
require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'transparencia';
$page_title  = 'CONAC — DIF San Mateo Atenco';

$bloques       = [];
$conceptos_map = [];
$pdfs_map      = [];

try {
    $pdo = get_db();
    $bloques = $pdo->query('SELECT id, anio, orden FROM conac_bloques ORDER BY anio DESC')->fetchAll();
    $stmt = $pdo->query('SELECT id, bloque_id, numero, nombre, orden FROM conac_conceptos ORDER BY orden ASC');
    while ($row = $stmt->fetch()) { $conceptos_map[(int)$row['bloque_id']][] = $row; }
    $stmt = $pdo->query('SELECT bloque_id, concepto_id, trimestre, pdf_path FROM conac_pdfs WHERE pdf_path IS NOT NULL AND pdf_path != ""');
    while ($row = $stmt->fetch()) { $pdfs_map[(int)$row['bloque_id']][(int)$row['concepto_id']][(int)$row['trimestre']] = $row['pdf_path']; }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('conac.php PDOException: ' . $e->getMessage());
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <link rel="stylesheet" href="<?= $base_path ?>css/acordeon.css">
    <link rel="stylesheet" href="<?= $base_path ?>css/tablas.css">
    <link rel="stylesheet" href="<?= $base_path ?>css/botonarriba.css">

    <div class="container-fluid service py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    CONAC</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>
            <div class="row g-5">
                <section id="team" class="dark">
                    <br>
                    <div class="container mx-auto text-center wow fadeIn">
                        <div class="overflow-x:scroll">

<?php if (empty($bloques)): ?>
                            <p class="text-muted">No hay bloques CONAC disponibles.</p>
<?php else: ?>
            <!-- Bloques como botones en grid de 3 columnas -->
            <div class="conac-buttons-grid">
    <?php foreach ($bloques as $bloque): ?>
                <div class="conac-btn-wrapper">
                    <div class="conac-year-btn" data-target="conacPanel<?= (int)$bloque['id'] ?>">
                        <span class="conac-year-text"><?= htmlspecialchars($bloque['anio'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
    <?php endforeach; ?>
            </div>

            <!-- Paneles expandibles (uno por bloque) -->
    <?php foreach ($bloques as $bloque): ?>
            <div class="conac-panel" id="conacPanel<?= (int)$bloque['id'] ?>" style="display:none;">
                <div class="question-text-div" style="background:#ccc;color:#333;">CONAC <?= htmlspecialchars($bloque['anio'], ENT_QUOTES, 'UTF-8') ?></div>
                <br>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Concepto</th>
                                <th scope="col"><p>1er. Trimestre&nbsp;</p></th>
                                <th scope="col"><p>2do. Trimestre&nbsp;</p></th>
                                <th scope="col"><p>3er.&nbsp; Trimestre&nbsp;</p></th>
                                <th scope="col"><p>4to.&nbsp; &nbsp;Trimestre</p></th>
                            </tr>
                        </thead>
                        <tbody>
        <?php
            $bloque_conceptos = $conceptos_map[(int)$bloque['id']] ?? [];
            foreach ($bloque_conceptos as $concepto):
        ?>
                            <tr>
                                <td><?= htmlspecialchars($concepto['numero'] . '.- ' . $concepto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
            <?php for ($trim = 1; $trim <= 4; $trim++): ?>
                                <td>
                <?php
                    $pdf_path = $pdfs_map[(int)$bloque['id']][(int)$concepto['id']][$trim] ?? null;
                    if ($pdf_path):
                        $pdf_url = htmlspecialchars($base_path . $pdf_path, ENT_QUOTES, 'UTF-8');
                ?>
                                    <img src="<?= $base_path ?>img/pdf-download2.jpg" alt="Ver PDF"
                                        class="img-thumbnail pdf-trigger" style="cursor:pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#pdfModal"
                                        data-pdf="<?= $pdf_url ?>">
                <?php else: ?>
                                    &nbsp;
                <?php endif; ?>
                                </td>
            <?php endfor; ?>
                            </tr>
        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    <?php endforeach; ?>
<?php endif; ?>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="pleca">
        <img src="<?= $base_path ?>img/pleca.png" alt="pleca">
    </div>

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
    /* Grid de botones de año: 3 columnas web, 2 tablet, 1 móvil */
    .conac-buttons-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 22px;
        margin-bottom: 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    .conac-btn-wrapper { width: calc(33.333% - 15px); }
    @media (max-width: 768px) {
        .conac-btn-wrapper { width: calc(50% - 11px); }
        .conac-year-btn { min-height: 70px; }
        .conac-year-text { font-size: clamp(15px, 2.5vw, 22px); }
    }
    @media (max-width: 576px) {
        .conac-buttons-grid { max-width: 100%; padding: 0; }
        .conac-btn-wrapper { width: 100%; margin: 0 auto; padding-left: 15%; }
        .conac-year-btn { min-height: 55px; background-position: left center; }
        .conac-year-text { font-size: clamp(16px, 5vw, 22px); width: 55%; padding-left: 0; }
        .conac-panel .table { font-size: 11px; }
        .conac-panel .table th p { margin: 0; font-size: 10px; }
        .conac-panel .table .img-thumbnail { max-width: 30px; padding: 2px; }
        .conac-panel .question-text-div { font-size: 13px; padding: 8px; }
    }
    .conac-year-btn {
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
    .conac-year-btn:hover,
    .conac-year-btn.active {
        /* Sin hover ni sombra */
    }
    .conac-year-text {
        font-family: "Montserrat", sans-serif;
        font-weight: 700;
        font-size: clamp(18px, 2.2vw, 30px);
        color: #fff;
        text-align: center;
        width: 42%;
        padding-left: 3%;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }
    .conac-panel { margin-bottom: 20px; }
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
        var yearBtns = document.querySelectorAll('.conac-year-btn');
        yearBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-target');
                var panel = document.getElementById(targetId);
                var isOpen = btn.classList.contains('active');
                yearBtns.forEach(function (b) {
                    b.classList.remove('active');
                    var p = document.getElementById(b.getAttribute('data-target'));
                    if (p) p.style.display = 'none';
                });
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
