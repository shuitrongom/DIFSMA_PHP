# Plan de Implementación: DIF CMS PHP Migration

## Visión General

Migración del sitio HTML estático del DIF San Mateo Atenco a PHP con CMS integrado. El plan sigue un orden incremental: infraestructura base → autenticación → secciones del frontend → panel admin por sección → pruebas.

**Lenguaje:** PHP 7.4+ con PDO, MySQL/MariaDB, Bootstrap 5.

---

## Tareas

- [x] 1. Infraestructura base: estructura de directorios, DB y componentes compartidos
  - Crear la estructura de directorios del proyecto según el diseño: `includes/`, `admin/`, `uploads/images/`, `uploads/pdfs/`, `logs/`, `tramites/`, `acerca-del-dif/`, `comunicacion-social/`, `transparencia/`
  - Crear `uploads/.htaccess` que deniegue ejecución de PHP y acceso directo
  - Crear `logs/` con `.gitkeep` y permisos de escritura
  - Crear `includes/db.php` con función `get_db(): PDO` (singleton, charset utf8mb4, ERRMODE_EXCEPTION, FETCH_ASSOC), leyendo credenciales de variables de entorno o archivo de configuración fuera del webroot
  - Crear `config.php` (fuera del webroot o con acceso restringido) con constantes: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `APP_DEBUG`, `UPLOAD_MAX_IMAGE_MB=5`, `UPLOAD_MAX_PDF_MB=20`
  - Crear el script SQL `database/schema.sql` con todas las tablas definidas en el diseño: `admin`, `login_attempts`, `slider_principal`, `slider_comunica`, `noticias_imagenes`, `presidencia`, `direcciones`, `organigrama`, `tramites`, `galeria_albumes`, `galeria_imagenes`, `seac_bloques`, `seac_conceptos`, `seac_pdfs`, `programas`, `programas_secciones`, `transparencia_items`, `footer_config`
  - Crear `database/seed.sql` con el INSERT del administrador inicial (hash bcrypt de contraseña de ejemplo) y datos semilla para `footer_config`, `tramites` (6 slugs), `seac_conceptos` (filas predefinidas), `presidencia` y `direcciones`
  - _Requisitos: 1.6, 1.8, 15.1, 15.4_

- [x] 2. Componentes de seguridad: CSRF, auth_guard y upload_handler
  - [x] 2.1 Crear `admin/csrf.php` con `csrf_token(): string` (genera y almacena en `$_SESSION['csrf_token']`) y `csrf_validate(string $token): bool` (valida y destruye el token)
    - _Requisitos: 15.5_

  - [x] 2.2 Escribir prueba de propiedad para CSRF (Property 14)
    - **Property 14: CSRF rechaza POST sin token válido**
    - **Valida: Requisitos 15.5**
    - Para cualquier POST sin token CSRF válido (ausente, expirado o incorrecto), el handler retorna rechazo sin escribir en DB — 100 iteraciones con Eris/QuickCheck

  - [x] 2.3 Crear `admin/auth_guard.php` que verifica `$_SESSION['admin_logged'] === true`; si no, destruye sesión y redirige a `login.php`
    - _Requisitos: 1.4_

  - [x] 2.4 Escribir prueba de propiedad para protección de rutas (Property 3)
    - **Property 3: rutas admin sin sesión redirigen a login**
    - **Valida: Requisitos 1.4**
    - Para cualquier ruta `/admin/*.php` (excepto login), sin sesión activa → Location: login.php — 100 iteraciones

  - [x] 2.5 Crear `admin/upload_handler.php` con `handle_upload(array $file, string $type = 'image'): array`
    - Validar tipo MIME real con `finfo_open(FILEINFO_MIME_TYPE)`
    - Validar extensión contra lista permitida (jpg/png/webp para imágenes, pdf para PDFs)
    - Validar tamaño contra constante de configuración
    - Renombrar con `bin2hex(random_bytes(16)) . '.' . $ext`
    - Almacenar en `uploads/images/` o `uploads/pdfs/` según `$type`
    - Retornar `['success' => bool, 'path' => string, 'error' => string]`
    - Registrar errores de escritura en `logs/upload_errors.log`
    - _Requisitos: 15.2, 15.3, 15.4_

  - [x] 2.6 Escribir prueba de propiedad para validación de archivos (Property 6)
    - **Property 6: validación de archivos — tipo y tamaño**
    - **Valida: Requisitos 2.2, 3.2, 4.2, 5.2, 6.2, 7.2, 8.2, 9.3, 11.3, 12.3, 15.2**
    - Para cualquier archivo con MIME inválido o tamaño > límite, `handle_upload()` retorna `success=false` — 100 iteraciones

  - [x] 2.7 Escribir prueba de propiedad para renombrado aleatorio (Property 7)
    - **Property 7: renombrado aleatorio de archivos subidos**
    - **Valida: Requisitos 15.3**
    - Para cualquier nombre de archivo original, el nombre almacenado en servidor es diferente — 100 iteraciones

- [x] 3. Autenticación: login, logout y rate limiting
  - [x] 3.1 Crear `admin/login.php` con formulario POST (usuario + contraseña + token CSRF), procesamiento: consultar admin por username con PDO prepare/execute, `password_verify()`, iniciar sesión con `$_SESSION['admin_logged'] = true`, redirigir a `dashboard.php`
    - En caso de fallo: INSERT en `login_attempts`, registrar en `logs/login_attempts.log` (timestamp + IP), mostrar mensaje genérico
    - _Requisitos: 1.1, 1.2, 1.3, 1.6_

  - [x] 3.2 Implementar rate limiting en `login.php`: antes de verificar credenciales, contar intentos de la IP en los últimos 15 minutos desde `login_attempts`; si ≥ 5, mostrar mensaje de bloqueo y no procesar
    - _Requisitos: 1.7_

  - [x] 3.3 Escribir prueba de propiedad para credenciales inválidas (Property 2)
    - **Property 2: credenciales inválidas nunca inician sesión**
    - **Valida: Requisitos 1.3**
    - Para cualquier par usuario/contraseña que no coincida con el admin, `auth()` retorna false — 100 iteraciones

  - [x] 3.4 Escribir prueba de propiedad para rate limiting (Property 5)
    - **Property 5: rate limiting tras 5 intentos fallidos**
    - **Valida: Requisitos 1.7**
    - Para cualquier IP, tras 5 intentos fallidos en 15 min, el 6to intento es bloqueado — 100 iteraciones

  - [x] 3.5 Crear `admin/logout.php` que destruye la sesión con `session_destroy()` y redirige a `login.php`
    - _Requisitos: 1.5_

  - [x] 3.6 Escribir prueba de propiedad para cierre de sesión (Property 4)
    - **Property 4: cierre de sesión destruye la sesión**
    - **Valida: Requisitos 1.5**
    - Para cualquier sesión autenticada activa, después del logout, acceso a rutas admin redirige a login — 100 iteraciones

- [x] 4. Checkpoint — Verificar infraestructura y autenticación
  - Asegurarse de que todas las pruebas pasan, que el login funciona correctamente y que `auth_guard.php` protege las rutas. Consultar al usuario si hay dudas.

- [x] 5. Componentes compartidos del frontend: header, navbar y footer dinámico
  - [x] 5.1 Crear `includes/header.php` con el `<head>` completo (meta, CSS de Bootstrap, Owl Carousel, Swiper, Lightbox, WOW.js, AOS, fuentes Montserrat/Fredoka, estilos personalizados) y la barra superior con logos, extraído del `index.html` original
    - _Requisitos: 15.7_

  - [x] 5.2 Crear `includes/navbar.php` con la navegación principal extraída del HTML original, con rutas relativas correctas para PHP
    - _Requisitos: 15.7_

  - [x] 5.3 Crear `includes/footer.php` que consulta `footer_config` (id=1) con PDO y renderiza el footer con los valores de la DB; si no existe registro, usa valores predeterminados del diseño original
    - _Requisitos: 14.3, 14.4, 14.5_

  - [x] 5.4 Escribir prueba de propiedad para round-trip del footer (Property 16)
    - **Property 16: round-trip del footer en todas las páginas**
    - **Valida: Requisitos 14.3**
    - Para cualquier configuración guardada en `footer_config`, todas las páginas PHP renderizan el footer con los valores actualizados — 100 iteraciones

- [x] 6. Página principal: index.php con sliders y secciones dinámicas
  - [x] 6.1 Crear `index.php` incluyendo `header.php`, `navbar.php` y `footer.php`; consultar `slider_principal` ordenado por `orden` y renderizar el carrusel Owl Carousel con las imágenes de la DB; mostrar placeholder si no hay imágenes
    - _Requisitos: 2.5, 2.6_

  - [x] 6.2 Escribir prueba de propiedad para round-trip de sliders (Property 8)
    - **Property 8: round-trip de contenido de sliders**
    - **Valida: Requisitos 2.2, 2.5, 3.2, 3.5**
    - Para cualquier N imágenes insertadas en `slider_principal`, el HTML renderizado contiene N elementos `<img>` con las rutas correctas — 100 iteraciones

  - [x] 6.3 Agregar en `index.php` la sección Slider_DIF_Comunica: consultar `slider_comunica` y renderizar con Swiper 3D
    - _Requisitos: 3.5_

  - [x] 6.4 Agregar en `index.php` la sección Slider_Noticias: consultar `noticias_imagenes` filtrando por `fecha_noticia = CURDATE()` y renderizar carrusel de tres columnas; mostrar mensaje si no hay noticias del día
    - _Requisitos: 4.5, 4.6, 4.7_

  - [x] 6.5 Escribir prueba de propiedad para filtrado de noticias por fecha (Property 10)
    - **Property 10: filtrado de noticias por fecha actual**
    - **Valida: Requisitos 4.5, 10.2**
    - Para cualquier conjunto de noticias con fechas variadas, el HTML renderizado contiene únicamente las imágenes cuya `fecha_noticia` es la fecha actual — 100 iteraciones

  - [x] 6.6 Agregar en `index.php` la sección "Todos Nuestros Programas": consultar `programas` con sus `programas_secciones` y renderizar tarjetas con dropdown y acordeón; mostrar mensaje si no hay programas
    - _Requisitos: 12.6, 12.7_

  - [x] 6.7 Agregar en `index.php` la sección Transparencia: consultar `transparencia_items` ordenados por `orden` y renderizar con el diseño visual original; mostrar mensaje si no hay entradas
    - _Requisitos: 13.5, 13.6_

- [x] 7. Páginas de comunicación social: noticias.php y galeria.php
  - [x] 7.1 Crear `comunicacion-social/noticias.php` reutilizando el mismo componente Slider_Noticias del Requisito 4 (misma consulta por fecha actual, mismo diseño de tres columnas)
    - _Requisitos: 10.1, 10.2, 10.3, 10.4_

  - [x] 7.2 Crear `comunicacion-social/galeria.php` consultando `galeria_albumes` con sus `galeria_imagenes` y renderizando tarjetas de álbumes con portada, nombre y fecha; soporte Lightbox para imágenes del álbum al hacer clic
    - _Requisitos: 9.6, 9.7_

- [x] 8. Páginas de "Acerca del DIF"
  - [x] 8.1 Crear `acerca-del-dif/presidencia.php` consultando el registro de `presidencia` y renderizando la tarjeta de equipo con imagen, nombre y cargo; fallback a `img/Presidente.png` si no hay imagen
    - _Requisitos: 5.3, 5.4_

  - [x] 8.2 Crear `acerca-del-dif/direcciones.php` consultando `direcciones` ordenadas por `orden` y renderizando tarjetas de equipo con imagen, nombre y cargo por departamento; fallback a imagen predeterminada por departamento
    - _Requisitos: 6.4, 6.5_

  - [x] 8.3 Crear `acerca-del-dif/organigrama.php` consultando `organigrama` y renderizando el PDF con `<iframe>`/`<embed>`; fallback a `img/organigrama_dif_sma.jpg` si no hay PDF
    - _Requisitos: 7.3, 7.4_

- [x] 9. Páginas de Trámites y Servicios
  - [x] 9.1 Crear `tramites/PMPNNA.php`, `tramites/DAAM.php`, `tramites/DANF.php`, `tramites/DAD.php`, `tramites/DPAF.php`, `tramites/DSJAIG.php` — cada una consulta `tramites` por su slug y renderiza imagen y contenido HTML enriquecido; fallback a imagen original si no hay imagen en DB
    - _Requisitos: 8.4, 8.5_

  - [x] 9.2 Escribir prueba de propiedad para round-trip de texto enriquecido (Property 11)
    - **Property 11: round-trip de texto enriquecido en trámites**
    - **Valida: Requisitos 8.3, 8.4**
    - Para cualquier string HTML guardado en `tramites` para un slug, al recuperarlo y renderizarlo el contenido es idéntico al guardado — 100 iteraciones

- [x] 10. Página de Transparencia: SEAC.php
  - [x] 10.1 Crear `transparencia/SEAC.php` consultando `seac_bloques` con sus `seac_pdfs` y `seac_conceptos`, y renderizando dinámicamente los `<article class="question">` con acordeón, tablas de trimestres y enlaces a PDFs; modal Bootstrap con iframe para visualización de PDF
    - _Requisitos: 11.7, 11.8_

  - [x] 10.2 Escribir prueba de propiedad para renderizado de bloques SEAC (Property 13)
    - **Property 13: renderizado correcto de bloques SEAC por año**
    - **Valida: Requisitos 11.7**
    - Para cualquier conjunto de bloques SEAC en DB, el HTML contiene un `<article class="question">` por bloque con la tabla de trimestres y enlaces correctos — 100 iteraciones

- [x] 11. Checkpoint — Verificar frontend completo
  - Asegurarse de que todas las páginas públicas renderizan correctamente con datos de la DB, los fallbacks funcionan y los estilos originales se mantienen. Consultar al usuario si hay dudas.

- [x] 12. Panel admin: dashboard y sección de autenticación
  - Crear `admin/dashboard.php` (requiere `auth_guard.php`) con menú de tarjetas Bootstrap que enlaza a cada sección admin; barra lateral colapsable con las 12 secciones gestionables
  - _Requisitos: 1.2_

- [x] 13. Panel admin: gestión de Slider Principal
  - [x] 13.1 Crear `admin/slider_principal.php` con listado de imágenes actuales (tabla con orden, vista previa, botones Editar/Eliminar), formulario de alta con campo de archivo + token CSRF, y procesamiento POST: validar CSRF → `handle_upload()` → PDO INSERT → redirect con mensaje flash
    - _Requisitos: 2.1, 2.2, 2.3, 2.4_

  - [x] 13.2 Escribir prueba unitaria para gestión de Slider Principal
    - Probar alta, edición y eliminación de imágenes; verificar que el archivo se elimina del servidor al borrar el registro
    - _Requisitos: 2.1, 2.2, 2.3, 2.4_

- [x] 14. Panel admin: gestión de Slider DIF Comunica
  - [x] 14.1 Crear `admin/slider_comunica.php` con el mismo patrón CRUD que el Slider Principal (listado, formulario, procesamiento POST con CSRF + `handle_upload()` + PDO)
    - _Requisitos: 3.1, 3.2, 3.3, 3.4_

- [x] 15. Panel admin: gestión de Noticias por Día
  - [x] 15.1 Crear `admin/noticias.php` con listado de imágenes de noticias (con fecha), formulario de alta con campo de archivo + campo de fecha + token CSRF, y procesamiento POST; edición permite modificar archivo y fecha
    - _Requisitos: 4.1, 4.2, 4.3, 4.4_

- [x] 16. Panel admin: gestión de Presidencia y Direcciones
  - [x] 16.1 Crear `admin/presidencia.php` con vista previa de imagen actual, formulario de reemplazo con `handle_upload()` + PDO UPDATE, y campos editables de nombre y cargo
    - _Requisitos: 5.1, 5.2_

  - [x] 16.2 Crear `admin/direcciones.php` con listado de departamentos, formulario de edición por departamento (imagen + nombre + cargo), procesamiento con `handle_upload()` + PDO UPDATE; al eliminar imagen, restaurar predeterminada
    - _Requisitos: 6.1, 6.2, 6.3, 6.5_

- [x] 17. Panel admin: gestión de Organigrama y Trámites
  - [x] 17.1 Crear `admin/organigrama.php` con indicación del PDF actual, formulario de subida con `handle_upload(type='pdf')` + PDO UPDATE
    - _Requisitos: 7.1, 7.2_

  - [x] 17.2 Crear `admin/tramites.php` con listado de los 6 trámites, formulario de edición por slug con campo de imagen + editor TinyMCE 6 para contenido HTML enriquecido + token CSRF, procesamiento con `handle_upload()` + PDO UPDATE
    - _Requisitos: 8.1, 8.2, 8.3_

- [x] 18. Panel admin: gestión de Galería Fotográfica
  - [x] 18.1 Crear `admin/galeria.php` con listado de álbumes (nombre, fecha, portada), formulario de creación de álbum (nombre + fecha + imagen de portada), y vista de detalle de álbum con listado de imágenes y formulario para agregar más imágenes
    - _Requisitos: 9.1, 9.2, 9.3_

  - [x] 18.2 Implementar eliminación de imagen individual (unlink + DELETE) y eliminación de álbum completo (unlink de todas las imágenes + DELETE CASCADE en DB)
    - _Requisitos: 9.4, 9.5_

  - [x] 18.3 Escribir prueba de propiedad para integridad referencial de álbumes (Property 12)
    - **Property 12: integridad referencial de álbumes de galería**
    - **Valida: Requisitos 9.5**
    - Para cualquier álbum eliminado, todas las filas de `galeria_imagenes` con ese `album_id` son eliminadas en cascada y los archivos son eliminados del servidor — 100 iteraciones

- [x] 19. Panel admin: gestión de SEAC
  - [x] 19.1 Crear `admin/seac.php` con listado de bloques por año, formulario para agregar nuevo bloque (campo año), y vista de detalle de bloque con la tabla de trimestres × conceptos con celdas para subir/reemplazar/eliminar PDFs
    - _Requisitos: 11.1, 11.2, 11.3, 11.4_

  - [x] 19.2 Implementar eliminación de PDF individual (unlink + limpiar referencia en DB) y eliminación de bloque completo (unlink de todos los PDFs + DELETE CASCADE)
    - _Requisitos: 11.5, 11.6_

- [x] 20. Panel admin: gestión de Programas y Transparencia
  - [x] 20.1 Crear `admin/programas.php` con listado de programas, formulario de alta/edición (imagen + nombre + secciones de acordeón dinámicas con título y contenido), procesamiento con `handle_upload()` + PDO; eliminación borra imagen y registros en cascada
    - _Requisitos: 12.1, 12.2, 12.3, 12.4, 12.5_

  - [x] 20.2 Crear `admin/transparencia.php` con listado de entradas, formulario de alta/edición (título + URL + imagen opcional), validación de URL, procesamiento con PDO; eliminación borra imagen si aplica
    - _Requisitos: 13.1, 13.2, 13.3, 13.4_

- [x] 21. Panel admin: gestión del Footer
  - Crear `admin/footer.php` con formulario de edición de todos los campos de `footer_config` (texto institucional, horario, dirección, teléfono, email, URLs de redes sociales), validación de URLs y longitud máxima de 500 caracteres, procesamiento con PDO UPDATE (INSERT si no existe id=1)
  - _Requisitos: 14.1, 14.2_

- [x] 22. Prueba de propiedad para eliminación completa de recursos (Property 9)
  - [x] 22.1 Escribir prueba de propiedad para eliminación completa (Property 9)
    - **Property 9: eliminación completa de recursos (archivos + DB)**
    - **Valida: Requisitos 2.4, 3.4, 4.4, 5.2, 9.4, 9.5, 11.5, 11.6, 12.5**
    - Para cualquier recurso registrado en DB, después de la operación de eliminación, el registro no existe en DB Y el archivo no existe en el sistema de archivos — 100 iteraciones

  - [x] 22.2 Escribir prueba de propiedad para inyección SQL (Property 15)
    - **Property 15: sentencias preparadas previenen inyección SQL**
    - **Valida: Requisitos 15.1**
    - Para cualquier string con caracteres SQL especiales como input, la consulta PDO se ejecuta correctamente sin alterar su estructura lógica — 100 iteraciones

- [x] 23. Checkpoint final — Verificar sistema completo
  - Asegurarse de que todas las pruebas pasan, el panel admin funciona end-to-end para todas las secciones, los archivos subidos se almacenan y eliminan correctamente, y los estilos originales se mantienen en todas las páginas. Consultar al usuario si hay dudas.

---

## Notas

- Las tareas marcadas con `*` son opcionales y pueden omitirse para un MVP más rápido
- Cada tarea referencia requisitos específicos para trazabilidad
- Los checkpoints garantizan validación incremental del sistema
- Las pruebas de propiedad usan Eris o php-quickcheck con mínimo 100 iteraciones
- Las pruebas unitarias usan PHPUnit
- El token CSRF debe incluirse en todos los formularios POST del panel admin
- Los archivos subidos nunca deben ser accesibles directamente como scripts PHP (`.htaccess` en `uploads/`)
