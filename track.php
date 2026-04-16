<?php
/**
 * track.php — Endpoint de registro de visitas del sitio público
 * Llamado via fetch() desde el footer de cada página pública.
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/includes/db.php';

$ua       = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$ip_hash  = hash('sha256', $ip . 'dif_salt_2025'); // hash anónimo

// ── Detectar bots ─────────────────────────────────────────────────────────────
$bot_patterns = '/bot|crawl|spider|slurp|mediapartners|googlebot|bingbot|yandex|baidu|duckduck|semrush|ahrefs|majestic|mj12bot/i';
$es_bot = preg_match($bot_patterns, $ua) ? 1 : 0;
if ($es_bot) { echo json_encode(['ok' => true, 'bot' => true]); exit; }

// ── Detectar dispositivo ──────────────────────────────────────────────────────
function det_dispositivo(string $ua): string {
    if (preg_match('/tablet|ipad|kindle|playbook|(android(?!.*mobile))/i', $ua)) return 'tablet';
    if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|windows phone/i', $ua)) return 'celular';
    return 'pc';
}

// ── Detectar OS ───────────────────────────────────────────────────────────────
function det_os(string $ua): string {
    if (preg_match('/Android ([0-9.]+)/i', $ua, $m)) return 'Android ' . $m[1];
    if (preg_match('/iPhone.*OS ([0-9_]+)/i', $ua, $m)) return 'iPhone iOS ' . str_replace('_','.',$m[1]);
    if (preg_match('/iPad.*OS ([0-9_]+)/i', $ua, $m)) return 'iPad iOS ' . str_replace('_','.',$m[1]);
    if (preg_match('/Windows NT 10/i', $ua)) return 'Windows 10/11';
    if (preg_match('/Windows NT 6\.1/i', $ua)) return 'Windows 7';
    if (preg_match('/Mac OS X ([0-9_]+)/i', $ua, $m)) return 'macOS ' . str_replace('_','.',$m[1]);
    if (preg_match('/Linux/i', $ua)) return 'Linux';
    return 'Desconocido';
}

// ── Detectar Navegador ────────────────────────────────────────────────────────
function det_navegador(string $ua): string {
    if (preg_match('/Edg\/([0-9.]+)/i', $ua, $m)) return 'Edge ' . $m[1];
    if (preg_match('/OPR\/([0-9.]+)/i', $ua, $m)) return 'Opera ' . $m[1];
    if (preg_match('/Chrome\/([0-9.]+)/i', $ua, $m)) return 'Chrome ' . $m[1];
    if (preg_match('/Firefox\/([0-9.]+)/i', $ua, $m)) return 'Firefox ' . $m[1];
    if (preg_match('/Safari\/([0-9.]+)/i', $ua, $m) && !preg_match('/Chrome/i', $ua)) return 'Safari';
    if (preg_match('/MSIE ([0-9.]+)/i', $ua, $m)) return 'IE ' . $m[1];
    return 'Otro';
}

// ── Datos del POST ────────────────────────────────────────────────────────────
$pagina   = substr(trim($_POST['pagina']   ?? '/'), 0, 500);
$titulo   = substr(trim($_POST['titulo']   ?? ''), 0, 300);
$referrer = substr(trim($_POST['referrer'] ?? ''), 0, 500);
$session  = substr(preg_replace('/[^a-f0-9]/', '', trim($_POST['session'] ?? '')), 0, 64);

if (empty($session)) { echo json_encode(['ok' => false]); exit; }

// ── Guardar ───────────────────────────────────────────────────────────────────
try {
    $pdo = get_db();
    $pdo->prepare('INSERT INTO visitor_analytics
        (session_id, pagina, titulo, referrer, ip_hash, dispositivo, os, navegador, es_bot)
        VALUES (?,?,?,?,?,?,?,?,?)')
        ->execute([
            $session,
            $pagina,
            $titulo ?: null,
            $referrer ?: null,
            $ip_hash,
            det_dispositivo($ua),
            det_os($ua),
            det_navegador($ua),
            $es_bot
        ]);
    echo json_encode(['ok' => true]);
} catch (PDOException $e) {
    echo json_encode(['ok' => false]);
}
