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
            ['title' => 'Slider Principal',    'file' => 'slider_principal.php', 'icon' => 'bi-images'],
            ['title' => 'Slider DIF Comunica', 'file' => 'slider_comunica.php',  'icon' => 'bi-megaphone'],
            ['title' => 'Programas',           'file' => 'programas.php',        'icon' => 'bi-grid-3x3-gap'],
            ['title' => 'Imagen Institucional','file' => 'institucion.php',      'icon' => 'bi-card-image'],
            ['title' => 'Transparencia Index', 'file' => 'transparencia.php',    'icon' => 'bi-shield-check'],
            ['title' => 'Footer',              'file' => 'footer.php',           'icon' => 'bi-layout-text-window-reverse'],
        ],
    ],
    [
        'group' => 'Acerca del DIF',
        'icon'  => 'bi-info-circle',
        'items' => [
            ['title' => 'Presidencia',  'file' => 'presidencia.php',  'icon' => 'bi-person-badge'],
            ['title' => 'Direcciones',  'file' => 'direcciones.php',  'icon' => 'bi-people'],
            ['title' => 'Organigrama',  'file' => 'organigrama.php',  'icon' => 'bi-diagram-3'],
        ],
    ],
    [
        'group' => 'Servicios',
        'icon'  => 'bi-file-earmark-text',
        'items' => [
            ['title' => 'Trámites', 'file' => 'tramites.php', 'icon' => 'bi-file-earmark-text'],
        ],
    ],
    [
        'group' => 'Comunicación Social',
        'icon'  => 'bi-megaphone',
        'items' => [
            ['title' => 'Noticias', 'file' => 'noticias.php', 'icon' => 'bi-newspaper'],
            ['title' => 'Galería',  'file' => 'galeria.php',  'icon' => 'bi-camera'],
        ],
    ],
    [
        'group' => 'Transparencia',
        'icon'  => 'bi-shield-check',
        'items' => [
            ['title' => 'SEAC',              'file' => 'seac.php',                'icon' => 'bi-file-earmark-pdf'],
            ['title' => 'Cuenta Pública',    'file' => 'cuenta_publica.php',      'icon' => 'bi-cash-stack'],
            ['title' => 'Presupuesto Anual', 'file' => 'presupuesto_anual.php',   'icon' => 'bi-wallet2'],
            ['title' => 'PAE',               'file' => 'pae.php',                 'icon' => 'bi-clipboard-data'],
            ['title' => 'Matrices',          'file' => 'matrices_indicadores.php', 'icon' => 'bi-bar-chart-line'],
            ['title' => 'CONAC',             'file' => 'conac.php',               'icon' => 'bi-bank'],
            ['title' => 'Financiero',        'file' => 'financiero.php',          'icon' => 'bi-currency-dollar'],
            ['title' => 'Avisos Privacidad', 'file' => 'avisos_privacidad.php',   'icon' => 'bi-shield-exclamation'],
        ],
    ],
];

// Determinar archivo actual para marcar como activo
$current_admin_file = basename($_SERVER['SCRIPT_FILENAME'] ?? '');

// Función helper para renderizar el sidebar
function render_admin_sidebar(array $sidebar_groups, string $current_file): void {
?>
<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <a href="dashboard.php" class="text-white text-decoration-none">
            <img src="../img/escudo.png" alt="DIF" style="height:28px;margin-right:6px;vertical-align:middle;"> Admin DIF
        </a>
        <button class="btn btn-sm btn-outline-light d-md-none" id="closeSidebar" aria-label="Cerrar menú">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <ul class="nav flex-column mt-2" style="overflow-y:auto;flex:1;">
<?php foreach ($sidebar_groups as $gi => $group): ?>
<?php
    // Verificar si el archivo activo está en este grupo
    $groupActive = false;
    foreach ($group['items'] as $item) {
        if ($item['file'] === $current_file) { $groupActive = true; break; }
    }
?>
        <li class="sidebar-group">
            <div class="sidebar-group-title<?= $groupActive ? '' : ' collapsed' ?>" data-group="g<?= $gi ?>">
                <span><i class="bi <?= htmlspecialchars($group['icon']) ?> me-1"></i> <?= htmlspecialchars($group['group']) ?></span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="sidebar-group-items" id="g<?= $gi ?>" style="<?= $groupActive ? '' : 'max-height:0;' ?>">
<?php foreach ($group['items'] as $item): ?>
                <a class="nav-link<?= $item['file'] === $current_file ? ' active' : '' ?>" href="<?= htmlspecialchars($item['file']) ?>">
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
        <a href="logout.php" class="btn btn-outline-danger btn-sm w-100">
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
<?php
}
