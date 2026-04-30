# DIFSMA_PHP — CMS del DIF San Mateo Atenco

Sistema de gestión de contenido (CMS) para el sitio web del DIF Municipal de San Mateo Atenco, Estado de México. Desarrollado en PHP con MySQL y Bootstrap 5.

---

## Descripción

Plataforma web completa que permite al DIF San Mateo Atenco administrar todo el contenido de su sitio institucional desde un panel de administración. Incluye gestión de noticias, programas sociales, transparencia, galería de imágenes, trámites y servicios, entre otros módulos.

---

## Tecnologías

| Tecnología | Versión / Detalle |
|---|---|
| PHP | >= 7.4 |
| MySQL / MariaDB | 5.7+ |
| Bootstrap | 5.x |
| jQuery | 3.6.4 |
| TinyMCE | Editor WYSIWYG para contenido |
| Swiper.js | Sliders y carruseles |
| Owl Carousel 2 | Carrusel secundario |
| Lightbox | Visualización de imágenes |
| Bootstrap Icons | Iconografía |
| DomPDF | Generación de PDFs |
| PHPMailer | Envío de correos SMTP |
| PHPSpreadsheet | Exportación a Excel |

---

## Estructura del Proyecto

```
DIFSMA_PHP/
├── admin/                  # Panel de administración (módulos CRUD)
├── acerca-del-dif/         # Páginas públicas: Presidencia, Direcciones, Organigrama
├── comunicacion-social/    # Noticias y Galería pública
├── css/                    # Hojas de estilo (Bootstrap, custom, admin)
├── database/               # Scripts SQL de tablas y migraciones
├── docs/                   # Documentación técnica
├── img/                    # Imágenes estáticas del sitio
├── includes/               # Componentes compartidos (header, navbar, footer, db)
├── js/                     # JavaScript del sitio público
├── lib/                    # Librerías de terceros (jQuery, WOW, Lightbox, etc.)
├── programas/              # Páginas públicas de programas sociales
├── tramites/               # Páginas públicas de trámites y servicios
├── transparencia/          # Páginas públicas de transparencia
├── uploads/                # Archivos subidos (imágenes, PDFs)
├── logs/                   # Logs de la aplicación
├── config.php              # Configuración global (DB, correo, rutas)
├── index.php               # Página principal del sitio
├── mantenimiento.php       # Página de mantenimiento
├── track.php               # Rastreo de visitas (analytics)
├── .htaccess               # Reglas de reescritura de URLs
├── composer.json           # Dependencias PHP
└── README.md
```

---

## Módulos del Panel de Administración

### Contenido Principal
| Módulo | Archivo | Descripción |
|---|---|---|
| Dashboard | `admin/dashboard.php` | Panel principal con resumen y accesos rápidos |
| Slider Principal | `admin/slider_principal.php` | Gestión de imágenes del carrusel principal |
| Slider DIF Comunica | `admin/slider_comunica.php` | Slider de comunicación social |
| Noticias | `admin/noticias.php` | Crear, editar y eliminar noticias con imágenes |
| Galería | `admin/galeria.php` | Álbumes y fotografías |

### Acerca del DIF
| Módulo | Archivo | Descripción |
|---|---|---|
| Presidencia | `admin/presidencia.php` | Información de la presidenta del DIF |
| Direcciones | `admin/direcciones.php` | Directorio de funcionarios |
| Organigrama | `admin/organigrama.php` | Imagen del organigrama institucional |
| Imagen Institucional | `admin/institucion.php` | Banner e identidad visual |

### Servicios
| Módulo | Archivo | Descripción |
|---|---|---|
| Trámites y Servicios | `admin/tramites.php` | Gestión de trámites con galería |
| Unidad de Autismo | `admin/autismo.php` | Contenido de la unidad de autismo |

### Programas
| Módulo | Archivo | Descripción |
|---|---|---|
| Programas | `admin/programas.php` | Gestión de programas sociales |
| Editor de Programa | `admin/programa_editar.php` | Edición detallada por secciones |

### Transparencia
| Módulo | Archivo | Descripción |
|---|---|---|
| Transparencia Index | `admin/transparencia.php` | Configuración general de transparencia |
| SEAC | `admin/seac.php` | Sistema de Evaluación de Armonización Contable |
| Cuenta Pública | `admin/cuenta_publica.php` | Documentos de cuenta pública por año |
| Presupuesto Anual | `admin/presupuesto_anual.php` | Presupuestos anuales |
| PAE | `admin/pae.php` | Programa Anual de Evaluación |
| Matrices de Indicadores | `admin/matrices_indicadores.php` | Matrices MIR por año |
| CONAC | `admin/conac.php` | Documentos CONAC por trimestre |
| Financiero | `admin/financiero.php` | Información financiera |
| Avisos de Privacidad | `admin/avisos_privacidad.php` | Gestión de avisos de privacidad |
| Secciones Dinámicas | `admin/transparencia_dinamica.php` | Crear secciones de transparencia personalizadas |

### Configuración y Sistema
| Módulo | Archivo | Descripción |
|---|---|---|
| Mantenimiento | `admin/mantenimiento.php` | Activar/desactivar mantenimiento por página |
| Usuarios | `admin/usuarios.php` | Gestión de usuarios y permisos |
| Footer | `admin/footer.php` | Configuración del pie de página |
| Voluntariado | `admin/voluntariado.php` | Contenido de voluntariado |
| Documentación | `admin/documentacion.php` | Manuales y documentos técnicos |
| Reportes e Historial | `admin/reportes_historial.php` | Registro de actividad del sistema |
| Analytics | `admin/analytics.php` | Estadísticas de visitas |

---

## Componentes Compartidos (includes/)

| Archivo | Función |
|---|---|
| `includes/db.php` | Conexión PDO a la base de datos |
| `includes/header.php` | Cabecera HTML, meta tags, CSS y spinner de carga |
| `includes/navbar.php` | Barra de navegación principal con menús desplegables |
| `includes/footer.php` | Pie de página con información de contacto, redes sociales y scripts JS |
| `includes/mantenimiento_check.php` | Verificación centralizada de modo mantenimiento por página |

---

## Seguridad

- **Autenticación**: Sistema de login con sesiones PHP y protección CSRF
- **Protección de rutas**: `auth_guard.php` protege todas las páginas del admin
- **Contraseñas**: Hashing con `password_hash()` y política de expiración
- **Intentos de login**: Bloqueo tras intentos fallidos (`login_attempts`)
- **CSRF**: Tokens en todos los formularios POST
- **Subida de archivos**: Validación de tipo y tamaño (`upload_handler.php`)
- **Prepared Statements**: Consultas parametrizadas con PDO en toda la aplicación

---

## Base de Datos

La base de datos `difsanma_dif_cms` contiene más de 50 tablas. Los scripts SQL se encuentran en la carpeta `database/`. Las tablas principales incluyen:

- **Contenido**: `slider_principal`, `noticias_imagenes`, `galeria_albumes`, `galeria_imagenes`
- **Institucional**: `presidencia`, `direcciones`, `organigrama`
- **Servicios**: `tramites`, `programas`, `programas_secciones`
- **Transparencia**: `seac_bloques`, `cp_bloques`, `conac_bloques`, `trans_secciones`
- **Configuración**: `footer_config`, `footer_links`, `slider_config`, `mantenimiento_config`
- **Sistema**: `admin` (usuarios), `historial`, `visitor_analytics`, `login_attempts`

---

## Instalación

### Requisitos
- PHP >= 7.4
- MySQL 5.7+ o MariaDB
- Servidor Apache con `mod_rewrite` habilitado
- Composer (para dependencias PHP)

### Pasos

1. **Clonar el repositorio**
   ```bash
   git clone <url-del-repositorio> DIFSMA_PHP
   ```

2. **Instalar dependencias PHP**
   ```bash
   cd DIFSMA_PHP
   composer install
   ```

3. **Configurar la base de datos**
   - Crear la base de datos en MySQL
   - Importar el esquema: `database/schema.sql`
   - Importar datos iniciales: `database/seed.sql`
   - Importar tablas individuales desde `database/dif_cms_*.sql` según se necesiten

4. **Configurar credenciales**
   - Editar `config.php` con los datos de conexión a la base de datos
   - Configurar credenciales SMTP para envío de correos

5. **Permisos de carpetas**
   ```bash
   chmod -R 755 uploads/
   chmod -R 755 logs/
   ```

6. **Configurar Apache**
   - Asegurar que `mod_rewrite` esté habilitado
   - El archivo `.htaccess` ya incluye las reglas necesarias

### Despliegue en Hosting (cPanel)

1. Subir la carpeta `DIFSMA_PHP` a `public_html/`
2. Crear un `.htaccess` en `public_html/` con:
   ```apache
   RewriteEngine On
   RewriteBase /
   RewriteCond %{REQUEST_URI} !^/DIFSMA_PHP/
   RewriteRule ^(.*)$ /DIFSMA_PHP/$1 [L]
   ```
3. Importar la base de datos desde cPanel > phpMyAdmin
4. Actualizar `config.php` con las credenciales del hosting

---

## Acceso al Panel de Administración

```
https://tudominio.com/admin/login
```

---

## Características Principales

- **Modo mantenimiento centralizado**: Activar/desactivar mantenimiento por página individual con acordeones organizados por sección
- **Contenido editable**: Editor TinyMCE para contenido enriquecido
- **Sliders configurables**: Gestión de imágenes con drag & drop para reordenar
- **Galería de imágenes**: Álbumes con lightbox para visualización
- **Transparencia dinámica**: Crear secciones de transparencia personalizadas que se sincronizan automáticamente
- **Programas dinámicos**: Crear programas con secciones editables
- **Trámites y servicios**: Gestión con galería de imágenes por trámite
- **Analytics integrado**: Rastreo de visitas por página
- **Historial de actividad**: Registro de todas las acciones del admin
- **Responsive**: Diseño adaptable a dispositivos móviles
- **Exportación**: Generación de PDFs y Excel desde el admin

---

## Licencia

Proyecto desarrollado para el DIF Municipal de San Mateo Atenco. Todos los derechos reservados.
