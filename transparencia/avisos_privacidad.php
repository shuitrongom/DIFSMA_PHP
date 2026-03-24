<?php
/**
 * transparencia/avisos_privacidad.php — Avisos de Privacidad
 * Se muestra como página completa con modal de PDF integrado.
 */
require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'transparencia';
$page_title  = 'Avisos de Privacidad — DIF San Mateo Atenco';

$texto_aviso = '';
$botones     = [];

try {
    $pdo = get_db();
    $stmt = $pdo->query('SELECT texto_aviso FROM avisos_privacidad_config LIMIT 1');
    $row = $stmt->fetch();
    if ($row) $texto_aviso = $row['texto_aviso'];

    $stmt = $pdo->query('SELECT id, titulo, pdf_path, orden FROM avisos_privacidad WHERE activo = 1 ORDER BY orden ASC');
    $botones = $stmt->fetchAll();
} catch (PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) error_log('avisos_privacidad.php: ' . $e->getMessage());
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

    <div class="container-fluid service py-5">
        <div class="container py-5">
            <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width: 700px;">
                <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif; font-weight:800; letter-spacing:2px; color:rgb(107,98,90);">
                    Aviso de Privacidad</h4>
                <div style="height:16px; background:rgb(200,16,44); width:23%; margin: 4px auto 24px;"></div>
            </div>

            <?php if (!empty($texto_aviso)): ?>
            <div class="mx-auto mb-5" style="max-width:900px;">
                <p style="font-family:'Montserrat',sans-serif;font-size:15px;line-height:1.8;color:#333;text-align:justify;">
                    <?php
                    // Escapar HTML, luego convertir URLs en links que abren en nueva pestaña
                    $safe = htmlspecialchars($texto_aviso, ENT_QUOTES, 'UTF-8');
                    $safe = preg_replace(
                        '/(https?:\/\/[^\s<]+)/',
                        '<a href="$1" target="_blank" rel="noopener noreferrer" style="color:rgb(200,16,44);word-break:break-all;">$1</a>',
                        $safe
                    );
                    echo nl2br($safe);
                    ?>
                </p>
            </div>
            <?php endif; ?>

            <?php if (!empty($botones)): ?>
            <div class="row g-3 justify-content-center" style="max-width:900px;margin:0 auto;">
                <?php foreach ($botones as $btn): ?>
                <div class="col-md-6">
                    <?php if (!empty($btn['pdf_path'])): ?>
                    <a href="#" class="d-block text-center text-white text-decoration-none pdf-aviso-trigger"
                       style="background:rgb(200,16,44);padding:18px 20px;border-radius:8px;font-family:'Montserrat',sans-serif;font-weight:600;font-size:14px;transition:opacity 0.2s;"
                       data-pdf="<?= htmlspecialchars("{$base_path}{$btn['pdf_path']}", ENT_QUOTES, 'UTF-8') ?>"
                       data-titulo="<?= htmlspecialchars($btn['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                       onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-file-pdf me-2"></i><?= htmlspecialchars($btn['titulo'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <?php else: ?>
                    <span class="d-block text-center text-white"
                          style="background:rgb(200,16,44);padding:18px 20px;border-radius:8px;font-family:'Montserrat',sans-serif;font-weight:600;font-size:14px;opacity:0.6;cursor:not-allowed;">
                        <i class="fas fa-file-pdf me-2"></i><?= htmlspecialchars($btn['titulo'], ENT_QUOTES, 'UTF-8') ?>
                        <br><small style="font-weight:400;font-size:11px;">(PDF no disponible)</small>
                    </span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
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
    @media print { body.pdf-modal-open * { display:none!important; visibility:hidden!important; } }
    body.pdf-modal-open { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
    #pdfContainer canvas { display:block; margin:6px auto; box-shadow:0 1px 4px rgba(0,0,0,.3); max-width:900px; }
    #pdfContainer { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
    </style>

    <script src="<?= $base_path ?>lib/pdfjs/pdf.min.js"></script>
    <script>
    (function(){
        pdfjsLib.GlobalWorkerOptions.workerSrc='<?= $base_path ?>lib/pdfjs/pdf.worker.min.js';

        // Click en botón rojo abre modal PDF
        document.querySelectorAll('.pdf-aviso-trigger').forEach(function(el){
            el.addEventListener('click', function(e){
                e.preventDefault();
                var modal = document.getElementById('pdfModal');
                modal._pdfUrl = el.getAttribute('data-pdf');
                document.getElementById('pdfModalLabel').textContent = el.getAttribute('data-titulo');
                var bsModal = new bootstrap.Modal(modal);
                bsModal.show();
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

        m.addEventListener('hidden.bs.modal',function(){
            container.innerHTML='';pageInfo.textContent='';
            if(m._loadingTask){m._loadingTask.destroy();m._loadingTask=null;}
            document.body.classList.remove('pdf-modal-open');
        });

        document.addEventListener('keydown',function(e){if(!document.body.classList.contains('pdf-modal-open'))return;if((e.ctrlKey||e.metaKey)&&(e.key==='p'||e.key==='s'||e.key==='c'||e.key==='a')){e.preventDefault();e.stopImmediatePropagation();return false;}if(e.key==='PrintScreen'){e.preventDefault();try{navigator.clipboard.writeText('');}catch(err){}return false;}},true);
        document.addEventListener('keyup',function(e){if(!document.body.classList.contains('pdf-modal-open')||e.key!=='PrintScreen')return;try{navigator.clipboard.writeText('');}catch(err){}},true);
        document.addEventListener('contextmenu',function(e){if(document.body.classList.contains('pdf-modal-open')){e.preventDefault();return false;}});
        container.addEventListener('dragstart',function(e){e.preventDefault();});
        container.addEventListener('selectstart',function(e){e.preventDefault();});
    })();
    </script>
