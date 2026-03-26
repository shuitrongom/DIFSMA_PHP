# Implementation Plan: Gallery Slider Fix

## Overview

Fix the gallery slider in `comunicacion-social/galeria.php` by overriding global Swiper styles that cause blank images on desktop, restructuring the modal HTML to move navigation arrows below the slider, adding dot pagination indicators, and updating the Swiper JS initialization. All changes are scoped to `galeria.php` only.

## Tasks

- [x] 1. Fix CSS: Override global Swiper styles for gallery slider visibility
  - [x] 1.1 Add scoped CSS overrides for `.gallery-swiper .swiper-slide` to set `opacity: 1 !important` and `transform: scale(1) !important`
    - This ensures images are visible on desktop by overriding the global styles from `header.php`
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 6.1_

  - [x] 1.2 Add CSS styles for `.gallery-controls` container, `.gallery-nav-btn` arrow buttons, and `.gallery-pagination` dot indicators
    - Gallery controls: flex layout, centered, `gap: 16px`, positioned below the slider
    - Arrow buttons: circular, `border: 2px solid rgb(200,16,44)`, white background, hover state with red background and white icon
    - Dots inactive: `background: #000`, `10px` circle; dots active: `background: rgb(200,16,44)`, `width: 28px`, pill shape (`border-radius: 5px`)
    - _Requirements: 2.1, 2.2, 2.3, 3.1, 3.2, 3.3, 4.1, 4.2, 4.3, 4.4_

  - [x] 1.3 Remove existing `.gallery-swiper .swiper-button-next` and `.gallery-swiper .swiper-button-prev` CSS rules (no longer needed with external navigation)
    - _Requirements: 2.4, 6.1_

- [x] 2. Restructure modal HTML for external navigation and pagination
  - [x] 2.1 Remove the internal `.swiper-button-next` and `.swiper-button-prev` divs from inside the `.gallery-swiper` container
    - _Requirements: 2.1, 2.4_

  - [x] 2.2 Add the `.gallery-controls` container below the `.gallery-swiper` div with prev button, pagination div, and next button
    - Use `<button>` elements with `aria-label` attributes for accessibility
    - Use Font Awesome chevron icons (`fa-chevron-left`, `fa-chevron-right`)
    - _Requirements: 2.1, 3.1, 4.1, 4.2_

- [x] 3. Update Swiper JS initialization with external navigation and pagination
  - [x] 3.1 Update the `openGallery` function's Swiper initialization to use external `nextEl: '.gallery-next'` and `prevEl: '.gallery-prev'`, and add `pagination: { el: '.gallery-pagination', clickable: true }`
    - _Requirements: 2.4, 3.1, 3.4, 3.5, 5.1_

  - [x] 3.2 Ensure Swiper destroy and cleanup logic remains correct on modal close (`hidden.bs.modal` event)
    - Verify previous instance is destroyed before creating new one on re-open
    - Verify wrapper innerHTML is cleared on close
    - _Requirements: 5.2, 5.3, 5.4_

- [x] 4. Checkpoint - Verify all changes
  - Ensure all tests pass, ask the user if questions arise.
  - Verify images are visible on desktop (no blank/white appearance)
  - Verify arrows appear below the slider with correct styling
  - Verify dots show black inactive and red active states
  - Verify no changes were made to `includes/header.php` or any other file besides `galeria.php`
  - _Requirements: 1.4, 4.4, 6.2, 6.3_

- [x] 5. Write manual regression verification checklist
  - **Property 1: All slides have opacity 1 on all viewports**
  - **Property 2: Inactive dots are black, active dot is red**
  - **Property 3: Number of dots equals number of images in album**
  - **Property 4: Exactly one dot is active at any time**
  - **Property 5: Swiper initializes after shown.bs.modal event**
  - **Property 6: Swiper is destroyed and wrapper cleared on modal close**
  - **Validates: Requirements 1.1, 1.2, 3.1, 3.2, 3.3, 3.4, 5.1, 5.2, 5.3, 6.1, 6.2, 6.3**

- [x] 6. Final checkpoint - Ensure all changes are complete
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- All changes are scoped to `comunicacion-social/galeria.php` only (CSS, HTML, and JS sections)
- No new dependencies are introduced; Swiper.js, Bootstrap, and Font Awesome are already loaded
- The `includes/header.php` file must NOT be modified to avoid regression on other sliders
