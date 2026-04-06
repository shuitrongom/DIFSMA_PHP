# Plan de Implementación: admin-historial-reportes

## Visión General

Implementar el módulo de generación de reportes PDF y Excel del historial de actividad del panel admin del CMS DIF San Mateo Atenco. El módulo consiste en un único archivo nuevo (`admin/reportes_historial.php`), la actualización de dependencias Composer, la modificación del sidebar, y las pruebas de propiedades con PHPUnit.

## Tareas

- [x] 1. Actualizar dependencias Composer
  - Agregar `"dompdf/dompdf": "^2.0"` y `"phpoffice/phpspreadsheet": "^2.0"` al bloque `require` de `composer.json`
  - Ejecutar `composer update` (o `php composer.phar update`) para instalar las librerías
  - Verificar que `vendor/autoload.php` cargue ambas librerías sin errores
  - _Requisitos: 3.1, 4.1_

- [x] 2. Agregar enlace "Reportes" al sidebar
  - En `admin/sidebar_sections.php`, agregar al grupo `'Sistema'` el ítem: `['title' => 'Reportes', 'file' => 'reportes_historial.php', 'icon' => 'bi-file-earmark-bar-graph']`
  - _Requisitos: 1.1, 7.3_

- [x] 3. Implementar funciones puras del módulo
  - [x] 3.1 Implementar `build_filter_query(array $get): array`
    - Construir el WHERE clause y array de parámetros a partir de los filtros GET
    - Replicar exactamente la lógica de filtrado de `admin/historial.php` (fecha_ini, fecha_fin, usuario LIKE, seccion LIKE, accion exact)
    - Retornar `['where' => string, 'params' => array]`
    - Usar únicamente parámetros preparados, nunca interpolación directa
    - _Requisitos: 2.2, 2.3, 6.3_

  - [x] 3.2 Escribir prueba de propiedad para `build_filter_query` — equivalencia de filtros
    - **Propiedad 1: Equivalencia de filtros entre historial.php y reportes_historial.php**
    - **Valida: Requisito 2.3**
    - Clase `FilterQueryTest`, método `testFilterEquivalenceWithHistorial`
    - Generar 100 combinaciones aleatorias de filtros y verificar que el WHERE y params producidos son idénticos a los de `historial.php`

  - [x] 3.3 Escribir prueba de propiedad para `build_filter_query` — parámetros preparados
    - **Propiedad 10: El constructor de consultas siempre usa parámetros preparados**
    - **Valida: Requisito 6.3**
    - Clase `FilterQueryTest`, método `testQueryBuilderAlwaysUsesParameters`
    - Generar 100 valores con caracteres especiales SQL (`'`, `"`, `;`, `--`) y verificar que nunca se interpolan en el WHERE string

  - [x] 3.4 Implementar `report_filename(string $fecha_ini, string $fecha_fin, string $ext): string`
    - Retornar `"historial_{$fecha_ini}_{$fecha_fin}.{$ext}"`
    - _Requisitos: 3.8, 4.6_

  - [x] 3.5 Escribir prueba de propiedad para `report_filename`
    - **Propiedad 5: El nombre de archivo sigue el patrón requerido para cualquier rango de fechas**
    - **Valida: Requisitos 3.8, 4.6**
    - Clase `FilenameTest`, método `testFilenameMatchesPattern`
    - Generar 100 pares de fechas aleatorias y verificar que el resultado coincide con el patrón `historial_{fecha_ini}_{fecha_fin}.{ext}`

- [x] 4. Implementar `build_pdf_html()`
  - [x] 4.1 Implementar `build_pdf_html(array $registros, array $stats, array $stats_dia, array $filtros): string`
    - Generar HTML completo para dompdf con: encabezado institucional (escudo.png, "DIF San Mateo Atenco", título, periodo), sección de estadísticas con gráfica de barras CSS por tipo de acción, tabla de registros con columnas Fecha/Hora / Usuario / Acción / Sección / Descripción, pie de página con número de página y fecha de generación
    - Aplicar colores institucionales: rojo `#C8102E` en encabezados, gris `#6B625A` en pie de página
    - Omitir la gráfica de distribución por día si hay menos de 2 días distintos con actividad
    - _Requisitos: 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 5.1, 5.4_

  - [x] 4.2 Escribir prueba de propiedad — encabezado institucional PDF
    - **Propiedad 2: El PDF contiene encabezado institucional para cualquier dataset**
    - **Valida: Requisito 3.2**
    - Clase `PdfBuilderTest`, método `testPdfContainsInstitutionalHeader`
    - 100 iteraciones con datasets aleatorios; verificar presencia de "DIF San Mateo Atenco", título y fechas del periodo

  - [x] 4.3 Escribir prueba de propiedad — gráfica PDF refleja todas las acciones
    - **Propiedad 3: La gráfica PDF refleja todos los tipos de acción presentes**
    - **Valida: Requisitos 3.3, 5.1**
    - Clase `PdfBuilderTest`, método `testPdfChartContainsAllActionTypes`
    - 100 iteraciones con arrays de stats aleatorios; verificar que hay un elemento de barra por cada tipo de acción presente

  - [x] 4.4 Escribir prueba de propiedad — tabla PDF contiene todos los registros
    - **Propiedad 4: La tabla PDF contiene todos los registros con todas las columnas requeridas**
    - **Valida: Requisito 3.4**
    - Clase `PdfBuilderTest`, método `testPdfTableContainsAllRecords`
    - 100 iteraciones; verificar exactamente una fila por registro con los cinco campos requeridos

  - [x] 4.5 Escribir prueba de propiedad — omisión de gráfica por día
    - **Propiedad 11: La gráfica de distribución por día se omite cuando hay menos de 2 días activos**
    - **Valida: Requisito 5.4**
    - Clase `PdfBuilderTest`, método `testDailyChartOmittedWhenLessThanTwoDays`
    - Verificar que con 0 o 1 días distintos el HTML no contiene la sección de gráfica por día

- [x] 5. Implementar `build_excel()`
  - [x] 5.1 Implementar `build_excel(array $registros, array $stats, array $stats_seccion, array $filtros): \PhpOffice\PhpSpreadsheet\Spreadsheet`
    - Crear hoja "Historial": celda A1 con título y periodo (merged, negrita, rojo), fila 2 con encabezados (fondo `#C8102E`, texto blanco), filas 3+ con datos (ID, Fecha, Hora, Usuario, Acción, Sección, Descripción, IP), filas alternas con fondo `#F2F2F2`, AutoFilter en fila 2
    - Crear hoja "Estadísticas": sección A con conteo por tipo de acción, sección D con conteo por sección
    - _Requisitos: 4.2, 4.3, 4.4, 4.5, 4.7, 5.3_

  - [x] 5.2 Escribir prueba de propiedad — hoja Historial contiene todos los registros
    - **Propiedad 6: La hoja "Historial" del Excel contiene todos los registros con todas las columnas**
    - **Valida: Requisito 4.2**
    - Clase `ExcelBuilderTest`, método `testHistorialSheetContainsAllRecords`
    - 100 iteraciones; verificar una fila de datos por registro con los ocho campos requeridos a partir de fila 3

  - [x] 5.3 Escribir prueba de propiedad — hoja Estadísticas agrega correctamente
    - **Propiedad 7: La hoja "Estadísticas" agrega correctamente los conteos**
    - **Valida: Requisitos 4.3, 5.3**
    - Clase `ExcelBuilderTest`, método `testStatsSheetCountsAreConsistent`
    - 100 iteraciones; verificar que suma de conteos por acción = total registros, y suma por sección = total registros

  - [x] 5.4 Escribir prueba de propiedad — celda A1 contiene título y periodo
    - **Propiedad 8: La celda A1 del Excel contiene el título con el periodo para cualquier rango de fechas**
    - **Valida: Requisito 4.5**
    - Clase `ExcelBuilderTest`, método `testCellA1ContainsTitleAndPeriod`
    - 100 iteraciones con pares de fechas aleatorias; verificar que A1 contiene "DIF San Mateo Atenco" y las fechas del periodo

- [x] 6. Checkpoint — Verificar que todas las pruebas de funciones puras pasan
  - Ejecutar `./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/` y confirmar que todas las pruebas de las clases `FilterQueryTest`, `FilenameTest`, `PdfBuilderTest` y `ExcelBuilderTest` pasan sin errores.
  - Consultar al usuario si hay dudas antes de continuar.

- [x] 7. Implementar `admin/reportes_historial.php` — modo HTML (vista principal)
  - Crear el archivo con: `require` de `auth_guard.php`, `csrf.php`, `includes/db.php`, `historial_helper.php`, `sidebar_sections.php` y `vendor/autoload.php`
  - Verificar rol (`admin` o `editor`); redirigir a `dashboard.php?error=acceso_denegado` si no autorizado
  - Leer y sanear parámetros GET con fallback a mes actual para fechas
  - Llamar a `build_filter_query()` y ejecutar las consultas: registros, stats por acción, stats por día, stats por sección
  - Mostrar formulario de filtros (fecha_ini, fecha_fin, usuario, sección, acción) con los mismos controles que `historial.php`
  - Mostrar tarjetas de estadísticas (total eventos, conteo por tipo de acción)
  - Mostrar botones "Descargar PDF" y "Descargar Excel" (GET con `?action=pdf` y `?action=excel` más los filtros actuales)
  - Mostrar mensaje informativo si no hay registros para los filtros seleccionados
  - Usar la misma estructura visual del panel (sidebar, navbar, estilos de `admin.css`)
  - _Requisitos: 1.2, 1.3, 1.4, 2.1, 2.2, 2.4, 2.5, 6.1, 6.2_

- [x] 8. Implementar `admin/reportes_historial.php` — modo descarga PDF
  - Dentro del mismo archivo, manejar `$_GET['action'] === 'pdf'`
  - Llamar a `build_filter_query()`, ejecutar consultas, llamar a `build_pdf_html()`
  - Instanciar dompdf, cargar el HTML, renderizar
  - Llamar a `registrar_historial($pdo, 'reporte', 'Reportes', "PDF descargado. Periodo: {$fecha_ini} al {$fecha_fin}")`
  - Enviar headers `Content-Type: application/pdf` y `Content-Disposition: attachment; filename="..."` usando `report_filename()`
  - Envolver en try/catch; en caso de error mostrar página de error sin stack trace y loguear con `error_log()` si `APP_DEBUG`
  - _Requisitos: 3.1, 3.8, 6.4, 6.5_

  - [x] 8.1 Escribir prueba de propiedad — toda descarga exitosa queda registrada
    - **Propiedad 9: Toda descarga exitosa queda registrada en el historial**
    - **Valida: Requisito 6.4**
    - Clase `HistorialRegistrationTest`, método `testEveryDownloadIsLogged`
    - Verificar que `registrar_historial()` es invocada con `accion = 'reporte'` y descripción que incluye formato y periodo, tanto para PDF como para Excel

- [x] 9. Implementar `admin/reportes_historial.php` — modo descarga Excel
  - Dentro del mismo archivo, manejar `$_GET['action'] === 'excel'`
  - Llamar a `build_filter_query()`, ejecutar consultas, llamar a `build_excel()`
  - Usar `Xlsx` writer de PhpSpreadsheet para escribir a `php://output`
  - Llamar a `registrar_historial($pdo, 'reporte', 'Reportes', "Excel descargado. Periodo: {$fecha_ini} al {$fecha_fin}")`
  - Enviar headers `Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` y `Content-Disposition: attachment; filename="..."` usando `report_filename()`
  - Envolver en try/catch; en caso de error mostrar página de error sin stack trace
  - _Requisitos: 4.1, 4.6, 6.4, 6.5_

- [x] 10. Checkpoint final — Verificar integración completa
  - Ejecutar `./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/` y confirmar que todas las pruebas pasan.
  - Verificar manualmente que el enlace "Reportes" aparece en el sidebar bajo el grupo "Sistema".
  - Verificar que `admin/reportes_historial.php` carga correctamente en el navegador con sesión activa.
  - Consultar al usuario si hay dudas antes de dar por concluida la implementación.

## Notas

- Las tareas marcadas con `*` son opcionales y pueden omitirse para un MVP más rápido
- Cada tarea referencia requisitos específicos para trazabilidad
- Las pruebas de propiedad usan `@dataProvider` de PHPUnit con generadores de 100 casos aleatorios (sin librería PBT externa)
- Las funciones puras (`build_filter_query`, `build_pdf_html`, `build_excel`, `report_filename`) deben definirse antes del bloque de despacho de modos para facilitar las pruebas unitarias mediante `require` del archivo
- El archivo `vendor/autoload.php` debe cargarse con verificación de existencia y mensaje de error claro si falta
