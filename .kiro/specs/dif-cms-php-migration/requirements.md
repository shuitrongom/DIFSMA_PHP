# Documento de Requisitos

## Introducción

Migración del sitio web estático HTML/CSS/JS del DIF San Mateo Atenco a PHP, conservando el diseño visual actual (Bootstrap, Owl Carousel, Lightbox, Swiper, estilos personalizados y paleta de colores). Se añade un panel de administración con autenticación segura que permite gestionar el contenido dinámico de cada sección del sitio sin modificar código fuente.

## Glosario

- **CMS**: Sistema de gestión de contenido implementado en PHP para este proyecto.
- **Panel_Admin**: Interfaz web protegida por autenticación desde la cual el administrador gestiona el contenido del sitio.
- **Administrador**: Usuario autenticado con acceso al Panel_Admin.
- **Sistema**: El sitio web DIF San Mateo Atenco migrado a PHP.
- **Slider**: Componente de carrusel de imágenes que se muestra en el frontend.
- **Slider_Principal**: Primer slider de la página de inicio (carrusel de pantalla completa).
- **Slider_DIF_Comunica**: Segundo slider de la página de inicio (sección "DIF Comunica", carrusel Swiper 3D).
- **Slider_Noticias**: Carrusel de imágenes agrupadas por fecha, presente en index y en noticias.html.
- **Galeria**: Sección de galería fotográfica con grupos de imágenes y soporte Lightbox.
- **SEAC**: Página de transparencia "Sistema de Evaluaciones de la Armonización Contable".
- **Bloque_SEAC**: Elemento `<article class="question">` que agrupa una tabla de PDFs por año y trimestre.
- **DB**: Base de datos MySQL/MariaDB utilizada para almacenar el contenido dinámico.
- **Upload_Handler**: Componente PHP que procesa la subida de archivos (imágenes y PDFs).
- **Auth_Guard**: Componente PHP que verifica la sesión activa antes de permitir acceso al Panel_Admin.
- **Tramite**: Página de Trámites y Servicios (PMPNNA, DAAM, DANF, DAD, DPAF, DSJAIG).

---

## Requisitos

### Requisito 1: Autenticación del Panel de Administración

**User Story:** Como único administrador del sistema, quiero iniciar sesión con usuario y contraseña, para acceder de forma segura al panel de administración.

#### Criterios de Aceptación

1. THE Sistema SHALL proporcionar una página de login en `/admin/login.php` con formulario de usuario y contraseña para un único Administrador predefinido en la DB.
2. WHEN el Administrador envía credenciales válidas, THE Auth_Guard SHALL iniciar una sesión PHP autenticada y redirigir al dashboard del Panel_Admin.
3. IF el Administrador envía credenciales inválidas, THEN THE Auth_Guard SHALL mostrar un mensaje de error y no iniciar sesión.
4. WHILE el Administrador no tiene sesión activa, THE Auth_Guard SHALL redirigir cualquier ruta de `/admin/` a la página de login.
5. WHEN el Administrador cierra sesión, THE Auth_Guard SHALL destruir la sesión y redirigir a la página de login.
6. THE Sistema SHALL almacenar la contraseña del administrador usando `password_hash()` con el algoritmo `PASSWORD_BCRYPT`.
7. THE Sistema SHALL proteger el formulario de login contra ataques de fuerza bruta limitando a 5 intentos fallidos consecutivos por IP en un período de 15 minutos, bloqueando temporalmente el acceso tras superarlos.
8. THE Sistema SHALL soportar únicamente un registro de administrador en la DB, sin funcionalidad de registro de nuevos usuarios ni gestión de roles.

---

### Requisito 2: Slider Principal (index.php)

**User Story:** Como administrador, quiero gestionar las imágenes del Slider_Principal de la página de inicio, para mantener el contenido visual actualizado.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección dedicada para gestionar el Slider_Principal con listado de imágenes actuales.
2. WHEN el Administrador sube una imagen nueva para el Slider_Principal, THE Upload_Handler SHALL validar que el archivo sea de tipo JPG, PNG o WEBP con tamaño máximo de 5 MB, almacenarlo en el servidor y registrar la referencia en la DB.
3. WHEN el Administrador edita una imagen del Slider_Principal, THE Panel_Admin SHALL permitir reemplazar el archivo de imagen y actualizar el registro en la DB.
4. WHEN el Administrador elimina una imagen del Slider_Principal, THE Sistema SHALL eliminar el archivo del servidor y el registro de la DB.
5. THE Sistema SHALL renderizar el Slider_Principal en index.php cargando dinámicamente las imágenes almacenadas en la DB.
6. IF no existen imágenes registradas para el Slider_Principal, THEN THE Sistema SHALL mostrar una imagen de marcador de posición predeterminada.

---

### Requisito 3: Slider DIF Comunica (index.php)

**User Story:** Como administrador, quiero gestionar las imágenes del Slider_DIF_Comunica (sección "DIF Comunica"), para publicar efemérides y comunicados visuales.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección dedicada para gestionar el Slider_DIF_Comunica con listado de imágenes actuales.
2. WHEN el Administrador sube una imagen nueva para el Slider_DIF_Comunica, THE Upload_Handler SHALL validar que el archivo sea de tipo JPG, PNG o WEBP con tamaño máximo de 5 MB, almacenarlo en el servidor y registrar la referencia en la DB.
3. WHEN el Administrador edita una imagen del Slider_DIF_Comunica, THE Panel_Admin SHALL permitir reemplazar el archivo y actualizar el registro en la DB.
4. WHEN el Administrador elimina una imagen del Slider_DIF_Comunica, THE Sistema SHALL eliminar el archivo del servidor y el registro de la DB.
5. THE Sistema SHALL renderizar el Slider_DIF_Comunica en index.php usando el componente Swiper con las imágenes almacenadas en la DB.

---

### Requisito 4: Sección Últimas Noticias por Día (index.php y noticias.php)

**User Story:** Como administrador, quiero gestionar imágenes de noticias agrupadas por fecha, para que el sitio muestre automáticamente las noticias del día actual.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección para gestionar imágenes del Slider_Noticias con campo de fecha asociada a cada imagen.
2. WHEN el Administrador sube una imagen de noticia, THE Upload_Handler SHALL requerir una fecha asociada, validar el archivo (JPG, PNG, WEBP, máximo 5 MB), almacenarlo y registrar imagen y fecha en la DB.
3. WHEN el Administrador edita una imagen de noticia, THE Panel_Admin SHALL permitir modificar el archivo y la fecha asociada.
4. WHEN el Administrador elimina una imagen de noticia, THE Sistema SHALL eliminar el archivo del servidor y el registro de la DB.
5. WHEN un visitante carga index.php o noticias.php, THE Sistema SHALL consultar la DB y mostrar en el Slider_Noticias únicamente las imágenes cuya fecha coincida con la fecha actual del servidor.
6. IF no existen imágenes para la fecha actual, THEN THE Sistema SHALL mostrar un mensaje indicando que no hay noticias disponibles para el día de hoy.
7. THE Sistema SHALL mantener el mismo estilo visual del Slider_Noticias (carrusel con navegación por flechas y tres columnas) definido en el diseño original.

---

### Requisito 5: Presidencia (presidencia.php)

**User Story:** Como administrador, quiero gestionar la imagen del presidente del DIF, para mantener actualizada la fotografía oficial.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección para gestionar la imagen de presidencia con vista previa de la imagen actual.
2. WHEN el Administrador sube o reemplaza la imagen de presidencia, THE Upload_Handler SHALL validar que el archivo sea JPG, PNG o WEBP con tamaño máximo de 5 MB, almacenarlo y actualizar el registro en la DB.
3. THE Sistema SHALL renderizar presidencia.php mostrando la imagen almacenada en la DB con el mismo diseño de tarjeta de equipo del original.
4. IF no existe imagen registrada para presidencia, THEN THE Sistema SHALL mostrar la imagen predeterminada `../img/Presidente.png`.

---

### Requisito 6: Direcciones (direcciones.php)

**User Story:** Como administrador, quiero gestionar las imágenes de cada dirección del DIF por departamento, para mantener actualizadas las fotografías del equipo directivo.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección para gestionar las imágenes de direcciones, organizada por departamento con el nombre del departamento como etiqueta.
2. WHEN el Administrador sube o reemplaza la imagen de un departamento, THE Upload_Handler SHALL validar el archivo (JPG, PNG, WEBP, máximo 5 MB), almacenarlo y actualizar el registro correspondiente en la DB.
3. WHEN el Administrador elimina la imagen de un departamento, THE Sistema SHALL eliminar el archivo del servidor y restaurar la imagen predeterminada para ese departamento.
4. THE Sistema SHALL renderizar direcciones.php mostrando las imágenes de cada departamento almacenadas en la DB con el mismo diseño de tarjetas de equipo del original.
5. THE Sistema SHALL mantener los nombres y cargos de los directivos como texto editable en la DB, permitiendo al Administrador actualizarlos desde el Panel_Admin.

---

### Requisito 7: Organigrama (organigrama.php)

**User Story:** Como administrador, quiero subir el PDF del organigrama institucional, para que los visitantes puedan consultarlo en línea.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección para gestionar el PDF del organigrama con indicación del archivo actualmente cargado.
2. WHEN el Administrador sube un nuevo PDF de organigrama, THE Upload_Handler SHALL validar que el archivo sea de tipo PDF con tamaño máximo de 20 MB, almacenarlo y actualizar el registro en la DB.
3. THE Sistema SHALL renderizar organigrama.php mostrando el PDF almacenado embebido en la página usando un elemento `<iframe>` o `<embed>` con el mismo estilo visual del original.
4. IF no existe PDF registrado para el organigrama, THEN THE Sistema SHALL mostrar la imagen `../img/organigrama_dif_sma.jpg` como alternativa.

---

### Requisito 8: Trámites y Servicios (tramites.php)

**User Story:** Como administrador, quiero gestionar la imagen y el texto de cada página de Trámites y Servicios, para mantener actualizada la información de cada dirección.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección para gestionar cada una de las seis páginas de Trámites y Servicios (PMPNNA, DAAM, DANF, DAD, DPAF, DSJAIG) con campos de imagen y texto editable.
2. WHEN el Administrador sube o reemplaza la imagen de un trámite, THE Upload_Handler SHALL validar el archivo (JPG, PNG, WEBP, máximo 5 MB), almacenarlo y actualizar el registro en la DB.
3. WHEN el Administrador edita el texto de un trámite, THE Panel_Admin SHALL proporcionar un editor de texto enriquecido y guardar el contenido en la DB.
4. THE Sistema SHALL renderizar cada página de trámite cargando dinámicamente la imagen y el texto almacenados en la DB, manteniendo el diseño y estilos originales.
5. IF no existe imagen registrada para un trámite, THEN THE Sistema SHALL mostrar la imagen predeterminada original de ese trámite.

---

### Requisito 9: Galería Fotográfica (galeria.php)

**User Story:** Como administrador, quiero gestionar álbumes de fotos con múltiples imágenes, para que los visitantes puedan explorar la galería institucional con Lightbox.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección para gestionar álbumes de la Galeria con campos de nombre del álbum, fecha y portada.
2. WHEN el Administrador crea un álbum nuevo, THE Panel_Admin SHALL requerir nombre, fecha y al menos una imagen de portada, registrar el álbum en la DB y permitir agregar imágenes adicionales al álbum.
3. WHEN el Administrador agrega imágenes a un álbum, THE Upload_Handler SHALL validar cada archivo (JPG, PNG, WEBP, máximo 5 MB), almacenarlo y registrar la referencia en la DB asociada al álbum.
4. WHEN el Administrador elimina una imagen de un álbum, THE Sistema SHALL eliminar el archivo del servidor y el registro de la DB.
5. WHEN el Administrador elimina un álbum completo, THE Sistema SHALL eliminar todos los archivos de imágenes del álbum del servidor y todos los registros asociados en la DB.
6. THE Sistema SHALL renderizar galeria.php mostrando todos los álbumes almacenados en la DB con imagen de portada, nombre y fecha, con soporte Lightbox para visualizar las imágenes del álbum al hacer clic.
7. THE Sistema SHALL mantener el mismo diseño de tarjetas de eventos con overlay y Lightbox del original.

---

### Requisito 10: Noticias con Slider por Día (noticias.php)

**User Story:** Como administrador, quiero que la página de Noticias muestre un slider igual al del index con imágenes del día actual, para mantener coherencia informativa entre secciones.

#### Criterios de Aceptación

1. THE Sistema SHALL reutilizar el mismo componente Slider_Noticias definido en el Requisito 4 para renderizar noticias.php.
2. WHEN un visitante carga noticias.php, THE Sistema SHALL mostrar únicamente las imágenes de noticias cuya fecha coincida con la fecha actual del servidor.
3. THE Sistema SHALL mantener el mismo diseño visual de la sección de noticias del index (carrusel con flechas de navegación y tres columnas responsivas).
4. IF no existen imágenes para la fecha actual en noticias.php, THEN THE Sistema SHALL mostrar un mensaje indicando que no hay noticias disponibles para el día de hoy.

---

### Requisito 11: SEAC — Gestión de PDFs y Bloques por Año/Trimestre (SEAC.php)

**User Story:** Como administrador, quiero gestionar los PDFs del SEAC organizados por año y trimestre, y poder agregar nuevos bloques de año, para mantener actualizada la información de armonización contable.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección para gestionar el SEAC con los Bloques_SEAC existentes organizados por año.
2. WHEN el Administrador agrega un nuevo Bloque_SEAC, THE Panel_Admin SHALL requerir el año y generar la estructura de tabla con los cuatro trimestres y las filas de conceptos predefinidas.
3. WHEN el Administrador sube un PDF a una celda de trimestre de un concepto, THE Upload_Handler SHALL validar que el archivo sea de tipo PDF con tamaño máximo de 20 MB, almacenarlo en el servidor y registrar la referencia en la DB asociada al año, trimestre y concepto.
4. WHEN el Administrador reemplaza un PDF existente en una celda, THE Upload_Handler SHALL eliminar el archivo anterior del servidor, almacenar el nuevo y actualizar el registro en la DB.
5. WHEN el Administrador elimina un PDF de una celda, THE Sistema SHALL eliminar el archivo del servidor y limpiar la referencia en la DB, mostrando la celda como vacía.
6. WHEN el Administrador elimina un Bloque_SEAC completo, THE Sistema SHALL eliminar todos los PDFs asociados del servidor y todos los registros del bloque en la DB.
7. THE Sistema SHALL renderizar SEAC.php generando dinámicamente los Bloques_SEAC desde la DB, manteniendo el mismo estilo de acordeón con tablas de trimestres y el visor de PDF en modal del original.
8. THE Sistema SHALL mantener el mismo comportamiento del modal de visualización de PDF (apertura en iframe dentro de modal Bootstrap) del diseño original.

---

### Requisito 12: Gestión de "Nuestros Programas" (index.php)

**User Story:** Como administrador, quiero gestionar las tarjetas de la sección "Todos Nuestros Programas" del index, para agregar, editar y eliminar programas con imagen y texto descriptivo sin modificar código fuente.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección dedicada para gestionar los programas de la sección "Todos Nuestros Programas" con listado de programas actuales.
2. WHEN el Administrador agrega un programa nuevo, THE Panel_Admin SHALL requerir una imagen de portada, un nombre de programa y al menos una sección de acordeón con título y contenido, registrar el programa en la DB y almacenar la imagen en el servidor.
3. WHEN el Administrador sube la imagen de un programa, THE Upload_Handler SHALL validar que el archivo sea de tipo JPG, PNG o WEBP con tamaño máximo de 5 MB, almacenarlo en el servidor y registrar la referencia en la DB.
4. WHEN el Administrador edita un programa existente, THE Panel_Admin SHALL permitir modificar la imagen, el nombre y las secciones de acordeón, y actualizar los registros en la DB.
5. WHEN el Administrador elimina un programa, THE Sistema SHALL eliminar el archivo de imagen del servidor y todos los registros asociados en la DB.
6. THE Sistema SHALL renderizar la sección "Todos Nuestros Programas" en index.php cargando dinámicamente los programas almacenados en la DB, manteniendo el mismo diseño de tarjetas con dropdown y acordeón del original.
7. IF no existen programas registrados en la DB, THEN THE Sistema SHALL mostrar un mensaje indicando que no hay programas disponibles.

---

### Requisito 13: Gestión de Sección "Transparencia" (index.php)

**User Story:** Como administrador, quiero gestionar los enlaces y contenido de la sección de Transparencia del index, para agregar, editar y eliminar entradas sin modificar código fuente.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección dedicada para gestionar los elementos de Transparencia del index con listado de entradas actuales.
2. WHEN el Administrador agrega una entrada de transparencia, THE Panel_Admin SHALL requerir un título, una URL de destino y opcionalmente una imagen o ícono representativo, y registrar la entrada en la DB.
3. WHEN el Administrador edita una entrada de transparencia, THE Panel_Admin SHALL permitir modificar el título, la URL y la imagen/ícono, y actualizar el registro en la DB.
4. WHEN el Administrador elimina una entrada de transparencia, THE Sistema SHALL eliminar el registro de la DB y, si aplica, el archivo de imagen del servidor.
5. THE Sistema SHALL renderizar la sección de Transparencia en index.php cargando dinámicamente las entradas almacenadas en la DB, manteniendo el mismo diseño visual del original.
6. IF no existen entradas de transparencia registradas en la DB, THEN THE Sistema SHALL mostrar un mensaje indicando que no hay contenido de transparencia disponible.

---

### Requisito 14: Gestión del Footer

**User Story:** Como administrador, quiero editar el contenido del footer del sitio, para mantener actualizados los textos, datos de contacto y redes sociales sin modificar código fuente.

#### Criterios de Aceptación

1. THE Panel_Admin SHALL mostrar una sección dedicada para gestionar el contenido del footer con campos para: texto institucional, dirección, teléfono, correo electrónico y URLs de redes sociales.
2. WHEN el Administrador guarda cambios en el footer, THE Panel_Admin SHALL validar que los campos de URL sean URLs válidas y que los campos de texto no excedan 500 caracteres, y actualizar los registros en la DB.
3. THE Sistema SHALL renderizar el footer en todas las páginas PHP del sitio cargando dinámicamente el contenido almacenado en la DB.
4. IF no existe configuración de footer en la DB, THEN THE Sistema SHALL mostrar los valores predeterminados del diseño original.
5. THE Sistema SHALL mantener el mismo diseño visual y estructura de columnas del footer original al renderizar el contenido dinámico.

---

### Requisito 15: Integridad y Seguridad del Sistema

**User Story:** Como administrador, quiero que el sistema sea seguro y confiable, para proteger el contenido y los datos del DIF.

#### Criterios de Aceptación

1. THE Sistema SHALL sanitizar todas las entradas de usuario antes de ejecutar consultas a la DB usando sentencias preparadas (PDO con `prepare`/`execute`).
2. THE Upload_Handler SHALL validar el tipo MIME real del archivo (no solo la extensión) antes de almacenarlo en el servidor.
3. THE Upload_Handler SHALL renombrar todos los archivos subidos con un nombre generado aleatoriamente para evitar colisiones y acceso predecible.
4. THE Sistema SHALL almacenar los archivos subidos fuera del directorio raíz web o en un directorio sin ejecución de scripts PHP.
5. THE Sistema SHALL incluir protección CSRF en todos los formularios del Panel_Admin mediante tokens de sesión únicos por formulario.
6. THE Sistema SHALL registrar en un archivo de log los intentos de login fallidos con timestamp e IP del solicitante.
7. THE Sistema SHALL mantener todos los estilos, fuentes, librerías (Bootstrap, Owl Carousel, Lightbox, Swiper, WOW.js, AOS) y la paleta de colores del diseño original en todas las páginas PHP migradas.
