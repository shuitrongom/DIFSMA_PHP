<?php
/**
 * admin/analytics.php — Analíticas de visitas del sitio público
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// ── Filtros ───────────────────────────────────────────────────────────────────
$fecha_ini = $_GET['fecha_ini'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$dispositivo = $_GET['dispositivo'] ?? '';

$where  = ['DATE(created_at) BETWEEN ? AND ?', 'es_bot = 0'];
$params = [$fecha_ini, $fecha_fin];
if ($dispositivo) { $where[] = 'dispositivo = ?'; $params[] = $dispositivo; }
$w = implode(' AND ', $where);

// ── KPIs principales ──────────────────────────────────────────────────────────
$total_visitas  = (int)$pdo->prepare("SELECT COUNT(*) FROM visitor_analytics WHERE $w")->execute($params) ? $pdo->prepare("SELECT COUNT(*) FROM visitor_analytics WHERE $w")->execute($params) : 0;
// Re-ejecutar correctamente
$st = $pdo->prepare("SELECT COUNT(*) as total, COUNT(DISTINCT session_id) as sesiones, COUNT(DISTINCT ip_hash) as unicos FROM visitor_analytics WHERE $w");
$st->execute($params); $kpi = $st->fetch();

// ── Visitas por día ───────────────────────────────────────────────────────────
$st = $pdo->prepare("SELECT DATE(created_at) as dia, COUNT(*) as total FROM visitor_analytics WHERE $w GROUP BY dia ORDER BY dia ASC");
$st->execute($params); $por_dia = $st->fetchAll();

// ── Por hora del día ──────────────────────────────────────────────────────────
$st = $pdo->prepare("SELECT HOUR(created_at) as hora, COUNT(*) as total FROM visitor_analytics WHERE $w GROUP BY hora ORDER BY hora ASC");
$st->execute($params); $por_hora = $st->fetchAll();
$horas_data = array_fill(0, 24, 0);
foreach ($por_hora as $r) $horas_data[(int)$r['hora']] = (int)$r['total'];

// ── Por dispositivo ───────────────────────────────────────────────────────────
$st = $pdo->prepare("SELECT dispositivo, COUNT(*) as total FROM visitor_analytics WHERE $w GROUP BY dispositivo ORDER BY total DESC");
$st->execute($params); $por_disp = $st->fetchAll();

// ── Por navegador ─────────────────────────────────────────────────────────────
$st = $pdo->prepare("SELECT navegador, COUNT(*) as total FROM visitor_analytics WHERE $w GROUP BY navegador ORDER BY total DESC LIMIT 8");
$st->execute($params); $por_nav = $st->fetchAll();

// ── Por OS ────────────────────────────────────────────────────────────────────
$st = $pdo->prepare("SELECT os, COUNT(*) as total FROM visitor_analytics WHERE $w GROUP BY os ORDER BY total DESC LIMIT 8");
$st->execute($params); $por_os = $st->fetchAll();

// ── Páginas más visitadas ─────────────────────────────────────────────────────
$st = $pdo->prepare("SELECT pagina, titulo, COUNT(*) as total FROM visitor_analytics WHERE $w GROUP BY pagina, titulo ORDER BY total DESC LIMIT 15");
$st->execute($params); $top_paginas = $st->fetchAll();

// ── Últimas visitas ───────────────────────────────────────────────────────────
$st = $pdo->prepare("SELECT * FROM visitor_analytics WHERE $w ORDER BY created_at DESC LIMIT 50");
$st->execute($params); $ultimas = $st->fetchAll();

// ── Preparar datos para gráficas JS ──────────────────────────────────────────
$dias_labels = array_column($por_dia, 'dia');
$dias_data   = array_map('intval', array_column($por_dia, 'total'));
$disp_labels = array_column($por_disp, 'dispositivo');
$disp_data   = array_map('intval', array_column($por_disp, 'total'));
$nav_labels  = array_column($por_nav, 'navegador');
$nav_data    = array_map('intval', array_column($por_nav, 'total'));
$os_labels   = array_column($por_os, 'os');
$os_data     = array_map('intval', array_column($por_os, 'total'));

$disp_icons = ['pc' => 'bi-laptop', 'celular' => 'bi-phone', 'tablet' => 'bi-tablet'];
$disp_colors = ['pc' => '#3b82f6', 'celular' => '#C8102C', 'tablet' => '#f59e0b'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Analíticas — Panel DIF</title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/admin.css?v=7">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.kpi-card { border-radius:14px; padding:1.4rem 1.6rem; color:#fff; }
.kpi-card .kpi-num { font-size:2.4rem; font-weight:800; line-height:1; }
.kpi-card .kpi-lbl { font-size:.8rem; opacity:.85; margin-top:4px; }
.kpi-card .kpi-icon { font-size:2.2rem; opacity:.3; }
.chart-box { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.2rem 1.4rem; }
.chart-box h6 { font-weight:700; color:#1a2332; margin-bottom:1rem; font-size:.9rem; }
.table-vis td, .table-vis th { font-size:.82rem; vertical-align:middle; }
.badge-disp { font-size:.72rem; padding:3px 8px; border-radius:20px; }
.hora-bar { height:6px; border-radius:3px; background:#C8102C; display:inline-block; min-width:2px; }
</style>
</head>
<body>
<div class="d-flex">
<?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>

<div class="main-content">
<nav class="navbar navbar-dark px-3">
    <button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
    <span class="navbar-brand mb-0 h6"><i class="bi bi-graph-up me-1"></i> Analíticas de Visitas</span>
    <a href="logout" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
</nav>

<div class="container-fluid p-4">

<!-- Filtros -->
<div class="card mb-4 border-0 shadow-sm">
  <div class="card-body py-3">
    <form method="GET" action="analytics" class="row g-2 align-items-end">
      <div class="col-sm-3">
        <label class="form-label fw-semibold mb-1" style="font-size:.82rem;">Desde</label>
        <input type="date" class="form-control form-control-sm" name="fecha_ini" value="<?= htmlspecialchars($fecha_ini) ?>">
      </div>
      <div class="col-sm-3">
        <label class="form-label fw-semibold mb-1" style="font-size:.82rem;">Hasta</label>
        <input type="date" class="form-control form-control-sm" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">
      </div>
      <div class="col-sm-3">
        <label class="form-label fw-semibold mb-1" style="font-size:.82rem;">Dispositivo</label>
        <select class="form-select form-select-sm" name="dispositivo">
          <option value="">Todos</option>
          <option value="pc" <?= $dispositivo==='pc'?'selected':'' ?>>PC / Laptop</option>
          <option value="celular" <?= $dispositivo==='celular'?'selected':'' ?>>Celular</option>
          <option value="tablet" <?= $dispositivo==='tablet'?'selected':'' ?>>Tablet</option>
        </select>
      </div>
      <div class="col-sm-3 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
        <a href="analytics" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></a>
      </div>
    </form>
  </div>
</div>

<!-- KPIs -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="kpi-card d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#C8102C,#a00d23);">
      <div><div class="kpi-num"><?= number_format($kpi['total'] ?? 0) ?></div><div class="kpi-lbl">Total de visitas</div></div>
      <i class="bi bi-eye kpi-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
      <div><div class="kpi-num"><?= number_format($kpi['sesiones'] ?? 0) ?></div><div class="kpi-lbl">Sesiones únicas</div></div>
      <i class="bi bi-person-check kpi-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#10b981,#047857);">
      <div><div class="kpi-num"><?= number_format($kpi['unicos'] ?? 0) ?></div><div class="kpi-lbl">IPs únicas</div></div>
      <i class="bi bi-people kpi-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#f59e0b,#b45309);">
      <div>
        <?php $dias_count = count($por_dia); $prom = $dias_count > 0 ? round(($kpi['total'] ?? 0) / $dias_count) : 0; ?>
        <div class="kpi-num"><?= number_format($prom) ?></div>
        <div class="kpi-lbl">Promedio diario</div>
      </div>
      <i class="bi bi-calendar-check kpi-icon"></i>
    </div>
  </div>
</div>

<!-- Gráficas fila 1 -->
<div class="row g-3 mb-4">
  <!-- Visitas por día -->
  <div class="col-lg-8">
    <div class="chart-box h-100">
      <h6><i class="bi bi-bar-chart-line me-1 text-danger"></i> Visitas por día</h6>
      <canvas id="chartDias" height="100"></canvas>
    </div>
  </div>
  <!-- Dispositivos -->
  <div class="col-lg-4">
    <div class="chart-box h-100">
      <h6><i class="bi bi-pie-chart me-1 text-danger"></i> Por dispositivo</h6>
      <canvas id="chartDisp" height="180"></canvas>
      <div class="mt-3">
        <?php foreach ($por_disp as $d): ?>
        <div class="d-flex align-items-center justify-content-between mb-1">
          <span style="font-size:.82rem;"><i class="bi <?= $disp_icons[$d['dispositivo']] ?? 'bi-display' ?> me-1"></i><?= ucfirst(htmlspecialchars($d['dispositivo'])) ?></span>
          <span class="badge" style="background:<?= $disp_colors[$d['dispositivo']] ?? '#6b7280' ?>;font-size:.75rem;"><?= number_format($d['total']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Gráficas fila 2 -->
<div class="row g-3 mb-4">
  <!-- Por hora -->
  <div class="col-lg-6">
    <div class="chart-box">
      <h6><i class="bi bi-clock me-1 text-danger"></i> Visitas por hora del día</h6>
      <canvas id="chartHoras" height="120"></canvas>
    </div>
  </div>
  <!-- Navegadores -->
  <div class="col-lg-3">
    <div class="chart-box">
      <h6><i class="bi bi-browser-chrome me-1 text-danger"></i> Navegadores</h6>
      <canvas id="chartNav" height="200"></canvas>
    </div>
  </div>
  <!-- OS -->
  <div class="col-lg-3">
    <div class="chart-box">
      <h6><i class="bi bi-cpu me-1 text-danger"></i> Sistemas Operativos</h6>
      <canvas id="chartOs" height="200"></canvas>
    </div>
  </div>
</div>

<!-- Páginas más visitadas -->
<div class="row g-3 mb-4">
  <div class="col-lg-5">
    <div class="chart-box">
      <h6><i class="bi bi-file-earmark-text me-1 text-danger"></i> Páginas más visitadas</h6>
      <table class="table table-sm table-vis mb-0">
        <thead><tr><th>#</th><th>Página</th><th>Visitas</th></tr></thead>
        <tbody>
          <?php $max_p = $top_paginas[0]['total'] ?? 1; foreach ($top_paginas as $i => $p): ?>
          <tr>
            <td class="text-muted"><?= $i+1 ?></td>
            <td>
              <div style="font-size:.8rem;font-weight:600;color:#1a2332;"><?= htmlspecialchars($p['titulo'] ?: $p['pagina']) ?></div>
              <div style="font-size:.72rem;color:#9ca3af;"><?= htmlspecialchars($p['pagina']) ?></div>
              <div style="height:3px;background:#e2e8f0;border-radius:2px;margin-top:3px;">
                <div style="height:3px;background:#C8102C;border-radius:2px;width:<?= round($p['total']/$max_p*100) ?>%;"></div>
              </div>
            </td>
            <td class="fw-bold text-danger"><?= number_format($p['total']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Últimas visitas -->
  <div class="col-lg-7">
    <div class="chart-box">
      <h6><i class="bi bi-list-ul me-1 text-danger"></i> Últimas 50 visitas</h6>
      <div style="max-height:380px;overflow-y:auto;">
        <table class="table table-sm table-vis mb-0">
          <thead style="position:sticky;top:0;background:#fff;z-index:1;">
            <tr>
              <th>Fecha / Hora</th>
              <th>Página</th>
              <th>Dispositivo</th>
              <th>OS / Navegador</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ultimas as $v): ?>
            <tr>
              <td style="white-space:nowrap;">
                <div style="font-size:.8rem;font-weight:600;"><?= date('d/m/Y', strtotime($v['created_at'])) ?></div>
                <div style="font-size:.75rem;color:#6b7280;"><?= date('H:i:s', strtotime($v['created_at'])) ?></div>
              </td>
              <td>
                <div style="font-size:.78rem;font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($v['pagina']) ?>">
                  <?= htmlspecialchars($v['titulo'] ?: $v['pagina']) ?>
                </div>
                <?php if ($v['referrer']): ?>
                <div style="font-size:.7rem;color:#9ca3af;">desde: <?= htmlspecialchars(parse_url($v['referrer'], PHP_URL_HOST) ?: $v['referrer']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php $dc = $disp_colors[$v['dispositivo']] ?? '#6b7280'; ?>
                <span class="badge badge-disp" style="background:<?= $dc ?>;">
                  <i class="bi <?= $disp_icons[$v['dispositivo']] ?? 'bi-display' ?> me-1"></i><?= ucfirst(htmlspecialchars($v['dispositivo'])) ?>
                </span>
              </td>
              <td>
                <div style="font-size:.78rem;"><?= htmlspecialchars($v['os'] ?? '—') ?></div>
                <div style="font-size:.72rem;color:#6b7280;"><?= htmlspecialchars($v['navegador'] ?? '—') ?></div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</div><!-- /container -->
</div><!-- /main-content -->
</div><!-- /d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click', function(){ sidebar.classList.toggle('collapsed'); });
const cb = document.getElementById('closeSidebar');
if (cb) cb.addEventListener('click', function(){ sidebar.classList.add('collapsed'); });

const RED = '#C8102C', BLUE = '#3b82f6', GREEN = '#10b981', AMBER = '#f59e0b';
const COLORS = [RED, BLUE, GREEN, AMBER, '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

// Visitas por día
new Chart(document.getElementById('chartDias'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($dias_labels) ?>,
        datasets: [{
            label: 'Visitas',
            data: <?= json_encode($dias_data) ?>,
            backgroundColor: RED + 'cc',
            borderColor: RED,
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: { responsive:true, plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
});

// Dispositivos (doughnut)
new Chart(document.getElementById('chartDisp'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map('ucfirst', $disp_labels)) ?>,
        datasets: [{ data: <?= json_encode($disp_data) ?>, backgroundColor: [RED, BLUE, AMBER], borderWidth: 2 }]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ font:{size:11} } } } }
});

// Por hora
new Chart(document.getElementById('chartHoras'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_map(fn($h) => str_pad($h,2,'0',STR_PAD_LEFT).':00', range(0,23))) ?>,
        datasets: [{
            label: 'Visitas',
            data: <?= json_encode(array_values($horas_data)) ?>,
            borderColor: RED,
            backgroundColor: RED + '22',
            fill: true,
            tension: 0.4,
            pointRadius: 3
        }]
    },
    options: { responsive:true, plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true } } }
});

// Navegadores
new Chart(document.getElementById('chartNav'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($nav_labels) ?>,
        datasets: [{ data: <?= json_encode($nav_data) ?>, backgroundColor: COLORS, borderRadius: 4 }]
    },
    options: { indexAxis:'y', responsive:true, plugins:{ legend:{display:false} }, scales:{ x:{ beginAtZero:true } } }
});

// OS
new Chart(document.getElementById('chartOs'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($os_labels) ?>,
        datasets: [{ data: <?= json_encode($os_data) ?>, backgroundColor: COLORS, borderRadius: 4 }]
    },
    options: { indexAxis:'y', responsive:true, plugins:{ legend:{display:false} }, scales:{ x:{ beginAtZero:true } } }
});
</script>
</body>
</html>
