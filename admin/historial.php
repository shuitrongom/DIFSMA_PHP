<?php
/**
 * @author  Sergio Huitron Gomez
 * @copyright 2025-2026 Sergio Huitron Gomez. Todos los derechos reservados.
 * @project DIF San Mateo Atenco - Sistema de Gestion de Contenido
 *
 * admin/historial.php - Historial de actividad y generador de reportes
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db();

// Filtros
$filtro_usuario  = trim($_GET['usuario'] ?? '');
$filtro_seccion  = trim($_GET['seccion'] ?? '');
$filtro_accion   = trim($_GET['accion'] ?? '');
$filtro_fecha_ini = trim($_GET['fecha_ini'] ?? date('Y-m-01'));
$filtro_fecha_fin = trim($_GET['fecha_fin'] ?? date('Y-m-d'));
$por_pagina = 50;
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$offset = ($pagina - 1) * $por_pagina;

// Construir query con filtros
$where = ['DATE(created_at) BETWEEN ? AND ?'];
$params = [$filtro_fecha_ini, $filtro_fecha_fin];
if ($filtro_usuario) { $where[] = 'username LIKE ?'; $params[] = "%{$filtro_usuario}%"; }
if ($filtro_seccion) { $where[] = 'seccion LIKE ?'; $params[] = "%{$filtro_seccion}%"; }
if ($filtro_accion)  { $where[] = 'accion = ?'; $params[] = $filtro_accion; }
$whereStr = implode(' AND ', $where);

// Total
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM admin_historial WHERE {$whereStr}");
$stmtTotal->execute($params);
$total = (int) $stmtTotal->fetchColumn();
$totalPaginas = max(1, ceil($total / $por_pagina));

// Registros
$stmt = $pdo->prepare("SELECT * FROM admin_historial WHERE {$whereStr} ORDER BY created_at DESC LIMIT {$por_pagina} OFFSET {$offset}");
$stmt->execute($params);
$registros = $stmt->fetchAll();

// Estadisticas del periodo
$stmtStats = $pdo->prepare("SELECT accion, COUNT(*) as total FROM admin_historial WHERE {$whereStr} GROUP BY accion ORDER BY total DESC");
$stmtStats->execute($params);
$stats = $stmtStats->fetchAll();

// Secciones unicas para filtro
$secciones = $pdo->query("SELECT DISTINCT seccion FROM admin_historial ORDER BY seccion ASC")->fetchAll(PDO::FETCH_COLUMN);
$usuarios  = $pdo->query("SELECT DISTINCT username FROM admin_historial ORDER BY username ASC")->fetchAll(PDO::FETCH_COLUMN);

// Accion eliminar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_log') {
    $token = $_POST['csrf_token'] ?? '';
    if (csrf_validate($token) && ($_SESSION['admin_rol'] ?? '') === 'admin') {
        $pdo->prepare('DELETE FROM admin_historial WHERE id = ?')->execute([(int)($_POST['log_id'] ?? 0)]);
    }
    header('Location: historial.php?' . http_build_query($_GET)); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'clear_all') {
    $token = $_POST['csrf_token'] ?? '';
    if (csrf_validate($token) && ($_SESSION['admin_rol'] ?? '') === 'admin') {
        $pdo->exec('DELETE FROM admin_historial');
    }
    header('Location: historial.php'); exit;
}

$token = csrf_token();
require_once __DIR__ . '/sidebar_sections.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Historial de Actividad - Admin DIF</title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/admin.css?v=7">
<style>
@media print {
    .no-print, .sidebar, nav.navbar, .btn, form, .pagination { display: none !important; }
    body { margin: 0; font-family: 'Segoe UI', sans-serif; }
    .print-header { display: block !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    .container-fluid { padding: 0 !important; }
    table { font-size: 10pt; }
    .badge { border: 1px solid #ccc; padding: 2px 6px; }
}
.print-header { display: none; }
.stat-card { border-radius: 10px; padding: 16px; text-align: center; }
</style>
</head>
<body>
<div class="d-flex">
<?php render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
<div class="main-content">
<nav class="navbar navbar-light bg-white shadow-sm px-3 no-print">
<button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
<span class="navbar-brand mb-0 h6"><i class="bi bi-clock-history me-1"></i> Historial de Actividad</span>
<div class="ms-auto d-flex gap-2">
<button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i> Imprimir Reporte</button>
<a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Salir</a>
</div>
</nav>

<!-- CABECERA DEL REPORTE (solo visible al imprimir) -->
<div class="print-header" style="padding:20px 30px;border-bottom:3px solid rgb(200,16,44);margin-bottom:20px;">
<table style="width:100%;"><tr>
<td style="width:80px;"><img src="../img/escudo.png" style="height:70px;" alt="DIF"></td>
<td style="padding-left:16px;">
<div style="font-size:18pt;font-weight:700;color:rgb(200,16,44);">DIF San Mateo Atenco</div>
<div style="font-size:12pt;color:rgb(107,98,90);">Reporte de Historial de Actividad del Sistema</div>
<div style="font-size:10pt;color:#666;">Periodo: <?= htmlspecialchars($filtro_fecha_ini) ?> al <?= htmlspecialchars($filtro_fecha_fin) ?> | Generado: <?= date('d/m/Y H:i') ?></div>
</td>
<td style="text-align:right;"><img src="../img/UNIDOS.png" style="height:50px;" alt="Unidos con Amor"></td>
</tr></table>
</div>

<div class="container-fluid p-4">

<!-- Estadisticas -->
<div class="row g-3 mb-4">
<div class="col-6 col-md-3">
<div class="stat-card" style="background:rgba(200,16,44,0.08);border:1px solid rgba(200,16,44,0.2);">
<div style="font-size:28pt;font-weight:700;color:rgb(200,16,44);"><?= $total ?></div>
<div style="font-size:11px;color:rgb(107,98,90);font-weight:600;">TOTAL EVENTOS</div>
</div>
</div>
<?php foreach (array_slice($stats, 0, 3) as $st): ?>
<div class="col-6 col-md-3">
<div class="stat-card" style="background:#f8f9fa;border:1px solid #dee2e6;">
<div style="font-size:24pt;font-weight:700;color:rgb(107,98,90);"><?= $st['total'] ?></div>
<div style="font-size:11px;color:#666;font-weight:600;"><?= strtoupper(htmlspecialchars($st['accion'])) ?></div>
</div>
</div>
<?php endforeach; ?>
</div>

<!-- Filtros -->
<div class="card mb-4 no-print">
<div class="card-header" style="background:rgb(107,98,90);color:#fff;"><i class="bi bi-funnel me-1"></i> Filtros</div>
<div class="card-body">
<form method="GET" action="historial.php" class="row g-2 align-items-end">
<div class="col-md-2"><label class="form-label small">Fecha inicio</label><input type="date" class="form-control form-control-sm" name="fecha_ini" value="<?= htmlspecialchars($filtro_fecha_ini) ?>"></div>
<div class="col-md-2"><label class="form-label small">Fecha fin</label><input type="date" class="form-control form-control-sm" name="fecha_fin" value="<?= htmlspecialchars($filtro_fecha_fin) ?>"></div>
<div class="col-md-2"><label class="form-label small">Usuario</label>
<select class="form-select form-select-sm" name="usuario"><option value="">Todos</option><?php foreach ($usuarios as $u): ?><option value="<?= htmlspecialchars($u) ?>" <?= $filtro_usuario===$u?'selected':'' ?>><?= htmlspecialchars($u) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label small">Seccion</label>
<select class="form-select form-select-sm" name="seccion"><option value="">Todas</option><?php foreach ($secciones as $s): ?><option value="<?= htmlspecialchars($s) ?>" <?= $filtro_seccion===$s?'selected':'' ?>><?= htmlspecialchars($s) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label small">Accion</label>
<select class="form-select form-select-sm" name="accion"><option value="">Todas</option>
<?php foreach (['crear','editar','eliminar','subir','login','logout','reorden'] as $a): ?><option value="<?= $a ?>" <?= $filtro_accion===$a?'selected':'' ?>><?= ucfirst($a) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2 d-flex gap-1"><button type="submit" class="btn btn-sm btn-danger w-100"><i class="bi bi-search me-1"></i> Filtrar</button><a href="historial.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></a></div>
</form>
</div>
</div>

<!-- Tabla de registros -->
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center" style="background:rgb(107,98,90);color:#fff;">
<span><i class="bi bi-table me-1"></i> Registros <span class="badge bg-light text-dark ms-1"><?= $total ?></span></span>
<?php if (($_SESSION['admin_rol'] ?? '') === 'admin'): ?>
<form method="POST" action="historial.php" class="d-inline no-print" onsubmit="return confirm('Eliminar TODO el historial?')">
<input type="hidden" name="action" value="clear_all"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<button type="submit" class="btn btn-sm btn-outline-light"><i class="bi bi-trash me-1"></i> Limpiar todo</button>
</form>
<?php endif; ?>
</div>
<div class="card-body p-0">
<?php if (empty($registros)): ?>
<div class="text-center text-muted py-5"><i class="bi bi-clock-history" style="font-size:3rem;"></i><p class="mt-3">No hay registros para el periodo seleccionado.</p></div>
<?php else: ?>
<div class="table-responsive">
<table class="table table-hover align-middle mb-0" style="font-size:13px;">
<thead style="background:rgb(200,16,44);color:#fff;">
<tr><th style="width:140px;">Fecha y Hora</th><th style="width:100px;">Usuario</th><th style="width:90px;">Accion</th><th style="width:160px;">Seccion</th><th>Descripcion</th><th style="width:110px;">IP</th><th style="width:50px;" class="no-print"></th></tr>
</thead>
<tbody>
<?php foreach ($registros as $r): ?>
<tr>
<td><small><?= date('d/m/Y', strtotime($r['created_at'])) ?><br><strong><?= date('H:i:s', strtotime($r['created_at'])) ?></strong></small></td>
<td><span class="badge bg-secondary"><?= htmlspecialchars($r['username'] ?? '—') ?></span></td>
<td><?= historial_badge($r['accion']) ?></td>
<td style="color:rgb(107,98,90);font-weight:600;"><?= htmlspecialchars($r['seccion']) ?></td>
<td class="text-muted small"><?= htmlspecialchars($r['descripcion'] ?? '—') ?></td>
<td><small class="text-muted"><?= htmlspecialchars($r['ip'] ?? '—') ?></small></td>
<td class="no-print">
<?php if (($_SESSION['admin_rol'] ?? '') === 'admin'): ?>
<form method="POST" action="historial.php?<?= http_build_query($_GET) ?>" class="d-inline" onsubmit="return confirm('Eliminar?')">
<input type="hidden" name="action" value="delete_log"><input type="hidden" name="log_id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
<button type="submit" class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-trash"></i></button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<!-- Paginacion -->
<?php if ($totalPaginas > 1): ?>
<div class="d-flex justify-content-center py-3 no-print">
<nav><ul class="pagination pagination-sm mb-0">
<?php for ($p = 1; $p <= $totalPaginas; $p++): $qp = array_merge($_GET, ['pagina' => $p]); ?>
<li class="page-item <?= $p === $pagina ? 'active' : '' ?>"><a class="page-link" href="historial.php?<?= http_build_query($qp) ?>"><?= $p ?></a></li>
<?php endfor; ?>
</ul></nav>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
</div>

<!-- Pie del reporte (solo impresion) -->
<div class="print-header" style="margin-top:30px;padding-top:15px;border-top:2px solid rgb(200,16,44);text-align:center;font-size:9pt;color:#666;">
<p>DIF San Mateo Atenco &mdash; Sistema de Gestion de Contenido &mdash; Reporte generado el <?= date('d/m/Y \a \l\a\s H:i') ?></p>
<p>Desarrollado por Sergio Huitron Gomez &copy; 2025-2026</p>
</div>

</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>var sb=document.getElementById('sidebar');if(window.innerWidth<=768)sb.classList.add('collapsed');document.getElementById('toggleSidebar').addEventListener('click',function(){sb.classList.toggle('collapsed');});var cb=document.getElementById('closeSidebar');if(cb)cb.addEventListener('click',function(){sb.classList.add('collapsed');});</script>
</body></html>
