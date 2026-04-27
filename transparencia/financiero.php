<?php
/**
 * transparencia/financiero.php — Financiero
 * Bloques por año → Conceptos con PDF directo
 */
require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'transparencia';
$page_title  = 'Financiero — DIF San Mateo Atenco';

// Verificar mantenimiento centralizado
$pagina_key = 'financiero';
require_once __DIR__ . '/../includes/mantenimiento_check.php';

$bloques       = [];
$conceptos_map = [];

try {
    $pdo = get_db();
    $bloques = $pdo->query('SELECT id, anio, orden FROM fin_bloques ORDER BY anio ASC')->fetchAll();
    $stmt = $pdo->query('SELECT id, bloque_id, numero, nombre, pdf_path, orden FROM fin_conceptos ORDER BY orden ASC');
    while ($row = $stmt->fetch()) { $conceptos_map[(int)$row['bloque_id']][] = $row; }
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('financiero.php PDOException: ' . $e->getMessage());
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
                    Financiero</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>
<?php if (empty($bloques)): ?>
            <p class="text-muted">No hay bloques de Financiero disponibles.</p>
<?php else: ?>
            <!-- Bloques como botones en grid de 3 columnas -->
            <div class="fin-buttons-grid">
    <?php foreach ($bloques as $bloque): ?>
                    <div class="fin-year-btn" data-target="finPanel<?= (int)$bloque['id'] ?>">
                        <span class="fin-year-text"><?= htmlspecialchars($bloque['anio'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
    <?php endforeach; ?>
            </div>

            <!-- Paneles expandibles (uno por bloque) -->
    <?php foreach ($bloques as $bloque): ?>
            <div class="fin-panel" id="finPanel<?= (int)$bloque['id'] ?>" style="display:none;">
                <div class="question-text-div" style="background:#ccc;color:#333;">FINANCIERO <?= htmlspecialchars($bloque['anio'], ENT_QUOTES, 'UTF-8') ?></div>
                <br>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align:left;padding-left:0.5rem;">Concepto</th>
                                <th scope="col" style="width:100px;text-align:center;">Archivo</th>
                            </tr>
                        </thead>
                        <tbody>
        <?php
            $bloque_conceptos = $conceptos_map[(int)$bloque['id']] ?? [];
            if (empty($bloque_conceptos)):
        ?>
                            <tr><td colspan="2" class="text-muted text-center py-3">No hay conceptos registrados.</td></tr>
        <?php else: ?>
            <?php foreach ($bloque_conceptos as $concepto): ?>
                            <tr>
                                <td style="text-align:left;padding-left:0.5rem;"><?= htmlspecialchars($concepto['numero'] . '.- ' . $concepto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="text-align:center;">
                <?php if (!empty($concepto['pdf_path'])): ?>
                                    <img src="<?= $base_path ?>img/pdf-download2.jpg" alt="Ver PDF"
                                        class="img-thumbnail pdf-trigger" style="cursor:pointer;max-height:55px;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#pdfModal"
                                        data-pdf="<?= htmlspecialchars("{$base_path}{$concepto['pdf_path']}", ENT_QUOTES, 'UTF-8') ?>">
                <?php else: ?>
                                    &nbsp;
                <?php endif; ?>
                                </td>
                            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    <?php endforeach; ?>
<?php endif; ?>

        </div>
    </div>

    <div class="pleca"><img src="<?= $base_path ?>img/pleca.png" alt="pleca"></div>

    <!-- Modal PDF -->
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
    .fin-buttons-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        justify-items: center;
        gap: 22px;
        margin-bottom: 20px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }
    .fin-year-btn:only-child { grid-column: 1 / span 2; justify-self: center; width: 45%; }
    @media (max-width: 768px) {
        .fin-btn-wrapper { width: calc(50% - 11px); }
        .fin-year-btn { min-height: 70px; }
        .fin-year-text { font-size: clamp(15px, 2.5vw, 22px); }
    }
    @media (max-width: 576px) {
        .fin-buttons-grid { grid-template-columns: 1fr; max-width: 100%; padding: 0; }
        .fin-year-btn { min-height: 55px; background-position: left center; }
        .fin-year-text { font-size: clamp(16px, 5vw, 22px); width: 55%; padding-left: 0; }
        .fin-panel .table { font-size: 11px; }
        .fin-panel .table th { font-size: 10px; }
        .fin-panel .table .img-thumbnail { max-width: 30px; padding: 2px; }
        .fin-panel .question-text-div { font-size: 13px; padding: 8px; }
    }
    .fin-year-btn {
        background: url(<?= $base_path ?>img/bloque_botone_dif.png) no-repeat center center;
        background-size: contain;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        cursor: pointer;
        min-height: 80px;
        width: 100%;
        max-width: 650px;
        aspect-ratio: 4 / 1;
    }
    .fin-year-btn:hover,
    .fin-year-btn.active {
        /* Sin hover ni sombra */
    }
    .fin-year-text {
        font-family: "Montserrat", sans-serif;
        font-weight: 700;
        font-size: clamp(18px, 2.2vw, 30px);
        color: #fff;
        text-align: center;
        width: 75%;
        padding-left: 8%;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }
    .fin-panel { margin-bottom: 20px; }
    @media print { body.pdf-modal-open * { display:none!important; visibility:hidden!important; } }
    body.pdf-modal-open { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
    #pdfContainer canvas { display:block; margin:6px auto; box-shadow:0 1px 4px rgba(0,0,0,.3); max-width:900px; }
    #pdfContainer { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
    </style>

    <script src="<?= $base_path ?>lib/pdfjs/pdf.min.js"></script>
    <script>
    (function(){
        pdfjsLib.GlobalWorkerOptions.workerSrc='<?= $base_path ?>lib/pdfjs/pdf.worker.min.js';

        var yearBtns=document.querySelectorAll('.fin-year-btn');
        yearBtns.forEach(function(btn){
            btn.addEventListener('click',function(){
                var targetId=btn.getAttribute('data-target');
                var panel=document.getElementById(targetId);
                var isOpen=btn.classList.contains('active');
                yearBtns.forEach(function(b){
                    b.classList.remove('active');
                    var p=document.getElementById(b.getAttribute('data-target'));
                    if(p)p.style.display='none';
                });
                if(!isOpen&&panel){
                    btn.classList.add('active');
                    panel.style.display='block';
                }
            });
        });

        var m=document.getElementById('pdfModal');
        if(!m)return;
        var container=document.getElementById('pdfContainer');
        var pageInfo=document.getElementById('pdfPageInfo');

        m.addEventListener('shown.bs.modal',function(){
            var url=m._pdfUrl; if(!url)return;
            document.body.classList.add('pdf-modal-open');
            container.innerHTML='<p style="color:#fff;padding:2rem;">Cargando PDF...</p>';
            var loadingTask=pdfjsLib.getDocument(url);
            m._loadingTask=loadingTask;
            loadingTask.promise.then(function(pdf){
                container.innerHTML='';
                pageInfo.textContent='Páginas: '+pdf.numPages;
                var maxW=Math.min(container.clientWidth-16,900);
                var chain=Promise.resolve();
                for(var i=1;i<=pdf.numPages;i++){
                    (function(num){chain=chain.then(function(){return pdf.getPage(num).then(function(page){var scale=maxW/page.getViewport({scale:1}).width;var vp=page.getViewport({scale:scale});var c=document.createElement('canvas');c.width=Math.floor(vp.width);c.height=Math.floor(vp.height);container.appendChild(c);return page.render({canvasContext:c.getContext('2d'),viewport:vp}).promise;});});})(i);
                }
            }).catch(function(err){console.error('PDF error:',err);container.innerHTML='<p style="color:#fff;padding:2rem;">Error: '+err.message+'</p>';});
        });
        m.addEventListener('show.bs.modal',function(event){m._pdfUrl=event.relatedTarget.getAttribute('data-pdf');});
        m.addEventListener('hidden.bs.modal',function(){container.innerHTML='';pageInfo.textContent='';if(m._loadingTask){m._loadingTask.destroy();m._loadingTask=null;}document.body.classList.remove('pdf-modal-open');});
        document.addEventListener('keydown',function(e){if(!document.body.classList.contains('pdf-modal-open'))return;if((e.ctrlKey||e.metaKey)&&(e.key==='p'||e.key==='s'||e.key==='c'||e.key==='a')){e.preventDefault();e.stopImmediatePropagation();return false;}if(e.key==='PrintScreen'){e.preventDefault();try{navigator.clipboard.writeText('');}catch(err){}return false;}},true);
        document.addEventListener('keyup',function(e){if(!document.body.classList.contains('pdf-modal-open')||e.key!=='PrintScreen')return;try{navigator.clipboard.writeText('');}catch(err){}},true);
        document.addEventListener('contextmenu',function(e){if(document.body.classList.contains('pdf-modal-open')){e.preventDefault();return false;}});
        container.addEventListener('dragstart',function(e){e.preventDefault();});
        container.addEventListener('selectstart',function(e){e.preventDefault();});
    })();
    </script>
