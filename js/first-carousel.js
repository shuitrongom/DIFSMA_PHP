(function(){
  const carousel = document.getElementById('carousel');
  const stage = document.getElementById('stage');
  const slides = Array.from(stage.children);
  const n = slides.length;
  const angle = 360 / n;
  let index = 0;
  let radius = 0;
  const transitionDuration = getComputedStyle(document.documentElement).getPropertyValue('--transition') || '5s';
  const intervalMs = parseFloat(transitionDuration) * 1000; // sincroniza intervalo con la duración de la transición (5s)

  function layout() {
    const w = carousel.clientWidth;
    // radio para distribuir las caras en cilindro (fórmula geométrica)
    radius = Math.round((w / 2) / Math.tan(Math.PI / n));
    slides.forEach((slide, i) => {
      const rot = i * angle;
      slide.style.transform = `rotateY(${rot}deg) translateZ(${radius}px)`;
    });
    // actualiza posición inicial
    rotateTo(index, 0);
  }

  function rotateTo(i, durationMs) {
    // set transition-duration directamente si queremos override temporal
    if (durationMs !== undefined) {
      stage.style.transition = `transform ${durationMs}ms ease-in-out`;
      // restaurar después de pequeño retardo para no perder valor root
      setTimeout(()=> {
        stage.style.transition = '';
      }, durationMs + 20);
    }
    const rot = -i * angle;
    stage.style.transform = `translateZ(-${radius}px) rotateY(${rot}deg)`;
    updateDots(i);
  }

  function next() { index = (index + 1) % n; rotateTo(index); }
  function prev() { index = (index - 1 + n) % n; rotateTo(index); }

  // puntos indicadores
  const dotsContainer = document.getElementById('dots');
  slides.forEach((_, i) => {
    const d = document.createElement('div');
    d.className = 'dot' + (i===0 ? ' active' : '');
    d.addEventListener('click', ()=> {
      index = i;
      rotateTo(index);
      restartAuto();
    });
    dotsContainer.appendChild(d);
  });
  function updateDots(i) {
    Array.from(dotsContainer.children).forEach((d, idx) => {
      d.classList.toggle('active', idx === i);
    });
  }

  // botones
  document.getElementById('prev').addEventListener('click', ()=> { prev(); restartAuto(); });
  document.getElementById('next').addEventListener('click', ()=> { next(); restartAuto(); });

  // auto-rotación infinita (interval sincronizado con la duración de transición)
  let timer = null;
  function startAuto() {
    stopAuto();
    timer = setInterval(next, intervalMs);
  }
  function stopAuto(){ if (timer) { clearInterval(timer); timer = null; } }
  function restartAuto(){ stopAuto(); startAuto(); }

  // pausa al hover
  carousel.addEventListener('mouseenter', stopAuto);
  carousel.addEventListener('mouseleave', startAuto);

  // recalcular layout al cambiar tamaño
  window.addEventListener('resize', () => { layout(); });

  // espera a cargar imágenes para calcular medidas más precisas
  let loaded = 0;
  slides.forEach(imgWrap => {
    const img = imgWrap.querySelector('img');
    if (img.complete) {
      loaded++;
      if (loaded === slides.length) layout();
    } else {
      img.addEventListener('load', () => {
        loaded++;
        if (loaded === slides.length) layout();
      });
    }
  });

  // inicio
  startAuto();
})();