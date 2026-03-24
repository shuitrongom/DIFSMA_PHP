<?php
/**
 * includes/footer.php
 *
 * Renderiza el footer completo del sitio DIF San Mateo Atenco.
 * Consulta `footer_config` (id=1) con PDO; si no existe registro,
 * usa los valores predeterminados del diseño original.
 * También incluye la barra de copyright, el botón back-to-top,
 * todas las librerías JavaScript y cierra </body></html>.
 *
 * Variables PHP aceptadas (deben definirse ANTES de hacer require_once):
 *   $base_path (string) — prefijo de ruta relativa, p.ej. '../' para subdirectorios.
 *                         Por defecto '' (raíz del sitio).
 *
 * Ejemplo de uso desde una página en acerca-del-dif/:
 *   <?php $base_path = '../'; require_once '../includes/footer.php'; ?>
 */

if (!isset($base_path)) $base_path = '';

// ── Valores predeterminados del diseño original ──────────────────────────────
$footer = [
    'texto_inst'    => 'Sistema Municipal DIF San Mateo Atenco, comprometido con el bienestar de las familias.',
    'horario'       => 'Horario de lunes a viernes de 8:00 a 16:00 horas',
    'direccion'     => 'Mariano Matamoros 310, Bo. La Concepción, San Mateo Atenco.',
    'telefono'      => '722 970 77 86',
    'email'         => 'presidencia@difsanmateoatenco.gob.mx',
    'url_facebook'  => 'https://facebook.com/DifSanMateoAtenco/',
    'url_twitter'   => 'https://twitter.com/DIFSMA',
    'url_instagram' => 'https://www.instagram.com/difsma',
];

// ── Consultar footer_config id=1 con PDO ─────────────────────────────────────
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare('SELECT * FROM footer_config WHERE id = 1 LIMIT 1');
    $stmt->execute();
    $row  = $stmt->fetch();

    if ($row) {
        // Sobreescribir solo los campos que no estén vacíos en la DB
        foreach ($footer as $key => $default) {
            if (!empty($row[$key])) {
                $footer[$key] = $row[$key];
            }
        }
    }
} catch (PDOException $e) {
    // En caso de error de DB, se usan los valores predeterminados
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('footer.php PDOException: ' . $e->getMessage());
    }
}

// ── Helper: escapar para HTML ─────────────────────────────────────────────────
function _fe(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Separar horario en dos líneas si contiene "de" como separador
$horario_parts = explode(' de ', $footer['horario'], 2);
$horario_linea1 = 'Horario de lunes a viernes';
$horario_linea2 = 'de 8:00 a 16:00 horas';
if (count($horario_parts) === 2) {
    // El campo puede venir como "Horario de lunes a viernes de 8:00 a 16:00 horas"
    // Intentamos dividir en la segunda ocurrencia de " de "
    $full = $footer['horario'];
    $pos  = strpos($full, ' de ', strpos($full, ' de ') + 1);
    if ($pos !== false) {
        $horario_linea1 = substr($full, 0, $pos);
        $horario_linea2 = substr($full, $pos + 1);
    } else {
        $horario_linea1 = $full;
        $horario_linea2 = '';
    }
}
?>

    <!-- Footer Start -->
    <div style="width:100%;">
        <div class="container-fluid px-0">
            <div class="row g-0 align-items-stretch">
                <!-- Columna izquierda: blanca con logo, horario, dirección, email -->
                <div class="col-12 col-md-4" style="background:#ffffff; padding: 2rem 2.5rem; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                    <div class="footer-item" style="width:100%; max-width:320px; text-align:left;">
                        <div class="mb-3">
                            <img src="<?= $base_path ?>img/logo_DIF.png" class="img-fluid" style="max-width:180px;" alt="Logo DIF San Mateo Atenco">
                        </div>
                        <div class="d-flex flex-column align-items-start">
                            <p class="text-body mb-1 small"><?= _fe($horario_linea1) ?></p>
                            <?php if ($horario_linea2): ?>
                            <p class="text-body mb-3 small"><?= _fe($horario_linea2) ?></p>
                            <?php endif; ?>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($footer['direccion']) ?>" target="_blank" rel="noopener" style="flex-shrink:0;">
                                    <img src="<?= $base_path ?>img/corazon_ubicacion.png" style="width:80px; height:80px; object-fit:contain;" alt="Ubicación">
                                </a>
                                <p class="text-body small mb-0" style="line-height:1.6;">
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($footer['direccion']) ?>" target="_blank" rel="noopener" class="text-body text-decoration-none" style="border-bottom:1px dotted #999;">
                                        <?= nl2br(_fe($footer['direccion'])) ?>
                                    </a><br>
                                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $footer['telefono']) ?>" class="text-body text-decoration-none" style="border-bottom:1px dotted #999;">
                                        Teléfono <?= _fe($footer['telefono']) ?>
                                    </a>
                                </p>
                            </div>
                            <div class="d-flex align-items-center mb-3" style="white-space:nowrap;">
                                <i class="fas fa-envelope text-black me-2" style="flex-shrink:0;"></i>
                                <a href="mailto:<?= _fe($footer['email']) ?>"
                                    class="text-body small text-decoration-none" target="_blank"><?= _fe($footer['email']) ?></a>
                            </div>
                            <div class="mt-2">
                                <img src="<?= $base_path ?>img/unidos_con_amor_floter.png" class="img-fluid" style="max-width:220px; width:100%;" alt="Unidos con Amor">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: roja con links de navegación y redes sociales -->
                <div class="col-12 col-md-8" style="background:rgb(200,16,44); padding: 2rem 2.5rem;">
                    <div class="footer-item">
                        <div class="row g-3">
                            <div class="d-flex flex-column align-items-start p-4">
                                <a class="mb-2 text-white ms-5" href="<?= $base_path ?>index.php">Inicio</a>
                                <a class="mb-2 text-white ms-5" href="<?= $base_path ?>acerca-del-dif/presidencia.php">Nosotros</a>
                                <a class="mb-2 text-white ms-5" href="<?= $base_path ?>comunicacion-social/noticias.php">Noticias</a>
                                <a class="mb-2 text-white ms-5" href="<?= $base_path ?>transparencia/SEAC.php">Transparencia</a>
                                <a class="mb-2 text-white ms-5" href="https://www.ipomex.org.mx/ipo3/lgt/indice/DIFSANMATEO.web" target="_blank">Compras y adquisiciones</a>
                                <a class="mb-2 text-white ms-5" href="https://difsanmateoatenco.gob.mx/plantilla/29" target="_blank">Declaraciones</a>
                                <a class="mb-2 text-white ms-5" href="https://www.saimex.org.mx/saimex/ciudadano/login.page" target="_blank">Sistema de Gestión de Usuarios</a>
                                <a class="mb-2 text-white ms-5" href="<?= $base_path ?>tramites/PMPNNA.php">Servicios en línea</a>
                                <a class="mb-2 text-white ms-5" href="#">Ubícanos</a>
                            </div>
                            <div class="footer-icon d-flex">
                                <a href="<?= _fe($footer['url_twitter']) ?>"
                                    class="text-start rounded-0 text-white mb-2 ms-5" target="_blank">@DIFSMA
                                    <img src="<?= $base_path ?>img/icon_x.png" alt="X" style="width:32px;height:32px;border-radius:50%;object-fit:cover;margin-right:8px;vertical-align:middle;">
                                </a>
                                <a href="<?= _fe($footer['url_facebook']) ?>"
                                    class="text-start rounded-0 text-white mb-2 ms-5" target="_blank">@DIF San Mateo Atenco
                                    <img src="<?= $base_path ?>img/icon_facebook.png" alt="Facebook" style="width:32px;height:32px;border-radius:50%;object-fit:cover;margin-right:8px;vertical-align:middle;">
                                </a>
                                <a href="<?= _fe($footer['url_instagram']) ?>"
                                    class="text-start rounded-0 text-white mb-2 ms-5" target="_blank">@difsma_
                                    <img src="<?= $base_path ?>img/icon_instagram.png" alt="Instagram" style="width:32px;height:32px;border-radius:50%;object-fit:cover;margin-right:8px;vertical-align:middle;">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="copyright-bar">
        <img src="<?= $base_path ?>img/logo_administracion.png" width="200" class="img-fluid d-block mx-auto mb-2 rounded shadow" alt="Administración">
        <p class="mb-0" style="font-size:12px;">© <?= date('Y') ?> DIF San Mateo Atenco. Todos los derechos reservados.</p>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn-up btn-dark border-3 border-end-0 rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base_path ?>lib/wow/wow.min.js"></script>
    <script src="<?= $base_path ?>lib/easing/easing.min.js"></script>
    <script src="<?= $base_path ?>lib/waypoints/waypoints.min.js"></script>
    <script src="<?= $base_path ?>lib/lightbox/js/lightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="<?= $base_path ?>js/main.js?v=2"></script>
    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <!-- Widget Clima -->
    <script>
        (function () {
            var lat = 19.2667, lon = -99.5333;
            var icons = {
                0:'☀️', 1:'🌤️', 2:'⛅', 3:'☁️',
                45:'🌫️', 48:'🌫️',
                51:'🌦️', 53:'🌦️', 55:'🌧️',
                61:'🌧️', 63:'🌧️', 65:'🌧️',
                71:'🌨️', 73:'🌨️', 75:'❄️',
                80:'🌦️', 81:'🌧️', 82:'⛈️',
                95:'⛈️', 96:'⛈️', 99:'⛈️'
            };
            var descs = {
                0:'Despejado', 1:'Mayormente despejado', 2:'Parcialmente nublado', 3:'Nublado',
                45:'Neblina', 48:'Neblina',
                51:'Llovizna', 53:'Llovizna', 55:'Llovizna intensa',
                61:'Lluvia ligera', 63:'Lluvia', 65:'Lluvia intensa',
                71:'Nieve ligera', 73:'Nieve', 75:'Nieve intensa',
                80:'Chubascos', 81:'Chubascos', 82:'Chubascos fuertes',
                95:'Tormenta', 96:'Tormenta', 99:'Tormenta severa'
            };
            fetch('https://api.open-meteo.com/v1/forecast?latitude=' + lat + '&longitude=' + lon + '&current_weather=true')
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    var cw = data.current_weather;
                    var code = cw.weathercode;
                    document.getElementById('weather-temp').textContent = Math.round(cw.temperature) + '°C';
                    document.getElementById('weather-icon').textContent = icons[code] || '🌡️';
                    document.getElementById('weather-desc').textContent = descs[code] || '';
                })
                .catch(function () {
                    document.getElementById('weather-icon').textContent = '🌡️';
                    document.getElementById('weather-desc').textContent = '';
                });
        })();
    </script>

    <!-- Evitar que el dropdown se cierre al hacer clic dentro -->
    <script>
        document.querySelectorAll('.dropdown-menu').forEach(function (element) {
            element.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        });
    </script>

    <!-- Buscador en página -->
    <script>
        (function () {
            var matches = [];
            var matchIndex = 0;
            var lastQuery = '';
            var HIGHLIGHT_CLASS = 'search-highlight';
            var ACTIVE_CLASS = 'search-highlight-active';

            var style = document.createElement('style');
            style.textContent = '.' + HIGHLIGHT_CLASS + '{background:#ffe066;color:#000;border-radius:2px;padding:0 1px;}.' + ACTIVE_CLASS + '{background:#ff9800;color:#000;outline:2px solid #e65100;}';
            document.head.appendChild(style);

            function clearHighlights() {
                document.querySelectorAll('.' + HIGHLIGHT_CLASS).forEach(function (el) {
                    var parent = el.parentNode;
                    parent.replaceChild(document.createTextNode(el.textContent), el);
                    parent.normalize();
                });
                matches = [];
                matchIndex = 0;
            }

            function highlightText(node, query) {
                if (node.nodeType === 3) {
                    var idx = node.nodeValue.toLowerCase().indexOf(query.toLowerCase());
                    if (idx === -1) return;
                    var before = document.createTextNode(node.nodeValue.slice(0, idx));
                    var mark = document.createElement('mark');
                    mark.className = HIGHLIGHT_CLASS;
                    mark.textContent = node.nodeValue.slice(idx, idx + query.length);
                    var after = document.createTextNode(node.nodeValue.slice(idx + query.length));
                    var parent = node.parentNode;
                    parent.insertBefore(before, node);
                    parent.insertBefore(mark, node);
                    parent.insertBefore(after, node);
                    parent.removeChild(node);
                    matches.push(mark);
                    highlightText(after, query);
                } else if (node.nodeType === 1 && !['SCRIPT','STYLE','NOSCRIPT','INPUT','TEXTAREA'].includes(node.tagName)) {
                    Array.from(node.childNodes).forEach(function (child) { highlightText(child, query); });
                }
            }

            function scrollToMatch(i) {
                document.querySelectorAll('.' + ACTIVE_CLASS).forEach(function (el) { el.classList.remove(ACTIVE_CLASS); });
                if (matches.length === 0) return;
                matchIndex = ((i % matches.length) + matches.length) % matches.length;
                matches[matchIndex].classList.add(ACTIVE_CLASS);
                matches[matchIndex].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            window.doSearch = function (e) {
                e.preventDefault();
                var q = document.getElementById('siteSearchInput').value.trim();
                if (!q) { clearHighlights(); return; }
                if (q !== lastQuery) {
                    clearHighlights();
                    lastQuery = q;
                    highlightText(document.body, q);
                    if (matches.length === 0) { alert('No se encontraron resultados para: "' + q + '"'); return; }
                    scrollToMatch(0);
                } else {
                    scrollToMatch(matchIndex + 1);
                }
            };

            document.getElementById('siteSearchInput').addEventListener('input', function () {
                if (!this.value) clearHighlights();
            });
        })();
    </script>

</body>
</html>
