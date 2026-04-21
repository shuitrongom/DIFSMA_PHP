<?php
/**
 * config.php — Configuración global de la aplicación DIF CMS
 *
 * PRODUCCIÓN: mover este archivo FUERA del webroot (e.g. /etc/dif-cms/config.php)
 * y ajustar la ruta en includes/db.php.
 *
 * Las credenciales de DB se leen primero de variables de entorno;
 * si no existen, se usan los valores definidos aquí como fallback.
 */

// ── Base de datos ──────────────────────────────────────────────────────────────
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'difsanma_dif_cms');
define('DB_USER', getenv('DB_USER') ?: 'difsanma');
define('DB_PASS', getenv('DB_PASS') ?: 'JSlf45#%$$235Ads');

// ── Entorno ────────────────────────────────────────────────────────────────────
// true  → muestra errores detallados (solo en desarrollo)
// false → errores genéricos al usuario, detalles solo en logs
define('APP_DEBUG', (bool)(getenv('APP_DEBUG') ?: true));

// ── Límites de subida de archivos ──────────────────────────────────────────
define('UPLOAD_MAX_IMAGE_MB', 20);  // Máximo para imágenes (JPG, PNG, WEBP)
define('UPLOAD_MAX_PDF_MB',  50);   // Máximo para PDFs

// ── Rutas base ─────────────────────────────────────────────────────────────────
define('BASE_PATH', __DIR__);
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('LOGS_PATH',    BASE_PATH . '/logs');

// ── Correo (SMTP) ──────────────────────────────────────────────────────────────
define('MAIL_HOST',     getenv('MAIL_HOST')     ?: 'smtp.gmail.com');
define('MAIL_PORT',     getenv('MAIL_PORT')     ?: 587);
define('MAIL_USER',     getenv('MAIL_USER')     ?: 'difsmagithub@gmail.com');
define('MAIL_PASS',     getenv('MAIL_PASS')     ?: 'lqvyzsikdfecdzay');
define('MAIL_FROM',     getenv('MAIL_FROM')     ?: 'difsmagithub@gmail.com');
define('MAIL_FROM_NAME',getenv('MAIL_FROM_NAME')?: 'DIF San Mateo Atenco');
