# Documento de Requisitos

## Introducción

Este documento define los requisitos para corregir y mejorar el slider de la galería fotográfica del DIF San Mateo Atenco (`comunicacion-social/galeria.php`). El problema principal es un bug donde las imágenes del slider aparecen en blanco en escritorio debido a estilos globales de Swiper en `header.php` que aplican `opacity: 0.3` y `transform: scale(0.82)`. Además, se rediseña la navegación del slider con flechas reposicionadas e indicadores de puntos (dots) con estilo profesional.

## Glosario

- **Gallery_Slider**: El componente Swiper.js dentro del modal de galería en `galeria.php`, identificado por la clase `.gallery-swiper`
- **Gallery_Modal**: El modal Bootstrap (`#galleryModal`) que contiene el slider de imágenes de un álbum
- **Navigation_Arrows**: Botones de flecha (anterior/siguiente) para navegar entre slides
- **Dot_Indicators**: Indicadores circulares de paginación que muestran el slide actual y el total de slides
- **Gallery_Controls**: Contenedor que agrupa las flechas de navegación y los dot indicators debajo del slider
- **Global_Swiper_Styles**: Estilos CSS definidos en `includes/header.php` que aplican `opacity` y `transform` a todos los elementos `.swiper-slide`

## Requisitos

### Requisito 1: Visibilidad de imágenes en escritorio

**User Story:** Como visitante del sitio, quiero ver las imágenes de la galería correctamente en escritorio, para poder navegar los álbumes fotográficos sin importar el dispositivo.

#### Criterios de Aceptación

1. THE Gallery_Slider SHALL display all slide images at full opacity (`opacity: 1`) on desktop viewports
2. THE Gallery_Slider SHALL display all slide images at full scale (`transform: scale(1)`) on desktop viewports
3. WHILE Global_Swiper_Styles apply `opacity: 0.3` and `transform: scale(0.82)` to `.swiper-slide` elements, THE Gallery_Slider SHALL override those styles using scoped CSS selectors with `!important` declarations
4. WHEN the Gallery_Modal is opened on any viewport width, THE Gallery_Slider SHALL render images visibly and without blank or white appearance

### Requisito 2: Reposicionamiento de flechas de navegación

**User Story:** Como visitante del sitio, quiero que las flechas de navegación estén ubicadas debajo del slider, para tener una experiencia de navegación más limpia y profesional.

#### Criterios de Aceptación

1. WHEN the Gallery_Modal is opened, THE Navigation_Arrows SHALL appear below the slider area inside the Gallery_Controls container
2. THE Navigation_Arrows SHALL be rendered as circular buttons with a border color of `rgb(200,16,44)` and white background
3. WHEN a user hovers over a Navigation_Arrow, THE Navigation_Arrow SHALL change its background to `rgb(200,16,44)` and its icon color to white
4. THE Navigation_Arrows SHALL use external Swiper navigation (elements outside the `.gallery-swiper` container) connected via `nextEl` and `prevEl` configuration

### Requisito 3: Indicadores de puntos (dots)

**User Story:** Como visitante del sitio, quiero ver indicadores de puntos que muestren mi posición en el álbum, para saber cuántas imágenes hay y en cuál me encuentro.

#### Criterios de Aceptación

1. WHEN the Gallery_Modal is opened with an album, THE Dot_Indicators SHALL render one dot per image in the album
2. THE Dot_Indicators SHALL display inactive dots with a black background (`#000`) and circular shape (`border-radius: 50%`)
3. WHEN a slide is active, THE Dot_Indicators SHALL display the corresponding dot with a red background (`rgb(200,16,44)`) and a pill shape (`width: 28px`, `border-radius: 5px`)
4. WHEN the active slide changes, THE Dot_Indicators SHALL update so that exactly one dot has the active style at any time
5. THE Dot_Indicators SHALL be clickable, allowing the user to navigate directly to a specific slide

### Requisito 4: Disposición profesional de controles

**User Story:** Como visitante del sitio, quiero que los controles de navegación tengan un aspecto profesional y ordenado, para una experiencia visual agradable.

#### Criterios de Aceptación

1. THE Gallery_Controls SHALL be positioned below the slider using a flex layout with centered alignment
2. THE Gallery_Controls SHALL display the previous arrow, then the Dot_Indicators, then the next arrow in a single horizontal row
3. THE Gallery_Controls SHALL maintain consistent spacing (`gap: 16px`) between the arrows and the dots
4. WHEN the viewport is mobile-sized, THE Gallery_Controls SHALL remain functional and properly aligned

### Requisito 5: Inicialización y limpieza del slider

**User Story:** Como desarrollador, quiero que el slider se inicialice y destruya correctamente al abrir y cerrar el modal, para evitar fugas de memoria y comportamiento inesperado.

#### Criterios de Aceptación

1. WHEN the Gallery_Modal fires the `shown.bs.modal` event, THE Gallery_Slider SHALL initialize a new Swiper instance with navigation and pagination configured
2. IF a previous Swiper instance exists when initializing, THEN THE Gallery_Slider SHALL destroy the previous instance before creating a new one
3. WHEN the Gallery_Modal is closed, THE Gallery_Slider SHALL destroy the Swiper instance and clear the slide wrapper innerHTML
4. WHEN an album has no images, THE Gallery_Slider SHALL not open the modal and shall return without action

### Requisito 6: No regresión en otros sliders

**User Story:** Como desarrollador, quiero que los cambios en la galería no afecten otros sliders del sitio, para mantener la estabilidad del sistema.

#### Criterios de Aceptación

1. THE Gallery_Slider CSS overrides SHALL use scoped selectors (`.gallery-swiper .swiper-slide`) that only affect the gallery modal slider
2. WHEN the gallery page is loaded, THE Global_Swiper_Styles in `header.php` SHALL remain unmodified
3. THE Gallery_Slider changes SHALL not affect the homepage main slider or the communication social slider
