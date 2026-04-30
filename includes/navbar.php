<?php
/**
 * Navbar principal del sitio DIF San Mateo Atenco
 *
 * Variables esperadas:
 *   $base_path   string  Prefijo de ruta relativa (ej. '../' para subdirectorios). Default: ''
 *   $active_page string  Página activa para resaltar en el menú.
 *                        Valores: 'inicio', 'acerca', 'servicios', 'comunicacion', 'transparencia', 'voluntariado'
 *                        Default: ''
 */

$base_path   = $base_path   ?? '';
$active_page = $active_page ?? '';
?>
<!-- Navbar start -->
<div class="container-fluid border-bottom bg-white wow fadeIn" data-wow-delay="0.1s">
    <div class="container px-0">
        <nav class="navbar navbar-expand-xl py-3">
            <a href="<?= htmlspecialchars($base_path) ?>index" class="navbar-brand"></a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse" style="border-color:rgba(0,0,0,0.3);">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav mx-auto">

                    <a href="<?= htmlspecialchars($base_path) ?>index"
                       class="nav-item nav-link-green<?= $active_page === 'inicio' ? ' active' : '' ?>">INICIO</a>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link-red dropdown-toggle<?= $active_page === 'acerca' ? ' active' : '' ?>"
                           data-bs-toggle="dropdown" data-bs-offset="0,0" data-bs-flip="false" data-bs-offset="0,0" data-bs-flip="false">ACERCA DEL DIF</a>
                        <li class="dropdown-menu rounded-0">
                            <a href="<?= htmlspecialchars($base_path) ?>acerca-del-dif/presidencia"
                               class="dropdown-item" style="color:#fff!important">Presidencia</a>
                            <a href="<?= htmlspecialchars($base_path) ?>acerca-del-dif/direcciones"
                               class="dropdown-item" style="color:#fff!important">Direcciones</a>
                            <a href="<?= htmlspecialchars($base_path) ?>acerca-del-dif/organigrama"
                               class="dropdown-item" style="color:#fff!important">Organigrama</a>
                        </li>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link-yellow dropdown-toggle<?= $active_page === 'servicios' ? ' active' : '' ?>"
                           data-bs-toggle="dropdown" data-bs-offset="0,0" data-bs-flip="false" data-bs-offset="0,0" data-bs-flip="false">SERVICIOS</a>
                        <div class="dropdown-menu m-0 rounded-0">
                            <?php
                            try {
                                $_nav_pdo = function_exists('get_db') ? get_db() : null;
                                if (!$_nav_pdo) {
                                    require_once (isset($base_path) && $base_path !== '' ? rtrim($base_path, '/') . '/' : '') . 'includes/db.php';
                                    $_nav_pdo = get_db();
                                }
                                $_nav_tramites = $_nav_pdo->query('SELECT slug, titulo FROM tramites ORDER BY id ASC')->fetchAll();
                                foreach ($_nav_tramites as $_nt) {
                                    $href = htmlspecialchars($base_path) . 'tramites/' . htmlspecialchars($_nt['slug']);
                                    echo '<a href="' . $href . '" class="dropdown-item" style="color:#fff!important">' . htmlspecialchars($_nt['titulo']) . '</a>';
                                }
                                // Unidad de Autismo al final de servicios
                                echo '<a href="' . htmlspecialchars($base_path) . 'autismo" class="dropdown-item" style="color:#fff!important">Unidad Municipal de Autismo</a>';
                            } catch (Exception $e) { /* silenciar */ }
                            ?>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link-pink dropdown-toggle<?= $active_page === 'comunicacion' ? ' active' : '' ?>"
                           data-bs-toggle="dropdown" data-bs-offset="0,0" data-bs-flip="false" data-bs-offset="0,0" data-bs-flip="false">COMUNICACIÓN SOCIAL</a>
                        <div class="dropdown-menu m-0 rounded-0">
                            <a href="<?= htmlspecialchars($base_path) ?>comunicacion-social/noticias"
                               class="dropdown-item" style="color:#fff!important">Noticias</a>
                            <a href="<?= htmlspecialchars($base_path) ?>comunicacion-social/galeria"
                               class="dropdown-item" style="color:#fff!important">Galerías</a>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link-purple dropdown-toggle<?= $active_page === 'transparencia' ? ' active' : '' ?>"
                           data-bs-toggle="dropdown" data-bs-offset="0,0" data-bs-flip="false" data-bs-offset="0,0" data-bs-flip="false">TRANSPARENCIA</a>
                        <div class="dropdown-menu m-0 rounded-0">
                            <a href="https://www.ipomex.org.mx/ipo3/lgt/indice/SANMATEOATENCO.web" target="_blank"
                               class="dropdown-item" style="color:#fff!important">IPOMEX 3.0</a>
                            <a href="https://infoem2.ipomex.org.mx/ipomex/#/obligaciones/167" target="_blank"
                               class="dropdown-item" style="color:#fff!important">IPOMEX 4.0</a>
                            <a href="https://www.saimex.org.mx/saimex/ciudadano/login.page" target="_blank"
                               class="dropdown-item" style="color:#fff!important">SAIMEX</a>
                            <a href="https://www.plataformadetransparencia.org.mx/" target="_blank"
                               class="dropdown-item" style="color:#fff!important">PLATAFORMA NACIONAL DE TRANSPARENCIA</a>
                            <a href="<?= htmlspecialchars($base_path) ?>transparencia/SEAC"
                               class="dropdown-item" style="color:#fff!important">SISTEMA DE EVALUACIONES DE LA ARMONIZACIÓN CONTABLE</a>
                            <a href="<?= htmlspecialchars($base_path) ?>transparencia/cuenta_publica"
                               class="dropdown-item" style="color:#fff!important">CUENTA PÚBLICA</a>
                            <a href="<?= htmlspecialchars($base_path) ?>transparencia/presupuesto_anual"
                               class="dropdown-item" style="color:#fff!important">PRESUPUESTO ANUAL</a>
                            <a href="<?= htmlspecialchars($base_path) ?>transparencia/pae"
                               class="dropdown-item" style="color:#fff!important">PAE</a>
                            <a href="<?= htmlspecialchars($base_path) ?>transparencia/matrices_indicadores"
                               class="dropdown-item" style="color:#fff!important">MATRICES DE INDICADORES</a>
                            <a href="<?= htmlspecialchars($base_path) ?>transparencia/conac"
                               class="dropdown-item" style="color:#fff!important">CONAC</a>
                            <a href="<?= htmlspecialchars($base_path) ?>transparencia/financiero"
                               class="dropdown-item" style="color:#fff!important">FINANCIERO</a>
                            <a href="<?= htmlspecialchars($base_path) ?>transparencia/avisos_privacidad"
                               class="dropdown-item" style="color:#fff!important">AVISOS DE PRIVACIDAD</a>
<?php
// Secciones dinámicas de transparencia
try {
    $__pdo = get_db();
    $__stmt = $__pdo->query('SELECT nombre, slug FROM trans_secciones WHERE activo = 1 ORDER BY orden ASC');
    while ($__sec = $__stmt->fetch()) {
        echo '<a href="' . htmlspecialchars($base_path) . 'transparencia/seccion_dinamica?slug=' . htmlspecialchars($__sec['slug']) . '" class="dropdown-item" style="color:#fff!important">' . htmlspecialchars(strtoupper($__sec['nombre'])) . '</a>';
    }
} catch (Exception $e) {}
?>
                        </div>
                    </div>

                    <a href="<?= $base_path ?>voluntariado" class="nav-item nav-link-blue<?= $active_page === 'voluntariado' ? ' active' : '' ?>">VOLUNTARIADO</a>

                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Navbar End -->
