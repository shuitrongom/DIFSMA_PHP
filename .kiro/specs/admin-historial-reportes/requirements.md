# Documento de Requisitos

## Introducción

Se requiere una sección en el panel de administración del CMS DIF San Mateo Atenco que permita generar y descargar reportes del historial de actividad del sistema. Los reportes deben estar disponibles en formato PDF y Excel, incluir el logotipo institucional del DIF, gráficas de actividad, y presentar la información de forma profesional con los campos: usuario, fecha/hora, sección, descripción del cambio e imagen de referencia cuando aplique.

El módulo se integra al historial existente (`admin/historial.php`) y a la tabla `admin_historial` de la base de datos. Los reportes se generan del lado del servidor en PHP y se descargan directamente desde el navegador.

---

## Glosario

- **Generador_Reportes**: Módulo PHP encargado de producir los archivos PDF y Excel a partir de los datos del historial.
- **Historial**: Tabla `admin_historial` que registra las acciones realizadas por los administradores en el CMS.
- **Reporte_PDF**: Documento en formato PDF generado con la librería TCPDF o FPDF, con diseño institucional del DIF.
- **Reporte_Excel**: Archivo en formato `.xlsx` generado con la librería PhpSpreadsheet.
- **Filtro_Reporte**: Conjunto de parámetros (rango de fechas, usuario, sección, acción) que delimitan los datos incluidos en el reporte.
- **Grafica**: Representación visual (barras, pastel) de las estadísticas de actividad incluida en el reporte.
- **Logo_DIF**: Imagen institucional ubicada en `img/escudo.png` y/o `img/logo_DIF.png`.
- **Admin**: Usuario autenticado con rol `admin` o `editor` en el sistema.
- **Panel_Admin**: Interfaz web del administrador del CMS DIF, accesible desde `admin/`.

---

## Requisitos

### Requisito 1: Acceso a la sección de reportes

**User Story:** Como administrador, quiero acceder a una sección dedicada de reportes desde el panel de administración, para poder generar y descargar reportes del historial sin abandonar el flujo de trabajo habitual.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar un enlace a la sección de reportes en el grupo "Sistema" del sidebar.
2. WHEN un Admin accede a `admin/reportes_historial.php`, THE Panel_Admin SHALL verificar la sesión activa mediante `auth_guard.php` antes de mostrar el contenido.
3. IF la sesión del Admin no es válida, THEN THE Panel_Admin SHALL redirigir al usuario a `admin/login.php`.
4. THE Panel_Admin SHALL mostrar la sección de reportes con la misma estructura visual (sidebar, navbar, estilos) que el resto del panel de administración.

---

### Requisito 2: Filtros para la generación de reportes

**User Story:** Como administrador, quiero aplicar filtros antes de generar un reporte, para que el documento descargado contenga únicamente la información relevante al periodo o criterio que me interesa.

#### Criterios de Aceptación

1. THE Generador_Reportes SHALL ofrecer un formulario con los siguientes filtros: rango de fechas (fecha inicio y fecha fin), usuario, sección y tipo de acción.
2. WHEN el Admin no especifica un rango de fechas, THE Generador_Reportes SHALL usar como valor predeterminado el mes en curso (primer día al día actual).
3. WHEN el Admin aplica filtros y solicita un reporte, THE Generador_Reportes SHALL consultar la tabla `admin_historial` aplicando exactamente los mismos criterios de filtrado que usa `admin/historial.php`.
4. IF no existen registros para los filtros seleccionados, THEN THE Generador_Reportes SHALL mostrar un mensaje informativo al Admin en lugar de generar un archivo vacío.
5. THE Generador_Reportes SHALL mostrar en pantalla un resumen de estadísticas (total de eventos, conteo por tipo de acción) antes de que el Admin descargue el reporte.

---

### Requisito 3: Generación y descarga de reporte en PDF

**User Story:** Como administrador, quiero descargar el historial de actividad en formato PDF con diseño profesional e institucional, para presentarlo como documento formal o conservarlo como evidencia.

#### Criterios de Aceptación

1. WHEN el Admin hace clic en "Descargar PDF", THE Generador_Reportes SHALL generar un archivo PDF y enviarlo al navegador con el encabezado `Content-Disposition: attachment`.
2. THE Reporte_PDF SHALL incluir en el encabezado de cada página: el Logo_DIF (`img/escudo.png`), el nombre "DIF San Mateo Atenco", el título "Reporte de Historial de Actividad" y el periodo del reporte.
3. THE Reporte_PDF SHALL incluir una sección de estadísticas con una Grafica de barras que muestre el conteo de eventos por tipo de acción (crear, editar, eliminar, subir, login, logout, reorden).
4. THE Reporte_PDF SHALL incluir una tabla con las columnas: Fecha/Hora, Usuario, Acción, Sección y Descripción, con todos los registros que correspondan a los filtros aplicados.
5. THE Reporte_PDF SHALL aplicar colores institucionales del DIF (rojo `#C8102E`, gris oscuro `#6B625A`) en encabezados de tabla, títulos y separadores.
6. THE Reporte_PDF SHALL incluir un pie de página en cada hoja con el número de página, el total de páginas y la fecha/hora de generación.
7. THE Reporte_PDF SHALL usar una fuente legible (mínimo 8pt para el cuerpo de tabla) y márgenes que garanticen que el contenido no quede cortado en impresión.
8. WHEN el PDF es generado exitosamente, THE Generador_Reportes SHALL nombrar el archivo con el patrón `historial_YYYY-MM-DD_YYYY-MM-DD.pdf` usando las fechas del filtro.

---

### Requisito 4: Generación y descarga de reporte en Excel

**User Story:** Como administrador, quiero descargar el historial en formato Excel, para poder analizar los datos, aplicar filtros adicionales o integrarlo con otras herramientas de oficina.

#### Criterios de Aceptación

1. WHEN el Admin hace clic en "Descargar Excel", THE Generador_Reportes SHALL generar un archivo `.xlsx` y enviarlo al navegador con el encabezado `Content-Disposition: attachment`.
2. THE Reporte_Excel SHALL incluir una hoja llamada "Historial" con las columnas: ID, Fecha, Hora, Usuario, Acción, Sección, Descripción e IP.
3. THE Reporte_Excel SHALL incluir una segunda hoja llamada "Estadísticas" con el conteo de eventos agrupados por tipo de acción y por sección.
4. THE Reporte_Excel SHALL aplicar formato visual profesional: encabezados de columna con fondo rojo `#C8102E` y texto blanco, filas alternas con fondo gris claro, y anchos de columna ajustados al contenido.
5. THE Reporte_Excel SHALL incluir en la celda A1 de la hoja "Historial" el título "DIF San Mateo Atenco — Reporte de Historial de Actividad" con el periodo del reporte.
6. WHEN el archivo Excel es generado exitosamente, THE Generador_Reportes SHALL nombrar el archivo con el patrón `historial_YYYY-MM-DD_YYYY-MM-DD.xlsx` usando las fechas del filtro.
7. THE Reporte_Excel SHALL incluir filtros automáticos de Excel (AutoFilter) en la fila de encabezados de la hoja "Historial".

---

### Requisito 5: Gráficas en el reporte

**User Story:** Como administrador, quiero que los reportes incluyan gráficas de actividad, para visualizar de forma rápida los patrones de uso del sistema.

#### Criterios de Aceptación

1. THE Reporte_PDF SHALL incluir una gráfica de barras horizontales que muestre el número de eventos por tipo de acción para el periodo filtrado.
2. THE Reporte_PDF SHALL incluir una gráfica de línea o barras que muestre la distribución de eventos por día dentro del periodo filtrado.
3. THE Reporte_Excel SHALL incluir en la hoja "Estadísticas" una tabla de datos estructurada que sirva como fuente para que el usuario pueda generar gráficas nativas de Excel.
4. WHEN el periodo filtrado contiene menos de 2 días distintos con actividad, THE Generador_Reportes SHALL omitir la gráfica de distribución por día e incluir únicamente la gráfica por tipo de acción.

---

### Requisito 6: Seguridad y control de acceso

**User Story:** Como administrador del sistema, quiero que la generación de reportes esté protegida, para evitar que usuarios no autorizados descarguen información sensible del historial.

#### Criterios de Aceptación

1. THE Generador_Reportes SHALL requerir sesión activa con rol `admin` o `editor` para acceder a la página de reportes.
2. WHEN un usuario con rol distinto a `admin` o `editor` intenta acceder a `admin/reportes_historial.php`, THE Generador_Reportes SHALL redirigir a `admin/dashboard.php` con un mensaje de acceso denegado.
3. THE Generador_Reportes SHALL validar y sanear todos los parámetros de filtro recibidos por GET antes de usarlos en consultas SQL, usando sentencias preparadas (PDO prepared statements).
4. THE Generador_Reportes SHALL registrar en el Historial cada descarga de reporte con la acción `'reporte'`, indicando el formato descargado (PDF o Excel) y el periodo del filtro.
5. IF la generación del archivo falla por error interno, THEN THE Generador_Reportes SHALL mostrar un mensaje de error al Admin sin exponer detalles técnicos del sistema.

---

### Requisito 7: Integración con el módulo de historial existente

**User Story:** Como desarrollador, quiero que el módulo de reportes reutilice la lógica existente del historial, para mantener consistencia y evitar duplicación de código.

#### Criterios de Aceptación

1. THE Generador_Reportes SHALL incluir `admin/historial_helper.php` para usar la función `registrar_historial()` al registrar las descargas.
2. THE Generador_Reportes SHALL usar la misma función `historial_badge()` de `historial_helper.php` como referencia para los colores de etiquetas en el reporte PDF.
3. THE Panel_Admin SHALL agregar el enlace "Reportes" al grupo "Sistema" del sidebar definido en `admin/sidebar_sections.php`, de forma que sea visible para usuarios con rol `admin`.
4. WHEN se genera un reporte, THE Generador_Reportes SHALL consultar únicamente la tabla `admin_historial` usando la conexión PDO provista por `includes/db.php`.
