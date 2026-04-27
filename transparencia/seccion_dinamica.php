<?php
/**
 * transparencia/seccion_dinamica.php — Renderiza una sección dinámica de transparencia
 * Recibe ?slug=nombre_seccion y renderiza según la plantilla asignada
 */
require_once __DIR__ . '/../includes/db.php';

$base_path   = '../';
$active_page = 'transparencia';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) { header('Location: ' . $base_path); exit; }

// Verificar mantenimiento centralizado para esta sección
$pagina_key = 'trans_' . $slug;
require_once __DIR__ . '/../includes/mantenimiento_check.php';

$pdo = get_db();
$stmt = $pdo->prepare('SELECT * FROM trans_secciones WHERE slug = ? AND activo = 1');
$stmt->execute([$slug]);
$seccion = $stmt->fetch();
if (!$seccion) { header('Location: ' . $base_path); exit; }

$page_title = htmlspecialchars($seccion['nombre']) . ' — DIF San Mateo Atenco';
$plantilla = $seccion['plantilla'];
$secId = (int) $seccion['id'];

// Cargar datos según plantilla
$bloques = []; $titulos = []; $pdfs = []; $conceptosByBloque = []; $pdfsByConcepto = []; $pdfsMap = [];

if ($plantilla === 'matrices') {
    $s = $pdo->prepare('SELECT * FROM trans_pdfs WHERE seccion_id=? AND pdf_path IS NOT NULL ORDER BY anio DESC');
    $s->execute([$secId]); $pdfs = $s->fetchAll();
} elseif ($plantilla === 'pae') {
    $s = $pdo->prepare('SELECT * FROM trans_titulos WHERE seccion_id=? AND bloque_id IS NULL ORDER BY orden ASC');
    $s->execute([$secId]); $titulos = $s->fetchAll();
    $s = $pdo->prepare('SELECT * FROM trans_pdfs WHERE seccion_id=? AND titulo_id IS NOT NULL AND pdf_path IS NOT NULL ORDER BY anio DESC');
    $s->execute([$secId]);
    foreach ($s->fetchAll() as $p) { $pdfsByTitulo[(int)$p['titulo_id']][] = $p; }
} else {
    $s = $pdo->prepare('SELECT * FROM trans_bloques WHERE seccion_id=? ORDER BY anio DESC');
    $s->execute([$secId]); $bloques = $s->fetchAll();
    foreach ($bloques as $bl) {
        $s = $pdo->prepare('SELECT * FROM trans_conceptos WHERE seccion_id=? AND bloque_id=? ORDER BY orden ASC');
        $s->execute([$secId, $bl['id']]); $conceptosByBloque[(int)$bl['id']] = $s->fetchAll();
        if (in_array($plantilla, ['seac','conac'])) {
            $s = $pdo->prepare('SELECT * FROM trans_pdfs WHERE seccion_id=? AND concepto_id IN (SELECT id FROM trans_conceptos WHERE bloque_id=?)');
            $s->execute([$secId, $bl['id']]);
            foreach ($s->fetchAll() as $r) { $pdfsMap[(int)$bl['id']][(int)$r['concepto_id']][(int)$r['trimestre']] = $r; }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="mx-auto text-center wow fadeIn" data-wow-delay="0.1s" style="max-width:700px;">
            <h4 class="mb-1 d-inline-block" style="font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:2px;color:rgb(107,98,90);">
                <?= htmlspecialchars($seccion['nombre']) ?></h4>
            <div style="height:16px;background:rgb(200,16,44);width:23%;margin:4px auto 24px;"></div>
        </div>

<?php if ($plantilla === 'matrices'): ?>
        <link rel="stylesheet" href="<?= $base_path ?>css/acordeon.css">
        <link rel="stylesheet" href="<?= $base_path ?>css/tablas.css">
        <div class="question-text-div" style="background:#ccc;">
            <p class="mt-0 mb-1 text-center fw-bold py-2" style="color:rgb(107,98,90);"><?= htmlspecialchars($seccion['nombre']) ?></p>
        </div><br>
        <?php if (empty($pdfs)): ?>
        <p class="text-center text-muted">No hay informacion disponible.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead><tr>
                    <?php foreach ($pdfs as $p): ?>
                    <th scope="col" style="text-align:center;background:#ccc;color:rgba(0,0,0,0.8);width:100px;"><?= htmlspecialchars($p['anio']) ?></th>
                    <?php endforeach; ?>
                </tr></thead>
                <tbody><tr>
                    <?php foreach ($pdfs as $p): ?>
                    <td style="text-align:center;">
                        <?php if (!empty($p['pdf_path'])): ?>
                        <img src="<?= $base_path ?>img/pdf-download2.jpg" alt="PDF <?= htmlspecialchars($p['anio']) ?>" class="img-thumbnail pdf-trigger" style="cursor:pointer;max-height:55px;" data-bs-toggle="modal" data-bs-target="#pdfModal" data-pdf="<?= htmlspecialchars($base_path . $p['pdf_path']) ?>">
                        <?php else: ?>&nbsp;<?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr></tbody>
            </table>
        </div>
        <?php endif; ?>

<?php elseif ($plantilla === 'pae'): ?>
        <?php
        $paeYears = [];
        if (!empty($pdfsByTitulo)) { foreach ($pdfsByTitulo as $tPs) { foreach ($tPs as $p) { $paeYears[(int)$p['anio']] = true; } } }
        krsort($paeYears); $paeYearKeys = array_keys($paeYears);
        ?>
        <link rel="stylesheet" href="<?= $base_path ?>css/acordeon.css">
        <link rel="stylesheet" href="<?= $base_path ?>css/tablas.css">
        <div class="question-text-div" style="background:#ccc;">
            <p class="mt-0 mb-1 text-center fw-bold py-2" style="color:rgb(107,98,90);"><?= htmlspecialchars($seccion['nombre']) ?></p>
        </div><br>
        <?php if (empty($titulos) || empty($paeYearKeys)): ?>
        <p class="text-muted text-center">No hay informacion disponible.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead><tr>
                    <th scope="col" style="text-align:left;padding-left:0.5rem;background:#ccc;color:#333;">Titulo</th>
                    <?php foreach ($paeYearKeys as $yr): ?>
                    <th scope="col" style="text-align:center;width:80px;background:#ccc;color:#333;"><?= $yr ?></th>
                    <?php endforeach; ?>
                </tr></thead>
                <tbody>
                <?php foreach ($titulos as $i => $tit): ?>
                <tr>
                    <td style="text-align:left;padding-left:0.5rem;color:rgb(107,98,90);"><?= htmlspecialchars(($i+1) . '.- ' . $tit['nombre']) ?></td>
                    <?php foreach ($paeYearKeys as $yr):
                        $tPs = $pdfsByTitulo[(int)$tit['id']] ?? [];
                        $found = null;
                        foreach ($tPs as $p) { if ((int)$p['anio'] === $yr) { $found = $p; break; } }
                    ?>
                    <td style="text-align:center;">
                        <?php if ($found && !empty($found['pdf_path'])): ?>
                        <img src="<?= $base_path ?>img/pdf-download2.jpg" alt="PDF <?= $yr ?>" class="img-thumbnail pdf-trigger" style="cursor:pointer;max-height:55px;" data-bs-toggle="modal" data-bs-target="#pdfModal" data-pdf="<?= htmlspecialchars($base_path . $found['pdf_path']) ?>">
                        <?php else: ?>&nbsp;<?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

<?php elseif (in_array($plantilla, ['seac','conac'])): ?>
        <?php if (empty($bloques)): ?>
        <p class="text-muted text-center">No hay bloques disponibles.</p>
        <?php else: ?>
        <link rel="stylesheet" href="<?= $base_path ?>css/acordeon.css">
        <link rel="stylesheet" href="<?= $base_path ?>css/tablas.css">
        <!-- Botones de año -->
        <div class="seac-buttons-grid">
            <?php foreach ($bloques as $bl): ?>
            <div class="seac-year-btn" data-target="dynPanel<?= (int)$bl['id'] ?>">
                <span class="seac-year-text"><?= (int)$bl['anio'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- Paneles expandibles -->
        <?php foreach ($bloques as $bl): $bConceptos = $conceptosByBloque[(int)$bl['id']] ?? []; ?>
        <div class="seac-panel" id="dynPanel<?= (int)$bl['id'] ?>" style="display:none;">
            <div class="question-text-div" style="background:#ccc;color:#333;"><?= htmlspecialchars($seccion['nombre']) ?> <?= (int)$bl['anio'] ?></div>
            <br>
            <?php if (empty($bConceptos)): ?><p class="text-muted">Sin conceptos.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr><th scope="col">Concepto</th>
                    <th scope="col"><p>1er. Trimestre</p></th><th scope="col"><p>2do. Trimestre</p></th>
                    <th scope="col"><p>3er. Trimestre</p></th><th scope="col"><p>4to. Trimestre</p></th></tr></thead>
                    <tbody>
                    <?php foreach ($bConceptos as $c): ?><tr>
                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                        <?php for ($t=1;$t<=4;$t++): $cell = $pdfsMap[(int)$bl['id']][(int)$c['id']][$t] ?? null; ?>
                        <td><?php if ($cell && !empty($cell['pdf_path'])): $pUrl = htmlspecialchars($base_path . $cell['pdf_path']); ?>
                            <img src="<?= $base_path ?>img/pdf-download2.jpg" alt="Ver PDF" class="img-thumbnail pdf-trigger" style="cursor:pointer;max-height:55px;" data-bs-toggle="modal" data-bs-target="#pdfModal" data-pdf="<?= $pUrl ?>">
                        <?php else: ?>&nbsp;<?php endif; ?></td>
                        <?php endfor; ?>
                    </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

<?php elseif ($plantilla === 'financiero' || $plantilla === 'cuenta_publica'): ?>
        <?php if (empty($bloques)): ?>
        <p class="text-muted text-center">No hay bloques disponibles.</p>
        <?php else: ?>
        <link rel="stylesheet" href="<?= $base_path ?>css/acordeon.css">
        <link rel="stylesheet" href="<?= $base_path ?>css/tablas.css">
        <!-- Botones de año -->
        <div class="seac-buttons-grid">
            <?php foreach ($bloques as $bl): ?>
            <div class="seac-year-btn" data-target="dynPanel<?= (int)$bl['id'] ?>">
                <span class="seac-year-text"><?= (int)$bl['anio'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- Paneles expandibles -->
        <?php foreach ($bloques as $bl): $bConceptos = $conceptosByBloque[(int)$bl['id']] ?? []; ?>
        <div class="seac-panel" id="dynPanel<?= (int)$bl['id'] ?>" style="display:none;">
            <div class="question-text-div" style="background:#ccc;color:#333;"><?= htmlspecialchars($seccion['nombre']) ?> <?= (int)$bl['anio'] ?></div>
            <br>
            <?php if (empty($bConceptos)): ?><p class="text-muted">Sin conceptos.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr><th scope="col" style="text-align:left;">Concepto</th><th scope="col" style="width:100px;text-align:center;">PDF</th></tr></thead>
                    <tbody>
                    <?php foreach ($bConceptos as $c): ?><tr>
                        <td style="text-align:left;"><?= htmlspecialchars($c['nombre']) ?></td>
                        <td style="text-align:center;"><?php if (!empty($c['pdf_path'])): $pUrl = htmlspecialchars($base_path . $c['pdf_path']); ?>
                            <img src="<?= $base_path ?>img/pdf-download2.jpg" alt="Ver PDF" class="img-thumbnail pdf-trigger" style="cursor:pointer;max-height:55px;" data-bs-toggle="modal" data-bs-target="#pdfModal" data-pdf="<?= $pUrl ?>">
                        <?php else: ?>&nbsp;<?php endif; ?></td>
                    </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

<?php elseif ($plantilla === 'presupuesto_anual'): ?>
        <?php if (empty($bloques)): ?>
        <p class="text-muted text-center">No hay bloques disponibles.</p>
        <?php else: ?>
        <link rel="stylesheet" href="<?= $base_path ?>css/acordeon.css">
        <link rel="stylesheet" href="<?= $base_path ?>css/tablas.css">
        <div class="seac-buttons-grid">
            <?php foreach ($bloques as $bl): ?>
            <div class="seac-year-btn" data-target="dynPanel<?= (int)$bl['id'] ?>">
                <span class="seac-year-text"><?= (int)$bl['anio'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php foreach ($bloques as $bl): $bConceptos = $conceptosByBloque[(int)$bl['id']] ?? []; ?>
        <div class="seac-panel" id="dynPanel<?= (int)$bl['id'] ?>" style="display:none;">
            <div class="question-text-div" style="background:#ccc;color:#333;"><?= htmlspecialchars($seccion['nombre']) ?> <?= (int)$bl['anio'] ?></div>
            <br>
            <?php if (empty($bConceptos)): ?><p class="text-muted">Sin conceptos.</p>
            <?php else: foreach ($bConceptos as $c):
                $s = $pdo->prepare('SELECT * FROM trans_pdfs WHERE concepto_id=? AND pdf_path IS NOT NULL ORDER BY anio DESC');
                $s->execute([$c['id']]); $cPdfs = $s->fetchAll(); ?>
            <div class="question-text-div" style="background:#333;"><?= htmlspecialchars($c['nombre']) ?></div>
            <?php if (!empty($cPdfs)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr><th scope="col">Anio</th><th scope="col" style="width:100px;text-align:center;">PDF</th></tr></thead>
                    <tbody>
                    <?php foreach ($cPdfs as $p): ?><tr>
                        <td><?= (int)$p['anio'] ?></td>
                        <td style="text-align:center;"><img src="<?= $base_path ?>img/pdf-download2.jpg" alt="Ver PDF" class="img-thumbnail pdf-trigger" style="cursor:pointer;max-height:55px;" data-bs-toggle="modal" data-bs-target="#pdfModal" data-pdf="<?= htmlspecialchars($base_path . $p['pdf_path']) ?>"></td>
                    </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            <br>
            <?php endforeach; endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
<?php endif; ?>

    </div>
</div>

<div class="pleca"><img src="<?= $base_path ?>img/pleca.png" alt="pleca"></div>

<?php if (in_array($plantilla, ['seac','conac','cuenta_publica','financiero','presupuesto_anual','pae','matrices'])): ?>
<!-- Modal PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-height:95vh;">
        <div class="modal-content" style="height:90vh;">
            <div class="modal-header"><h5 class="modal-title">Documento PDF</h5><span id="pdfPageInfo" style="margin-left:auto;margin-right:1rem;font-size:.85rem;color:#888;"></span><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="pdfContainer" style="overflow-y:auto;padding:8px;background:#525659;text-align:center;"></div>
        </div>
    </div>
</div>
<style>
.seac-buttons-grid{display:grid;grid-template-columns:repeat(2,1fr);justify-items:center;gap:22px;margin-bottom:20px;max-width:900px;margin-left:auto;margin-right:auto;}
.seac-year-btn:only-child{grid-column:1/span 2;justify-self:center;width:45%;}
.seac-year-btn{background:url(<?= $base_path ?>img/bloque_botone_dif.png) no-repeat center center;background-size:contain;padding:0;display:flex;align-items:center;justify-content:flex-start;cursor:pointer;min-height:80px;width:100%;max-width:650px;aspect-ratio:4/1;}
.seac-year-text{font-family:"Montserrat",sans-serif;font-weight:700;font-size:clamp(18px,2.2vw,28px);color:#fff;text-align:center;width:75%;padding-left:8%;text-shadow:1px 1px 3px rgba(0,0,0,0.3);}
.seac-panel{margin-bottom:20px;}
#pdfContainer canvas{display:block;margin:6px auto;box-shadow:0 1px 4px rgba(0,0,0,.3);max-width:900px;}
@media(max-width:576px){.seac-buttons-grid{grid-template-columns:1fr;}.seac-year-btn{min-height:55px;}.seac-year-text{font-size:clamp(16px,5vw,22px);width:55%;}}
</style>
<script src="<?= $base_path ?>lib/pdfjs/pdf.min.js"></script>
<script>
(function(){
    pdfjsLib.GlobalWorkerOptions.workerSrc='<?= $base_path ?>lib/pdfjs/pdf.worker.min.js';
    document.querySelectorAll('.seac-year-btn').forEach(function(btn){
        btn.addEventListener('click',function(){
            var tid=btn.getAttribute('data-target'),panel=document.getElementById(tid),isOpen=btn.classList.contains('active');
            document.querySelectorAll('.seac-year-btn').forEach(function(b){b.classList.remove('active');var p=document.getElementById(b.getAttribute('data-target'));if(p)p.style.display='none';});
            if(!isOpen&&panel){btn.classList.add('active');panel.style.display='block';}
        });
    });
    var modal=document.getElementById('pdfModal');if(!modal)return;
    var container=document.getElementById('pdfContainer'),pageInfo=document.getElementById('pdfPageInfo');
    modal.addEventListener('show.bs.modal',function(e){modal._pdfUrl=e.relatedTarget.getAttribute('data-pdf');});
    modal.addEventListener('shown.bs.modal',function(){
        var url=modal._pdfUrl;if(!url)return;container.innerHTML='<p style="color:#fff;padding:2rem;">Cargando...</p>';
        var lt=pdfjsLib.getDocument(url);modal._lt=lt;
        lt.promise.then(function(pdf){container.innerHTML='';pageInfo.textContent='Paginas: '+pdf.numPages;var mW=Math.min(container.clientWidth-16,900),ch=Promise.resolve();
        for(var i=1;i<=pdf.numPages;i++){(function(n){ch=ch.then(function(){return pdf.getPage(n).then(function(pg){var sc=mW/pg.getViewport({scale:1}).width,vp=pg.getViewport({scale:sc}),c=document.createElement('canvas');c.width=Math.floor(vp.width);c.height=Math.floor(vp.height);container.appendChild(c);return pg.render({canvasContext:c.getContext('2d'),viewport:vp}).promise;});});})(i);}
        }).catch(function(err){container.innerHTML='<p style="color:#fff;padding:2rem;">Error: '+err.message+'</p>';});
    });
    modal.addEventListener('hidden.bs.modal',function(){container.innerHTML='';pageInfo.textContent='';if(modal._lt){modal._lt.destroy();modal._lt=null;}});
})();
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
