<?php
/**
 * @author  Sergio Huitron Gomez
 * @project DIF San Mateo Atenco - Sistema de Gestion de Contenido
 *
 * admin/reportes_historial.php - Generador de reportes del historial de actividad
 * Soporta descarga en PDF (dompdf) y Excel (PhpSpreadsheet).
 */

// ---------------------------------------------------------------------------
// Funciones puras — sin efectos secundarios, sin dependencias de sesión/BD
// ---------------------------------------------------------------------------

/**
 * Construye el WHERE clause y el array de parámetros preparados a partir de
 * los filtros recibidos por GET, replicando exactamente la lógica de
 * admin/historial.php.
 *
 * @param  array $get  Normalmente $_GET; se leen las claves:
 *                     fecha_ini, fecha_fin, usuario, seccion, accion
 * @return array{where: string, params: array}
 */
function build_filter_query(array $get): array
{
    $fecha_ini = trim($get['fecha_ini'] ?? date('Y-m-01'));
    $fecha_fin = trim($get['fecha_fin'] ?? date('Y-m-d'));
    $usuario   = trim($get['usuario']   ?? '');
    $seccion   = trim($get['seccion']   ?? '');
    $accion    = trim($get['accion']    ?? '');

    $where  = ['DATE(created_at) BETWEEN ? AND ?'];
    $params = [$fecha_ini, $fecha_fin];

    if ($usuario) {
        $where[]  = 'username LIKE ?';
        $params[] = "%{$usuario}%";
    }
    if ($seccion) {
        $where[]  = 'seccion LIKE ?';
        $params[] = "%{$seccion}%";
    }
    if ($accion) {
        $where[]  = 'accion = ?';
        $params[] = $accion;
    }

    return [
        'where'  => implode(' AND ', $where),
        'params' => $params,
    ];
}

/**
 * Genera el nombre de archivo de descarga con el patrón requerido.
 *
 * @param  string $fecha_ini  Fecha de inicio (YYYY-MM-DD)
 * @param  string $fecha_fin  Fecha de fin    (YYYY-MM-DD)
 * @param  string $ext        Extensión sin punto: 'pdf' o 'xlsx'
 * @return string             Ej.: "historial_2025-01-01_2025-01-31.pdf"
 */
function report_filename(string $fecha_ini, string $fecha_fin, string $ext): string
{
    return "historial_{$fecha_ini}_{$fecha_fin}.{$ext}";
}

/**
 * Genera el HTML completo que dompdf renderizará como PDF.
 *
 * @param  array $registros  Filas de admin_historial (created_at, username, accion, seccion, descripcion)
 * @param  array $stats      Estadísticas por tipo de acción: [['accion'=>..., 'total'=>...], ...]
 * @param  array $stats_dia  Estadísticas por día: [['dia'=>..., 'total'=>...], ...]
 * @param  array $filtros    Filtros aplicados: ['fecha_ini'=>..., 'fecha_fin'=>..., ...]
 * @return string            HTML completo listo para dompdf
 */
function build_pdf_html(array $registros, array $stats, array $stats_dia, array $filtros): string
{
    $fecha_ini  = htmlspecialchars($filtros['fecha_ini'] ?? '', ENT_QUOTES, 'UTF-8');
    $fecha_fin  = htmlspecialchars($filtros['fecha_fin'] ?? '', ENT_QUOTES, 'UTF-8');
    $total      = array_sum(array_column($stats, 'total'));
    $escudo_path = dirname(__DIR__) . '/img/logo_DIF.png';
    $now        = date('d/m/Y H:i');

    // -----------------------------------------------------------------------
    // Gráfica de barras por tipo de acción
    // -----------------------------------------------------------------------
    $max_accion = 0;
    foreach ($stats as $s) {
        if ((int)$s['total'] > $max_accion) {
            $max_accion = (int)$s['total'];
        }
    }

    $chart_acciones = '';
    foreach ($stats as $s) {
        $label   = htmlspecialchars($s['accion'], ENT_QUOTES, 'UTF-8');
        $val     = (int)$s['total'];
        $pct     = $max_accion > 0 ? round($val / $max_accion * 100) : 0;
        $chart_acciones .= '
        <tr>
          <td style="width:90px;font-size:8pt;padding:2px 4px 2px 0;">' . $label . '</td>
          <td style="padding:2px 0;">
            <div style="width:' . $pct . '%;background:#C8102E;height:14px;min-width:2px;"></div>
          </td>
          <td style="width:30px;font-size:8pt;padding:2px 0 2px 4px;">' . $val . '</td>
        </tr>';
    }

    // -----------------------------------------------------------------------
    // Gráfica de barras por día (solo si hay >= 2 días distintos)
    // -----------------------------------------------------------------------
    $chart_dias_html = '';
    if (count($stats_dia) >= 2) {
        $max_dia = 0;
        foreach ($stats_dia as $d) {
            if ((int)$d['total'] > $max_dia) {
                $max_dia = (int)$d['total'];
            }
        }

        $chart_dias_rows = '';
        foreach ($stats_dia as $d) {
            $label   = htmlspecialchars($d['dia'], ENT_QUOTES, 'UTF-8');
            $val     = (int)$d['total'];
            $pct     = $max_dia > 0 ? round($val / $max_dia * 100) : 0;
            $chart_dias_rows .= '
            <tr>
              <td style="width:90px;font-size:7pt;padding:2px 4px 2px 0;">' . $label . '</td>
              <td style="padding:2px 0;">
                <div style="width:' . $pct . '%;background:#6B625A;height:12px;min-width:2px;"></div>
              </td>
              <td style="width:30px;font-size:7pt;padding:2px 0 2px 4px;">' . $val . '</td>
            </tr>';
        }

        $chart_dias_html = '
        <h3 style="font-size:10pt;color:#6B625A;margin:14px 0 4px 0;border-bottom:1px solid #6B625A;padding-bottom:3px;">
            Distribución por Día
        </h3>
        <table style="width:100%;border-collapse:collapse;">
          <tbody>' . $chart_dias_rows . '</tbody>
        </table>';
    }

    // -----------------------------------------------------------------------
    // Filas de la tabla de registros
    // -----------------------------------------------------------------------
    $rows = '';
    foreach ($registros as $i => $r) {
        $bg         = ($i % 2 === 1) ? 'background:#F2F2F2;' : '';
        $fecha      = htmlspecialchars($r['created_at']  ?? '', ENT_QUOTES, 'UTF-8');
        $usuario    = htmlspecialchars($r['username']    ?? '', ENT_QUOTES, 'UTF-8');
        $accion     = htmlspecialchars($r['accion']      ?? '', ENT_QUOTES, 'UTF-8');
        $seccion    = htmlspecialchars($r['seccion']     ?? '', ENT_QUOTES, 'UTF-8');
        $desc       = htmlspecialchars($r['descripcion'] ?? '', ENT_QUOTES, 'UTF-8');
        $disp       = htmlspecialchars(ucfirst($r['dispositivo'] ?? 'pc'), ENT_QUOTES, 'UTF-8');
        $ip         = htmlspecialchars($r['ip']          ?? '', ENT_QUOTES, 'UTF-8');
        $host       = htmlspecialchars($r['hostname']    ?? '', ENT_QUOTES, 'UTF-8');
        $ip_host    = $host ? $ip . '<br><small>' . $host . '</small>' : $ip;
        $rows .= '
        <tr style="' . $bg . '">
          <td style="padding:3px 5px;border-bottom:1px solid #E0E0E0;">' . $fecha   . '</td>
          <td style="padding:3px 5px;border-bottom:1px solid #E0E0E0;">' . $usuario . '</td>
          <td style="padding:3px 5px;border-bottom:1px solid #E0E0E0;">' . $accion  . '</td>
          <td style="padding:3px 5px;border-bottom:1px solid #E0E0E0;">' . $seccion . '</td>
          <td style="padding:3px 5px;border-bottom:1px solid #E0E0E0;">' . $desc    . '</td>
          <td style="padding:3px 5px;border-bottom:1px solid #E0E0E0;">' . $disp    . '</td>
          <td style="padding:3px 5px;border-bottom:1px solid #E0E0E0;">' . $ip_host . '</td>
        </tr>';
    }

    // -----------------------------------------------------------------------
    // Imagen del escudo (base64 para que dompdf la encuentre siempre)
    // -----------------------------------------------------------------------
    $escudo_tag = '';
    if (file_exists($escudo_path) && extension_loaded('gd')) {
        $mime = (substr($escudo_path, -4) === '.png') ? 'image/png' : 'image/jpeg';
        $b64  = base64_encode(file_get_contents($escudo_path));
        $escudo_tag = '<img src="data:' . $mime . ';base64,' . $b64 . '" style="height:50px;vertical-align:middle;margin-right:10px;">';
    }

    // -----------------------------------------------------------------------
    // HTML completo
    // -----------------------------------------------------------------------
    $html = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9pt;
    margin: 0;
    color: #333;
  }
  @page {
    margin: 20mm 15mm 22mm 15mm;
  }
  .header-box {
    background: #C8102E;
    color: #fff;
    padding: 10px 14px;
    margin-bottom: 14px;
  }
  .header-box table {
    width: 100%;
    border-collapse: collapse;
  }
  .header-title {
    font-size: 13pt;
    font-weight: bold;
    margin: 0;
  }
  .header-sub {
    font-size: 9pt;
    margin: 2px 0 0 0;
  }
  .section-title {
    font-size: 10pt;
    color: #C8102E;
    margin: 0 0 6px 0;
    border-bottom: 1px solid #C8102E;
    padding-bottom: 3px;
  }
  .stats-total {
    font-size: 9pt;
    margin-bottom: 8px;
  }
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8pt;
  }
  table.data-table thead tr {
    background: #C8102E;
    color: #fff;
  }
  table.data-table thead th {
    padding: 5px 5px;
    text-align: left;
    font-size: 8pt;
  }
  table.data-table tbody td {
    font-size: 8pt;
  }
  .footer {
    position: fixed;
    bottom: -18mm;
    left: 0;
    right: 0;
    font-size: 7pt;
    color: #6B625A;
    border-top: 1px solid #6B625A;
    padding-top: 3px;
  }
  .footer table {
    width: 100%;
    border-collapse: collapse;
  }
  .page-number:before {
    content: "Página " counter(page) " de " counter(pages);
  }
</style>
</head>
<body>

<!-- FOOTER fijo -->
<div class="footer">
  <table>
    <tr>
      <td><span class="page-number"></span></td>
      <td style="text-align:right;">Generado: ' . $now . '</td>
    </tr>
  </table>
</div>

<!-- ENCABEZADO INSTITUCIONAL -->
<div class="header-box">
  <table>
    <tr>
      <td style="width:60px;vertical-align:middle;">' . $escudo_tag . '</td>
      <td style="vertical-align:middle;">
        <p class="header-title">DIF San Mateo Atenco</p>
        <p class="header-sub">Reporte de Historial de Actividad</p>
        <p class="header-sub">Periodo: ' . $fecha_ini . ' al ' . $fecha_fin . '</p>
      </td>
    </tr>
  </table>
</div>

<!-- ESTADÍSTICAS -->
<h2 class="section-title">Estadísticas</h2>
<p class="stats-total">Total de eventos: <strong>' . $total . '</strong></p>

<h3 style="font-size:10pt;color:#6B625A;margin:0 0 4px 0;border-bottom:1px solid #6B625A;padding-bottom:3px;">
    Eventos por Tipo de Acción
</h3>
<table style="width:100%;border-collapse:collapse;margin-bottom:10px;">
  <tbody>' . $chart_acciones . '</tbody>
</table>

' . $chart_dias_html . '

<!-- TABLA DE REGISTROS -->
<h2 class="section-title" style="margin-top:16px;">Detalle de Actividad</h2>
<table class="data-table">
  <thead>
    <tr>
      <th style="width:15%;">Fecha/Hora</th>
      <th style="width:11%;">Usuario</th>
      <th style="width:9%;">Acción</th>
      <th style="width:12%;">Sección</th>
      <th style="width:33%;">Descripción</th>
      <th style="width:9%;">Dispositivo</th>
      <th style="width:11%;">IP / Host</th>
    </tr>
  </thead>
  <tbody>' . $rows . '</tbody>
</table>

</body>
</html>';

    return $html;
}

/**
 * Genera el objeto Spreadsheet con hojas "Historial" y "Estadísticas".
 *
 * @param  array $registros      Filas de admin_historial
 * @param  array $stats          Estadísticas por tipo de acción: [['accion'=>..., 'total'=>...], ...]
 * @param  array $stats_seccion  Estadísticas por sección: [['seccion'=>..., 'total'=>...], ...]
 * @param  array $filtros        Filtros aplicados: ['fecha_ini'=>..., 'fecha_fin'=>..., ...]
 * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
 */
function build_excel(array $registros, array $stats, array $stats_seccion, array $filtros): \PhpOffice\PhpSpreadsheet\Spreadsheet
{
    $fecha_ini = $filtros['fecha_ini'] ?? date('Y-m-01');
    $fecha_fin = $filtros['fecha_fin'] ?? date('Y-m-d');

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

    // -----------------------------------------------------------------------
    // Hoja 1: "Historial"
    // -----------------------------------------------------------------------
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Historial');

    // Fila 1: Título merged A1:H1
    $titulo = "DIF San Mateo Atenco \u{2014} Reporte de Historial de Actividad | Periodo: {$fecha_ini} al {$fecha_fin}";
    $sheet->setCellValue('A1', $titulo);
    $sheet->mergeCells('A1:J1');

    $tituloStyle = $sheet->getStyle('A1');
    $tituloStyle->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
    $tituloStyle->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFC8102E');
    $tituloStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    // Fila 2: Encabezados
    $headers = ['ID', 'Fecha', 'Hora', 'Usuario', 'Acción', 'Sección', 'Descripción', 'IP', 'Dispositivo', 'Hostname'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '2', $header);
        $col++;
    }

    $headerStyle = $sheet->getStyle('A2:J2');
    $headerStyle->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
    $headerStyle->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFC8102E');

    // AutoFilter en fila 2
    $sheet->setAutoFilter('A2:J2');

    // Filas 3+: Datos de registros
    foreach ($registros as $i => $r) {
        $rowNum = $i + 3;

        $sheet->setCellValue('A' . $rowNum, $r['id']          ?? '');
        $sheet->setCellValue('B' . $rowNum, $r['fecha']       ?? '');
        $sheet->setCellValue('C' . $rowNum, $r['hora']        ?? '');
        $sheet->setCellValue('D' . $rowNum, $r['username']    ?? '');
        $sheet->setCellValue('E' . $rowNum, $r['accion']      ?? '');
        $sheet->setCellValue('F' . $rowNum, $r['seccion']     ?? '');
        $sheet->setCellValue('G' . $rowNum, $r['descripcion'] ?? '');
        $sheet->setCellValue('H' . $rowNum, $r['ip']          ?? '');
        $sheet->setCellValue('I' . $rowNum, ucfirst($r['dispositivo'] ?? 'pc'));
        $sheet->setCellValue('J' . $rowNum, $r['hostname']    ?? '');

        // Filas alternas: índice par (0, 2, 4...) = filas 3, 5, 7... → fondo gris
        if ($i % 2 === 0) {
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF2F2F2');
        }
    }

    // Anchos de columna
    $colWidths = ['A' => 6, 'B' => 12, 'C' => 10, 'D' => 15, 'E' => 12, 'F' => 20, 'G' => 40, 'H' => 15, 'I' => 12, 'J' => 25];
    foreach ($colWidths as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }

    // -----------------------------------------------------------------------
    // Hoja 2: "Estadísticas"
    // -----------------------------------------------------------------------
    $statsSheet = $spreadsheet->createSheet();
    $statsSheet->setTitle('Estadísticas');

    // Sección A (columnas A-B): Conteo por tipo de acción
    $statsSheet->setCellValue('A1', 'Tipo de Acción');
    $statsSheet->setCellValue('B1', 'Total');
    $statsSheet->getStyle('A1')->getFont()->setBold(true);
    $statsSheet->getStyle('B1')->getFont()->setBold(true);

    foreach ($stats as $j => $s) {
        $r = $j + 2;
        $statsSheet->setCellValue('A' . $r, $s['accion'] ?? '');
        $statsSheet->setCellValue('B' . $r, (int)($s['total'] ?? 0));
    }

    // Sección D (columnas D-E): Conteo por sección
    $statsSheet->setCellValue('D1', 'Sección');
    $statsSheet->setCellValue('E1', 'Total');
    $statsSheet->getStyle('D1')->getFont()->setBold(true);
    $statsSheet->getStyle('E1')->getFont()->setBold(true);

    foreach ($stats_seccion as $j => $s) {
        $r = $j + 2;
        $statsSheet->setCellValue('D' . $r, $s['seccion'] ?? '');
        $statsSheet->setCellValue('E' . $r, (int)($s['total'] ?? 0));
    }

    // Volver a la hoja Historial como activa
    $spreadsheet->setActiveSheetIndex(0);

    return $spreadsheet;
}

// ---------------------------------------------------------------------------
// Requires y setup
// ---------------------------------------------------------------------------
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/historial_helper.php';
require_once __DIR__ . '/sidebar_sections.php';

$autoload_path = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload_path)) {
    die('<p style="color:red;font-family:sans-serif;padding:20px;">Error: Ejecuta <code>composer install</code> para instalar las dependencias.</p>');
}
require_once $autoload_path;

// ---------------------------------------------------------------------------
// Verificar rol
// ---------------------------------------------------------------------------
$rol = $_SESSION['admin_rol'] ?? '';
if (!in_array($rol, ['admin', 'editor'])) {
    header('Location: dashboard?error=acceso_denegado');
    exit;
}

// ---------------------------------------------------------------------------
// Leer y sanear parámetros GET
// ---------------------------------------------------------------------------
$fecha_ini = trim($_GET['fecha_ini'] ?? date('Y-m-01'));
$fecha_fin = trim($_GET['fecha_fin'] ?? date('Y-m-d'));
$filtro_usuario = trim($_GET['usuario'] ?? '');
$filtro_seccion = trim($_GET['seccion'] ?? '');
$filtro_accion  = trim($_GET['accion']  ?? '');
$action = trim($_GET['action'] ?? '');

// Sanear fechas
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_ini)) $fecha_ini = date('Y-m-01');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) $fecha_fin = date('Y-m-d');

// ── Modo PDF ──────────────────────────────────────────────────────────────
if ($action === 'pdf') {
    try {
        $filter = build_filter_query($_GET);
        $pdo = get_db();
        
        // Registros completos para el PDF
        $stmtReg = $pdo->prepare("SELECT * FROM admin_historial WHERE {$filter['where']} ORDER BY created_at DESC");
        $stmtReg->execute($filter['params']);
        $registros = $stmtReg->fetchAll();
        
        // Stats por acción
        $stmtStats = $pdo->prepare("SELECT accion, COUNT(*) as total FROM admin_historial WHERE {$filter['where']} GROUP BY accion ORDER BY total DESC");
        $stmtStats->execute($filter['params']);
        $stats = $stmtStats->fetchAll();
        
        // Stats por día
        $stmtDia = $pdo->prepare("SELECT DATE(created_at) as dia, COUNT(*) as total FROM admin_historial WHERE {$filter['where']} GROUP BY DATE(created_at) ORDER BY dia ASC");
        $stmtDia->execute($filter['params']);
        $stats_dia = $stmtDia->fetchAll();
        
        $filtros = ['fecha_ini' => $fecha_ini, 'fecha_fin' => $fecha_fin];
        $html = build_pdf_html($registros, $stats, $stats_dia, $filtros);
        
        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => false]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Registrar en historial
        registrar_historial($pdo, 'reporte', 'Reportes', "PDF descargado. Periodo: {$fecha_ini} al {$fecha_fin}");
        
        $filename = report_filename($fecha_ini, $fecha_fin, 'pdf');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        echo $dompdf->output();
        exit;
        
    } catch (\Throwable $e) {
        error_log('reportes_historial PDF error: ' . $e->getMessage());
        http_response_code(500);
        echo '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:20px;"><h2>Error al generar el PDF</h2><p>Ocurrió un error interno. Por favor intenta de nuevo.</p><a href="reportes_historial">Volver</a></body></html>';
        exit;
    }
}

// ── Modo Excel ────────────────────────────────────────────────────────────
if ($action === 'excel') {
    try {
        $filter = build_filter_query($_GET);
        $pdo = get_db();
        
        // Registros completos para el Excel
        $stmtReg = $pdo->prepare("SELECT id, DATE(created_at) as fecha, TIME(created_at) as hora, username, accion, seccion, descripcion, ip, dispositivo, hostname FROM admin_historial WHERE {$filter['where']} ORDER BY created_at DESC");
        $stmtReg->execute($filter['params']);
        $registros = $stmtReg->fetchAll();
        
        // Stats por acción
        $stmtStats = $pdo->prepare("SELECT accion, COUNT(*) as total FROM admin_historial WHERE {$filter['where']} GROUP BY accion ORDER BY total DESC");
        $stmtStats->execute($filter['params']);
        $stats = $stmtStats->fetchAll();
        
        // Stats por sección
        $stmtSec = $pdo->prepare("SELECT seccion, COUNT(*) as total FROM admin_historial WHERE {$filter['where']} GROUP BY seccion ORDER BY total DESC");
        $stmtSec->execute($filter['params']);
        $stats_seccion = $stmtSec->fetchAll();
        
        $filtros = ['fecha_ini' => $fecha_ini, 'fecha_fin' => $fecha_fin];
        $spreadsheet = build_excel($registros, $stats, $stats_seccion, $filtros);
        
        // Registrar en historial
        registrar_historial($pdo, 'reporte', 'Reportes', "Excel descargado. Periodo: {$fecha_ini} al {$fecha_fin}");
        
        $filename = report_filename($fecha_ini, $fecha_fin, 'xlsx');

        // Escribir a archivo temporal para evitar contaminación del buffer de salida
        $tmpFile = tempnam(sys_get_temp_dir(), 'dif_excel_');
        $writer  = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tmpFile);

        // Limpiar todos los buffers antes de enviar el binario
        while (ob_get_level() > 0) { ob_end_clean(); }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tmpFile));
        header('Cache-Control: private, max-age=0, must-revalidate');

        readfile($tmpFile);
        unlink($tmpFile);
        exit;
        
    } catch (\Exception $e) {
        error_log('reportes_historial Excel error: ' . $e->getMessage());
        http_response_code(500);
        echo '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:20px;"><h2>Error al generar el Excel</h2><p><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p><a href="reportes_historial">Volver</a></body></html>';
        exit;
    }
}

// ---------------------------------------------------------------------------
// Modo HTML — ejecutar consultas y mostrar la vista
// ---------------------------------------------------------------------------
$pdo = get_db();

$filter = build_filter_query($_GET);
$whereStr = $filter['where'];
$params   = $filter['params'];

// Registros (sin paginación para reportes — máx. 50 en vista previa)
$stmtReg = $pdo->prepare("SELECT * FROM admin_historial WHERE {$whereStr} ORDER BY created_at DESC");
$stmtReg->execute($params);
$registros = $stmtReg->fetchAll();
$total = count($registros);

// Stats por acción
$stmtStats = $pdo->prepare("SELECT accion, COUNT(*) as total FROM admin_historial WHERE {$whereStr} GROUP BY accion ORDER BY total DESC");
$stmtStats->execute($params);
$stats = $stmtStats->fetchAll();

// Stats por día
$stmtDia = $pdo->prepare("SELECT DATE(created_at) as dia, COUNT(*) as total FROM admin_historial WHERE {$whereStr} GROUP BY DATE(created_at) ORDER BY dia ASC");
$stmtDia->execute($params);
$stats_dia = $stmtDia->fetchAll();

// Stats por sección
$stmtSec = $pdo->prepare("SELECT seccion, COUNT(*) as total FROM admin_historial WHERE {$whereStr} GROUP BY seccion ORDER BY total DESC");
$stmtSec->execute($params);
$stats_seccion = $stmtSec->fetchAll();

// Listas para selects del formulario
$secciones = $pdo->query("SELECT DISTINCT seccion FROM admin_historial ORDER BY seccion ASC")->fetchAll(PDO::FETCH_COLUMN);
$usuarios  = $pdo->query("SELECT DISTINCT username FROM admin_historial ORDER BY username ASC")->fetchAll(PDO::FETCH_COLUMN);

// Vista previa: primeros 50 registros
$preview = array_slice($registros, 0, 50);

// Construir query string con filtros actuales (para botones PDF/Excel)
$filtros_qs = http_build_query([
    'fecha_ini' => $fecha_ini,
    'fecha_fin' => $fecha_fin,
    'usuario'   => $filtro_usuario,
    'seccion'   => $filtro_seccion,
    'accion'    => $filtro_accion,
]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reportes de Historial - Admin DIF</title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/admin.css?v=7">
<style>
.stat-card { border-radius: 10px; padding: 16px; text-align: center; }
</style>
</head>
<body>
<div class="d-flex">
<?php render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
<div class="main-content">
<nav class="navbar navbar-light bg-white shadow-sm px-3">
<button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
<span class="navbar-brand mb-0 h6"><i class="bi bi-file-earmark-bar-graph me-1"></i> Reportes de Historial</span>
<div class="ms-auto d-flex gap-2">
<a href="logout" class="btn btn-sm btn-action-delete"><i class="bi bi-box-arrow-right"></i> Salir</a>
</div>
</nav>

<div class="container-fluid p-4">
<?php require_once __DIR__ . '/page_help.php'; page_help('reportes_historial'); ?>

<!-- Tarjetas de estadísticas -->
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
<div style="font-size:24pt;font-weight:700;color:rgb(107,98,90);"><?= (int)$st['total'] ?></div>
<div style="font-size:11px;color:#666;font-weight:600;"><?= strtoupper(htmlspecialchars($st['accion'])) ?></div>
</div>
</div>
<?php endforeach; ?>
</div>

<!-- Formulario de filtros -->
<div class="card mb-4">
<div class="card-header" style="background:rgb(107,98,90);color:#fff;"><i class="bi bi-funnel me-1"></i> Filtros</div>
<div class="card-body">
<form method="GET" action="reportes_historial" class="row g-2 align-items-end">
<div class="col-md-2"><label class="form-label small">Fecha inicio</label><input type="date" class="form-control form-control-sm" name="fecha_ini" value="<?= htmlspecialchars($fecha_ini) ?>"></div>
<div class="col-md-2"><label class="form-label small">Fecha fin</label><input type="date" class="form-control form-control-sm" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>"></div>
<div class="col-md-2"><label class="form-label small">Usuario</label>
<select class="form-select form-select-sm" name="usuario"><option value="">Todos</option><?php foreach ($usuarios as $u): ?><option value="<?= htmlspecialchars($u) ?>" <?= $filtro_usuario===$u?'selected':'' ?>><?= htmlspecialchars($u) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label small">Sección</label>
<select class="form-select form-select-sm" name="seccion"><option value="">Todas</option><?php foreach ($secciones as $s): ?><option value="<?= htmlspecialchars($s) ?>" <?= $filtro_seccion===$s?'selected':'' ?>><?= htmlspecialchars($s) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label small">Acción</label>
<select class="form-select form-select-sm" name="accion"><option value="">Todas</option>
<?php foreach (['crear','editar','eliminar','subir','login','logout','reorden','reporte'] as $a): ?><option value="<?= $a ?>" <?= $filtro_accion===$a?'selected':'' ?>><?= ucfirst($a) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2 d-flex gap-1"><button type="submit" class="btn btn-sm btn-danger w-100"><i class="bi bi-search me-1"></i> Filtrar</button><a href="reportes_historial" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></a></div>
</form>
</div>
</div>

<!-- Botones de descarga o mensaje informativo -->
<?php if ($total > 0): ?>
<div class="d-flex gap-2 mb-4">
<a href="reportes_historial?<?= $filtros_qs ?>&action=pdf" class="btn btn-danger">
<i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
</a>
<a href="reportes_historial?<?= $filtros_qs ?>&action=excel" class="btn btn-success">
<i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel
</a>
</div>
<?php else: ?>
<div class="alert alert-info d-flex align-items-center mb-4" role="alert">
<i class="bi bi-info-circle-fill me-2"></i>
No hay registros para los filtros seleccionados. Ajusta el rango de fechas u otros criterios.
</div>
<?php endif; ?>

<!-- Tabla de vista previa -->
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center" style="background:rgb(107,98,90);color:#fff;">
<span><i class="bi bi-table me-1"></i> Vista previa <span class="badge bg-light text-dark ms-1"><?= $total ?> registros<?= $total > 50 ? ' (mostrando primeros 50)' : '' ?></span></span>
</div>
<div class="card-body p-0">
<?php if (empty($preview)): ?>
<div class="text-center text-muted py-5"><i class="bi bi-clock-history" style="font-size:3rem;"></i><p class="mt-3">No hay registros para el periodo seleccionado.</p></div>
<?php else: ?>
<div class="table-responsive">
<table class="table table-hover align-middle mb-0" style="font-size:13px;">
<thead style="background:rgb(200,16,44);color:#fff;">
<tr><th style="width:130px;">Fecha y Hora</th><th style="width:90px;">Usuario</th><th style="width:80px;">Acción</th><th style="width:130px;">Sección</th><th>Descripción</th><th style="width:80px;">Dispositivo</th><th style="width:130px;">IP / Host</th></tr>
</thead>
<tbody>
<?php foreach ($preview as $r): ?>
<tr>
<td><small><?= date('d/m/Y', strtotime($r['created_at'])) ?><br><strong><?= date('H:i:s', strtotime($r['created_at'])) ?></strong></small></td>
<td><span class="badge bg-secondary"><?= htmlspecialchars($r['username'] ?? '—') ?></span></td>
<td><?= historial_badge($r['accion']) ?></td>
<td style="color:rgb(107,98,90);font-weight:600;"><?= htmlspecialchars($r['seccion']) ?></td>
<td class="text-muted small"><?= htmlspecialchars($r['descripcion'] ?? '—') ?></td>
<td><small><?php
    $di = ($r['dispositivo'] ?? 'pc') === 'celular'
        ? '<i class="bi bi-phone"></i> Celular'
        : (($r['dispositivo'] ?? 'pc') === 'tablet'
            ? '<i class="bi bi-tablet"></i> Tablet'
            : '<i class="bi bi-laptop"></i> PC');
    echo $di;
?></small></td>
<td><small class="text-muted"><?= htmlspecialchars($r['ip'] ?? '—') ?><?php if (!empty($r['hostname'])): ?><br><span style="color:#aaa;font-size:11px;"><?= htmlspecialchars($r['hostname']) ?></span><?php endif; ?></small></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>
</div>

</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var sb=document.getElementById('sidebar');
if(window.innerWidth<=768)sb.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click',function(){sb.classList.toggle('collapsed');});
var cb=document.getElementById('closeSidebar');
if(cb)cb.addEventListener('click',function(){sb.classList.add('collapsed');});
</script>
</body></html>
