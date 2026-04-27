<?php
/**
 * admin/sidebar_sections.php — Definición centralizada del sidebar admin
 * Estructura agrupada igual que el navbar del sitio público.
 *
 * Cada grupo tiene: 'group' (título), 'icon' (icono del grupo), 'items' (array de secciones)
 */

$sidebar_groups = [
    [
        'group' => 'Inicio',
        'icon'  => 'bi-house-door',
        'items' => [
            ['title' => 'Slider Principal',    'file' => 'slider_principal', 'icon' => 'bi-images'],
            ['title' => 'Slider DIF Comunica', 'file' => 'slider_comunica',  'icon' => 'bi-megaphone'],
            ['title' => 'Programas',           'file' => 'programas',        'icon' => 'bi-grid-3x3-gap'],
            ['title' => 'Imagen Institucional','file' => 'institucion',      'icon' => 'bi-card-image'],
            ['title' => 'Transparencia Index', 'file' => 'transparencia',    'icon' => 'bi-shield-check'],
            ['title' => 'Footer',              'file' => 'footer',           'icon' => 'bi-layout-text-window-reverse'],
        ],
    ],
    [
        'group' => 'Acerca del DIF',
        'icon'  => 'bi-info-circle',
        'items' => [
            ['title' => 'Presidencia',  'file' => 'presidencia',  'icon' => 'bi-person-badge'],
            ['title' => 'Direcciones',  'file' => 'direcciones',  'icon' => 'bi-people'],
            ['title' => 'Organigrama',  'file' => 'organigrama',  'icon' => 'bi-diagram-3'],
        ],
    ],
    [
        'group' => 'Servicios',
        'icon'  => 'bi-file-earmark-text',
        'items' => [
            ['title' => 'Trámites',              'file' => 'tramites',          'icon' => 'bi-file-earmark-text'],
            ['title' => 'Unidad de Autismo',     'file' => 'autismo',           'icon' => 'bi-heart-pulse'],
        ],
    ],
    [
        'group' => 'Comunicación Social',
        'icon'  => 'bi-megaphone',
        'items' => [
            ['title' => 'Noticias', 'file' => 'noticias', 'icon' => 'bi-newspaper'],
            ['title' => 'Galería',  'file' => 'galeria',  'icon' => 'bi-camera'],
        ],
    ],
    [
        'group' => 'Voluntariado',
        'icon'  => 'bi-heart',
        'items' => [
            ['title' => 'Voluntariado', 'file' => 'voluntariado', 'icon' => 'bi-heart'],
        ],
    ],
    [
        'group' => 'Transparencia',
        'icon'  => 'bi-shield-check',
        'items' => [
            ['title' => 'SEAC',              'file' => 'seac',                'icon' => 'bi-file-earmark-pdf'],
            ['title' => 'Cuenta Pública',    'file' => 'cuenta_publica',      'icon' => 'bi-cash-stack'],
            ['title' => 'Presupuesto Anual', 'file' => 'presupuesto_anual',   'icon' => 'bi-wallet2'],
            ['title' => 'PAE',               'file' => 'pae',                 'icon' => 'bi-clipboard-data'],
            ['title' => 'Matrices',          'file' => 'matrices_indicadores', 'icon' => 'bi-bar-chart-line'],
            ['title' => 'CONAC',             'file' => 'conac',               'icon' => 'bi-bank'],
            ['title' => 'Financiero',        'file' => 'financiero',          'icon' => 'bi-currency-dollar'],
            ['title' => 'Avisos Privacidad', 'file' => 'avisos_privacidad',   'icon' => 'bi-shield-exclamation'],
            ['title' => 'Secciones Dinámicas', 'file' => 'transparencia_dinamica', 'icon' => 'bi-plus-square'],
        ],
    ],
    [
        'group' => 'Sistema',
        'icon'  => 'bi-gear',
        'items' => [
            ['title' => 'Usuarios',    'file' => 'usuarios',           'icon' => 'bi-people'],
            ['title' => 'Analíticas',  'file' => 'analytics',          'icon' => 'bi-graph-up'],
            ['title' => 'Reportes',    'file' => 'reportes_historial',  'icon' => 'bi-file-earmark-bar-graph'],
            ['title' => 'Mantenimiento', 'file' => '../mantenimiento',  'icon' => 'bi-tools'],
        ],
    ],
    [
        'group' => 'Documentación',
        'icon'  => 'bi-book',
        'items' => [
            ['title' => 'Documentación',  'file' => 'documentacion',  'icon' => 'bi-book'],
        ],
    ],
];

// Determinar archivo actual para marcar como activo
$current_admin_file = basename($_SERVER['SCRIPT_FILENAME'] ?? '');

// Función helper para renderizar el sidebar
function render_admin_sidebar(array $sidebar_groups, string $current_file): void {
    // Cargar permisos del usuario si no es admin
    $is_admin = ($_SESSION['admin_rol'] ?? 'admin') === 'admin';
    $allowed_files = [];
    $always_allowed = ['dashboard.php', 'logout.php'];
    if (!$is_admin) {
        try {
            require_once __DIR__ . '/../includes/db.php';
            $pdo_sb = get_db();
            $stmt_sb = $pdo_sb->prepare('SELECT seccion_file FROM admin_permisos WHERE user_id = ?');
            $stmt_sb->execute([$_SESSION['admin_id'] ?? 0]);
            while ($r = $stmt_sb->fetch()) { $allowed_files[] = $r['seccion_file']; }
        } catch (PDOException $e) {}
    }
?>
<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <a href="dashboard" class="text-white text-decoration-none">
            <img src="../img/escudo.png" alt="DIF" style="height:28px;margin-right:6px;vertical-align:middle;"> Admin DIF
        </a>
        <button class="btn btn-sm btn-outline-light d-md-none" id="closeSidebar" aria-label="Cerrar menú">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <ul class="nav flex-column mt-2" style="overflow-y:auto;flex:1;">
<?php foreach ($sidebar_groups as $gi => $group): ?>
<?php
    // Filtrar items por permisos
    $visible_items = [];
    foreach ($group['items'] as $item) {
        if ($is_admin || in_array($item['file'], $allowed_files) || in_array($item['file'], $always_allowed)) {
            $visible_items[] = $item;
        }
    }
    if (empty($visible_items)) continue; // Ocultar grupo si no tiene items visibles
    $groupActive = false;
    foreach ($visible_items as $item) {
        if ($item['file'] === $current_file) { $groupActive = true; break; }
    }
?>
        <li class="sidebar-group">
            <div class="sidebar-group-title<?= $groupActive ? '' : ' collapsed' ?>" data-group="g<?= $gi ?>">
                <span><i class="bi <?= htmlspecialchars($group['icon']) ?> me-1"></i> <?= htmlspecialchars($group['group']) ?></span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="sidebar-group-items" id="g<?= $gi ?>" style="<?= $groupActive ? '' : 'max-height:0;' ?>">
<?php foreach ($visible_items as $item): ?>
<?php 
    // Abrir en nueva pestaña si es la página de mantenimiento
    $target = ($item['file'] === '../mantenimiento') ? ' target="_blank" rel="noopener noreferrer"' : '';
?>
                <a class="nav-link<?= $item['file'] === $current_file ? ' active' : '' ?>" href="<?= htmlspecialchars($item['file']) ?>"<?= $target ?>>
                    <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i> <?= htmlspecialchars($item['title']) ?>
                </a>
<?php endforeach; ?>
            </div>
        </li>
<?php if ($gi < count($sidebar_groups) - 1): ?>
        <li class="sidebar-divider"></li>
<?php endif; ?>
<?php endforeach; ?>
    </ul>
    <div class="mt-auto p-3 border-top border-secondary">
        <a href="logout" class="btn btn-outline-light btn-sm w-100">
            <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
        </a>
    </div>
</nav>
<script>
(function(){
    document.querySelectorAll('.sidebar-group-title').forEach(function(t){
        t.addEventListener('click',function(){
            var items=document.getElementById(t.getAttribute('data-group'));
            if(!items)return;
            if(t.classList.contains('collapsed')){
                t.classList.remove('collapsed');
                items.style.maxHeight=items.scrollHeight+'px';
                setTimeout(function(){items.style.maxHeight='none';},300);
            }else{
                items.style.maxHeight=items.scrollHeight+'px';
                items.offsetHeight;
                items.style.maxHeight='0';
                t.classList.add('collapsed');
            }
        });
    });
    // Set initial max-height for open groups
    document.querySelectorAll('.sidebar-group-title:not(.collapsed)').forEach(function(t){
        var items=document.getElementById(t.getAttribute('data-group'));
        if(items)items.style.maxHeight='none';
    });

    // ── Auto-logout por inactividad (5 min) ──
    var TIMEOUT=5*60*1000, timer;
    function resetTimer(){
        clearTimeout(timer);
        timer=setTimeout(function(){ window.location.href='login.php?expired=1'; }, TIMEOUT);
    }
    ['mousemove','keydown','click','scroll','touchstart'].forEach(function(e){
        document.addEventListener(e, resetTimer, {passive:true});
    });
    resetTimer();
})();
</script>
<script src="../js/admin-tooltips.js"></script>
<script>
// Mostrar nombre de archivo seleccionado en inputs file
document.addEventListener('change', function(e) {
    if (e.target.type !== 'file') return;
    var inp = e.target;
    if (inp.files && inp.files.length > 0) {
        inp.classList.add('file-selected');
        var names = Array.from(inp.files).map(function(f) { return f.name; }).join(', ');
        inp.title = names;
    } else {
        inp.classList.remove('file-selected');
        inp.title = '';
    }
});

// ── Modal visor de PDF ────────────────────────────────────────────────────────
(function() {
    var modal = document.createElement('div');
    modal.id = 'pdfViewerModal';
    modal.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:99999;flex-direction:column;align-items:center;justify-content:center;';
    modal.innerHTML = '<div style="width:90vw;height:90vh;background:#fff;border-radius:10px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.5);"><div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;background:#2d2d2d;color:#fff;flex-shrink:0;"><span id="pdfViewerTitle" style="font-size:0.9rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:70%;"></span><div style="display:flex;gap:8px;"><a id="pdfViewerDownload" href="#" download style="color:#fff;text-decoration:none;font-size:0.8rem;padding:4px 10px;border:1px solid rgba(255,255,255,0.3);border-radius:5px;"><i class="bi bi-download me-1"></i>Descargar</a><button id="pdfViewerClose" style="background:rgb(200,16,44);border:none;color:#fff;border-radius:5px;padding:4px 12px;cursor:pointer;font-size:0.85rem;">&#x2715; Cerrar</button></div></div><iframe id="pdfViewerFrame" src="" style="flex:1;border:none;width:100%;"></iframe></div>';
    document.body.appendChild(modal);

    var frame = document.getElementById('pdfViewerFrame');
    var titleEl = document.getElementById('pdfViewerTitle');
    var dlBtn = document.getElementById('pdfViewerDownload');
    var closeBtn = document.getElementById('pdfViewerClose');

    function openPdf(url, name) {
        frame.src = url;
        titleEl.textContent = name || 'Documento PDF';
        dlBtn.href = url;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closePdf() {
        modal.style.display = 'none';
        frame.src = '';
        document.body.style.overflow = '';
    }

    closeBtn.addEventListener('click', closePdf);
    modal.addEventListener('click', function(e) { if (e.target === modal) closePdf(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePdf(); });

    document.addEventListener('click', function(e) {
        var a = e.target.closest('a[target="_blank"]');
        if (!a) return;
        var href = a.getAttribute('href') || '';
        // Ignorar botones de descarga directa
        if (href.indexOf('download=1') !== -1) return;
        if (/\.pdf(\?|$)/i.test(href) || a.querySelector('.bi-file-earmark-pdf,.bi-file-pdf,.bi-eye')) {
            e.preventDefault();
            var row = a.closest('tr');
            var name = row ? (row.querySelector('td')?.textContent?.trim() || 'Documento') : 'Documento PDF';
            openPdf(href, name);
        }
    });
})();
</script>
<?php
}
