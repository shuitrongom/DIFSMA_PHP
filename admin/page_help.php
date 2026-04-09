<?php
/**
 * admin/page_help.php — Banners de ayuda contextual para cada sección del admin
 * Uso: page_help('slider_principal');
 */

function page_help(string $seccion): void {
    $helps = [
        'slider_principal' => [
            'icon'  => 'bi-images',
            'title' => 'Slider Principal',
            'desc'  => 'Administra las imágenes del carrusel principal de la página de inicio. Puedes subir nuevas imágenes (JPG, PNG o WEBP, máx. 20 MB) y configurar opcionalmente una redirección: al hacer clic en la imagen en el sitio, el visitante será llevado a la sección que elijas. Al editar, puedes cambiar solo la imagen, solo la redirección, o ambas a la vez — si no seleccionas una nueva imagen, la actual se conserva. Arrastra las tarjetas para reordenar las imágenes. Se recomienda usar imágenes horizontales de al menos 1200×500 px.',
        ],
        'slider_comunica' => [
            'icon'  => 'bi-megaphone',
            'title' => 'Slider DIF Comunica',
            'desc'  => 'Administra las imágenes del carrusel de la sección "DIF Comunica". Puedes agregar, reemplazar y reordenar las imágenes. Usa imágenes de buena resolución para mantener la calidad visual del sitio.',
        ],
        'programas' => [
            'icon'  => 'bi-grid-3x3-gap',
            'title' => 'Nuestros Programas',
            'desc'  => 'Gestiona los programas del DIF que se muestran en la página de inicio. Puedes crear nuevos programas con imagen y secciones de acordeón, editar los existentes y cambiar su orden de aparición.',
        ],
        'institucion' => [
            'icon'  => 'bi-card-image',
            'title' => 'Imagen Institucional',
            'desc'  => 'Actualiza la imagen institucional que aparece en la sección principal del sitio. Sube una imagen representativa del DIF San Mateo Atenco en formato JPG, PNG o WEBP.',
        ],
        'transparencia' => [
            'icon'  => 'bi-shield-check',
            'title' => 'Transparencia — Página de Inicio',
            'desc'  => 'Configura los botones y enlaces de transparencia que aparecen en la página principal. Puedes agregar, editar y reordenar los accesos directos a las secciones de transparencia.',
        ],
        'footer' => [
            'icon'  => 'bi-layout-text-window-reverse',
            'title' => 'Configuración del Footer',
            'desc'  => 'Edita la información que aparece en el pie de página del sitio: texto institucional, horario, dirección, teléfono, correo y redes sociales. También puedes gestionar los enlaces de navegación del footer.',
        ],
        'presidencia' => [
            'icon'  => 'bi-person-badge',
            'title' => 'Presidencia',
            'desc'  => 'Actualiza la información y fotografía del presidente del DIF. Puedes editar el nombre, cargo, mensaje y subir una nueva imagen de perfil.',
        ],
        'direcciones' => [
            'icon'  => 'bi-people',
            'title' => 'Direcciones y Departamentos',
            'desc'  => 'Administra los departamentos y direcciones del DIF. Puedes agregar nuevos departamentos, editar la información existente (nombre, cargo, imagen) y cambiar el orden de aparición.',
        ],
        'organigrama' => [
            'icon'  => 'bi-diagram-3',
            'title' => 'Organigrama',
            'desc'  => 'Actualiza la imagen del organigrama institucional del DIF. Sube una imagen clara y legible en formato JPG, PNG o WEBP.',
        ],
        'autismo' => [
            'icon'  => 'bi-heart-pulse',
            'title' => 'Unidad Municipal de Autismo',
            'desc'  => 'Administra el contenido de la página de la Unidad Municipal de Autismo. Puedes actualizar el logo/imagen principal, los textos descriptivos y las imágenes de la sección central e inferior.',
        ],
        'tramites' => [
            'icon'  => 'bi-file-earmark-text',
            'title' => 'Trámites y Servicios',
            'desc'  => 'Gestiona los trámites y servicios que ofrece el DIF. Puedes agregar nuevos trámites con título y descripción, editarlos y eliminarlos.',
        ],
        'noticias' => [
            'icon'  => 'bi-newspaper',
            'title' => 'Noticias por Día',
            'desc'  => 'Administra las imágenes de noticias organizadas por fecha. Sube imágenes de eventos o actividades del DIF indicando la fecha correspondiente. Las imágenes se muestran agrupadas por día en el sitio.',
        ],
        'galeria' => [
            'icon'  => 'bi-camera',
            'title' => 'Galería Fotográfica',
            'desc'  => 'Organiza las fotografías del DIF en álbumes. Crea álbumes con nombre, fecha y portada, luego agrega las imágenes correspondientes. Puedes reordenar las fotos dentro de cada álbum arrastrándolas.',
        ],
        'voluntariado' => [
            'icon'  => 'bi-heart',
            'title' => 'Voluntariado',
            'desc'  => 'Actualiza el contenido de la página de Voluntariado: lema, logo, misión, visión y valores. También puedes gestionar la galería de imágenes que aparece en esa sección.',
        ],
        'seac' => [
            'icon'  => 'bi-file-earmark-pdf',
            'title' => 'SEAC',
            'desc'  => 'Administra los documentos PDF del Sistema de Evaluación y Acreditación de la Calidad (SEAC). Organiza los archivos por bloques, conceptos y trimestres. Sube los PDFs correspondientes a cada período.',
        ],
        'cuenta_publica' => [
            'icon'  => 'bi-cash-stack',
            'title' => 'Cuenta Pública',
            'desc'  => 'Gestiona los documentos de Cuenta Pública organizados por año y concepto. Sube los PDFs de cada período y mantenlos actualizados para cumplir con las obligaciones de transparencia.',
        ],
        'presupuesto_anual' => [
            'icon'  => 'bi-wallet2',
            'title' => 'Presupuesto Anual',
            'desc'  => 'Administra los documentos del Presupuesto Anual del DIF. Organiza los archivos por año y concepto. Asegúrate de subir los documentos oficiales en formato PDF.',
        ],
        'pae' => [
            'icon'  => 'bi-clipboard-data',
            'title' => 'PAE — Programa Anual de Evaluación',
            'desc'  => 'Gestiona los documentos del Programa Anual de Evaluación. Sube los PDFs organizados por título y año para mantener actualizada la información de transparencia.',
        ],
        'matrices_indicadores' => [
            'icon'  => 'bi-bar-chart-line',
            'title' => 'Matrices de Indicadores',
            'desc'  => 'Administra las Matrices de Indicadores de Resultados (MIR). Organiza los documentos por año y sube los PDFs correspondientes a cada período de evaluación.',
        ],
        'conac' => [
            'icon'  => 'bi-bank',
            'title' => 'CONAC',
            'desc'  => 'Gestiona los documentos del Consejo Nacional de Armonización Contable (CONAC). Organiza los archivos por bloques, conceptos y trimestres. Mantén actualizados todos los reportes contables.',
        ],
        'financiero' => [
            'icon'  => 'bi-currency-dollar',
            'title' => 'Información Financiera',
            'desc'  => 'Administra los documentos de información financiera del DIF. Organiza los PDFs por año y concepto para cumplir con las obligaciones de transparencia financiera.',
        ],
        'avisos_privacidad' => [
            'icon'  => 'bi-shield-exclamation',
            'title' => 'Avisos de Privacidad',
            'desc'  => 'Gestiona los avisos de privacidad del DIF. Puedes editar el texto del aviso, agregar botones con PDFs descargables y mantener actualizada la información de protección de datos.',
        ],
        'transparencia_dinamica' => [
            'icon'  => 'bi-plus-square',
            'title' => 'Secciones Dinámicas de Transparencia',
            'desc'  => 'Crea y administra secciones personalizadas de transparencia. Puedes agregar nuevas secciones con diferentes plantillas (SEAC, CONAC, Financiero, etc.) y gestionar su contenido de forma independiente.',
        ],
        'historial' => [
            'icon'  => 'bi-clock-history',
            'title' => 'Historial de Actividad',
            'desc'  => 'Consulta el registro de todas las acciones realizadas en el panel de administración. Puedes filtrar por fecha, usuario, sección y tipo de acción para auditar los cambios realizados en el sistema.',
        ],
        'reportes_historial' => [
            'icon'  => 'bi-file-earmark-bar-graph',
            'title' => 'Reportes de Historial',
            'desc'  => 'Genera y descarga reportes del historial de actividad en formato PDF o Excel. Aplica filtros por fecha, usuario, sección y acción para obtener el reporte que necesitas.',
        ],
        'usuarios' => [
            'icon'  => 'bi-people',
            'title' => 'Gestión de Usuarios',
            'desc'  => 'Administra los usuarios del panel de administración. Puedes crear nuevos usuarios con acceso limitado a secciones específicas, cambiar contraseñas, activar/desactivar cuentas y gestionar permisos.',
        ],
    ];

    $h = $helps[$seccion] ?? null;
    if (!$h) return;
    ?>
    <div class="admin-page-help mb-4" role="note">
        <div class="d-flex align-items-start gap-3">
            <div class="admin-help-icon flex-shrink-0">
                <i class="bi <?= htmlspecialchars($h['icon']) ?>"></i>
            </div>
            <div>
                <div class="admin-help-title"><?= htmlspecialchars($h['title']) ?></div>
                <div class="admin-help-desc"><?= htmlspecialchars($h['desc']) ?></div>
            </div>
        </div>
    </div>
    <?php
}
