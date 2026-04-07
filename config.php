<?php
/**
 * config.php — Configuración global de la aplicación DIF CMS
 */

// ── Mostrar errores temporalmente para diagnóstico ─────────────────────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ── Base de datos ──────────────────────────────────────────────────────────────
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'difsanma_dif_cms');
define('DB_USER', getenv('DB_USER') ?: 'difsanma');
define('DB_PASS', getenv('DB_PASS') ?: 'JSlf45#%$$235Ads');

// ── Entorno ────────────────────────────────────────────────────────────────────
// true  → muestra errores detallados (solo en desarrollo)
// false → errores genéricos al usuario, detalles solo en logs
define('APP_DEBUG', (bool)(getenv('APP_DEBUG') ?: false));

// ── Límites de subida de archivos ──────────────────────────────────────────
define('UPLOAD_MAX_IMAGE_MB', 20);  // Máximo para imágenes (JPG, PNG, WEBP)
define('UPLOAD_MAX_PDF_MB',  50);   // Máximo para PDFs

// ── Rutas base ─────────────────────────────────────────────────────────────────
define('BASE_PATH', __DIR__);
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('LOGS_PATH',    BASE_PATH . '/logs');
