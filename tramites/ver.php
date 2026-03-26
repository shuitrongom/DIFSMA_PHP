<?php
/**
 * tramites/ver.php — Página genérica para trámites dinámicos
 * Carga el trámite por slug desde query param: ?slug=NOMBRE
 */
$tramite_slug  = $_GET['slug'] ?? '';
$default_image = 'img/placeholder.jpg';

if (empty($tramite_slug)) {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/_tramite_template.php';
