<?php
/**
 * mantenimiento.php — Página de mantenimiento del sitio DIF San Mateo Atenco
 */
require_once __DIR__ . '/includes/db.php';

$base_path   = '';
$active_page = '';
$page_title  = 'En Mantenimiento — DIF San Mateo Atenco';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Mantenimiento Start -->
<div class="container-fluid py-5" style="min-height:70vh;display:flex;align-items:center;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center wow fadeIn" data-wow-delay="0.1s">

                <!-- Ícono animado -->
                <div class="mb-4">
                    <div style="
                        display:inline-flex;
                        align-items:center;
                        justify-content:center;
                        width:120px;height:120px;
                        border-radius:50%;
                        background:linear-gradient(135deg,rgb(200,16,44) 0%,rgb(160,10,35) 100%);
                        box-shadow:0 8px 32px rgba(200,16,44,0.35);
                        animation:pulse 2s infinite;
                    ">
                        <i class="fas fa-tools" style="font-size:3rem;color:#fff;"></i>
                    </div>
                </div>

                <!-- Título -->
                <h1 style="
                    font-family:'Montserrat',sans-serif;
                    font-weight:800;
                    font-size:2.8rem;
                    color:rgb(107,98,90);
                    margin-bottom:0.5rem;
                ">Sitio en Mantenimiento</h1>

                <!-- Línea decorativa -->
                <div style="
                    height:4px;
                    width:80px;
                    background:rgb(200,16,44);
                    border-radius:2px;
                    margin:1rem auto 1.5rem;
                "></div>

                <!-- Descripción -->
                <p style="
                    font-family:'Montserrat',sans-serif;
                    font-size:1.1rem;
                    color:rgb(107,98,90);
                    line-height:1.8;
                    max-width:600px;
                    margin:0 auto 2rem;
                ">
                    Estamos realizando mejoras en nuestro sitio web para ofrecerte una mejor experiencia.
                    Regresaremos en breve con contenido actualizado.
                </p>

                <!-- Tarjeta informativa -->
                <div class="row g-3 justify-content-center mb-4">
                    <div class="col-sm-4">
                        <div style="
                            background:#fff;
                            border-radius:12px;
                            padding:1.5rem 1rem;
                            box-shadow:0 4px 20px rgba(0,0,0,0.08);
                            border-top:4px solid rgb(200,16,44);
                        ">
                            <i class="fas fa-clock" style="font-size:1.8rem;color:rgb(200,16,44);margin-bottom:.5rem;"></i>
                            <p style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);margin:0;font-size:.9rem;">Tiempo estimado</p>
                            <p style="font-family:'Montserrat',sans-serif;color:#999;margin:0;font-size:.82rem;">Breve interrupción</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div style="
                            background:#fff;
                            border-radius:12px;
                            padding:1.5rem 1rem;
                            box-shadow:0 4px 20px rgba(0,0,0,0.08);
                            border-top:4px solid rgb(200,16,44);
                        ">
                            <i class="fas fa-shield-alt" style="font-size:1.8rem;color:rgb(200,16,44);margin-bottom:.5rem;"></i>
                            <p style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);margin:0;font-size:.9rem;">Mejoras de seguridad</p>
                            <p style="font-family:'Montserrat',sans-serif;color:#999;margin:0;font-size:.82rem;">Actualizaciones del sistema</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div style="
                            background:#fff;
                            border-radius:12px;
                            padding:1.5rem 1rem;
                            box-shadow:0 4px 20px rgba(0,0,0,0.08);
                            border-top:4px solid rgb(200,16,44);
                        ">
                            <i class="fas fa-rocket" style="font-size:1.8rem;color:rgb(200,16,44);margin-bottom:.5rem;"></i>
                            <p style="font-family:'Montserrat',sans-serif;font-weight:700;color:rgb(107,98,90);margin:0;font-size:.9rem;">Nuevas funciones</p>
                            <p style="font-family:'Montserrat',sans-serif;color:#999;margin:0;font-size:.82rem;">Próximamente disponibles</p>
                        </div>
                    </div>
                </div>

                <!-- Contacto -->
                <div style="
                    background:#f8f7f6;
                    border-radius:12px;
                    padding:1.2rem 2rem;
                    display:inline-block;
                    margin-bottom:2rem;
                ">
                    <p style="font-family:'Montserrat',sans-serif;font-size:.88rem;color:rgb(107,98,90);margin:0;">
                        <i class="fas fa-envelope me-2" style="color:rgb(200,16,44);"></i>
                        ¿Necesitas ayuda? Contáctanos:
                        <strong>presidencia@difsanmateoatenco.gob.mx</strong>
                    </p>
                </div>

                <!-- Botón volver -->
                <div>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary me-2" style="
                        font-family:'Montserrat',sans-serif;
                        font-weight:600;
                        border-radius:8px;
                        padding:.6rem 1.5rem;
                    ">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <a href="index" class="btn" style="
                        background:rgb(200,16,44);
                        color:#fff;
                        font-family:'Montserrat',sans-serif;
                        font-weight:600;
                        border-radius:8px;
                        padding:.6rem 1.5rem;
                    ">
                        <i class="fas fa-home me-2"></i>Ir al Inicio
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Mantenimiento End -->

<style>
@keyframes pulse {
    0%   { box-shadow: 0 8px 32px rgba(200,16,44,0.35); }
    50%  { box-shadow: 0 8px 48px rgba(200,16,44,0.6); }
    100% { box-shadow: 0 8px 32px rgba(200,16,44,0.35); }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
