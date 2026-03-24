<?php
/**
 * transparencia/matrices_indicadores.php — Matrices de Indicadores
 * Años horizontales con PDFs debajo
 */
require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'transparencia';
$page_title  = 'Matrices de Indicadores — DIF San Mateo Atenco';

$registros = [];
try {
    $pdo  = get_db();
    $registros = $pdo->query('SELECT id, anio, pdf_path FROM mi_pdfs ORDER BY anio DESC')->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('matrices_indicadores.php PDOException: ' . $e->getMessage());
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
                    Matrices de Indicadores</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>
            <div class="row g-5">
                <section id="team" class="dark">
                    <br>
                    <div class="container mx-auto text-center wow fadeIn">
                        <div class="overflow-x:scroll">

                            <div class="question-text-div" style="background:#ccc;">
                                <p class="mt-0 mb-1 text-dark text-center fw-bold py-2">&nbsp;&nbsp;Matrices de Indicadores</p>
                            </div>
                            <br>

<?php if (empty($registros)): ?>
                            <p class="text-muted py-3">No hay información disponible.</p>
<?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <?php foreach ($registros as $r): ?>
                                            <th scope="col" style="text-align:center;background:#ccc;color:#333;width:100px;"><?= htmlspecialchars($r['anio']) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php foreach ($registros as $r): ?>
                                            <td style="text-align:center;">
                                                <?php if (!empty($r['pdf_path'])): ?>
                                                <img src="<?= $base_path ?>img/pdf-download2.jpg" alt="PDF <?= htmlspecialchars($r['anio']) ?>"
                                                    class="img-thumbnail pdf-trigger" style="cursor:pointer;max-height:30px;"
                                                    data-bs-toggle="modal" data-bs-target="#pdfModal"
                                                    data-pdf="<?= htmlspecialchars("{$base_path}{$r['pdf_path']}", ENT_QUOTES, 'UTF-8') ?>">
                                                <?php else: ?>
                                                &nbsp;
                                                <?php endif; ?>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
<?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
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
    @media print { body.pdf-modal-open * { display:none!important; visibility:hidden!important; } }
    body.pdf-modal-open { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
    #pdfContainer canvas { display:block; margin:6px auto; box-shadow:0 1px 4px rgba(0,0,0,.3); max-width:900px; }
    #pdfContainer { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
    </style>

    <script src="<?= $base_path ?>lib/pdfjs/pdf.min.js"></script>
    <script>
    (function(){
        pdfjsLib.GlobalWorkerOptions.workerSrc='<?= $base_path ?>lib/pdfjs/pdf.worker.min.js';
        var m=document.getElementById('pdfModal');
        if(!m)return;
        var container=document.getElementById('pdfContainer');
        var pageInfo=document.getElementById('pdfPageInfo');
        m.addEventListener('shown.bs.modal',function(){
            var url=m._pdfUrl; if(!url)return;
            document.body.classList.add('pdf-modal-open');
            container.innerHTML='<p style="color:#fff;padding:2rem;">Cargando PDF...</p>';
            var loadingTask=pdfjsLib.getDocument(url); m._loadingTask=loadingTask;
            loadingTask.promise.then(function(pdf){
                container.innerHTML=''; pageInfo.textContent='Páginas: '+pdf.numPages;
                var maxW=Math.min(container.clientWidth-16,900); var chain=Promise.resolve();
                for(var i=1;i<=pdf.numPages;i++){(function(num){chain=chain.then(function(){
                    return pdf.getPage(num).then(function(page){
                        var scale=maxW/page.getViewport({scale:1}).width; var vp=page.getViewport({scale:scale});
                        var c=document.createElement('canvas'); c.width=Math.floor(vp.width); c.height=Math.floor(vp.height);
                        container.appendChild(c); return page.render({canvasContext:c.getContext('2d'),viewport:vp}).promise;
                    });});})(i);}
            }).catch(function(err){ console.error('PDF error:',err); container.innerHTML='<p style="color:#fff;padding:2rem;">Error al cargar el PDF: '+err.message+'</p>'; });
        });
        m.addEventListener('show.bs.modal',function(event){ m._pdfUrl=event.relatedTarget.getAttribute('data-pdf'); });
        m.addEventListener('hidden.bs.modal',function(){ container.innerHTML=''; pageInfo.textContent=''; if(m._loadingTask){m._loadingTask.destroy();m._loadingTask=null;} document.body.classList.remove('pdf-modal-open'); });
        document.addEventListener('keydown',function(e){ if(!document.body.classList.contains('pdf-modal-open'))return; if((e.ctrlKey||e.metaKey)&&(e.key==='p'||e.key==='s'||e.key==='c'||e.key==='a')){e.preventDefault();e.stopImmediatePropagation();return false;} if(e.key==='PrintScreen'){e.preventDefault();try{navigator.clipboard.writeText('');}catch(err){}return false;} },true);
        document.addEventListener('keyup',function(e){ if(!document.body.classList.contains('pdf-modal-open')||e.key!=='PrintScreen')return; try{navigator.clipboard.writeText('');}catch(err){} },true);
        document.addEventListener('contextmenu',function(e){if(document.body.classList.contains('pdf-modal-open')){e.preventDefault();return false;}});
        container.addEventListener('dragstart',function(e){e.preventDefault();}); container.addEventListener('selectstart',function(e){e.preventDefault();});
    })();
    </script>
