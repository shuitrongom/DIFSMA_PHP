/**
 * upload-progress.js — Subida con iframe oculto + overlay visual
 * El formulario se envía normalmente al iframe, garantizando que el archivo se suba.
 */
(function () {
	const overlay = document.createElement('div');
	overlay.className = 'upload-overlay';
	overlay.innerHTML =
		'<div class="upload-box">' +
		'<p style="font-weight:600;font-size:1rem;margin-bottom:0.5rem;">Subiendo archivo...</p>' +
		'<p class="upload-filename text-muted small mb-1"></p>' +
		'<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:100%"></div></div>' +
		'<p class="upload-size text-muted small mt-2"></p>' +
		'<p class="text-muted small">Por favor espere, no cierre la página.</p>' +
		'</div>';
	document.body.appendChild(overlay);

	const fnEl = overlay.querySelector('.upload-filename');
	const szEl = overlay.querySelector('.upload-size');

	function fmb(b) {
		return (b / 1048576).toFixed(1) + ' MB';
	}

	// Crear iframe oculto
	const iframe = document.createElement('iframe');
	iframe.name = 'uploadFrame';
	iframe.style.display = 'none';
	document.body.appendChild(iframe);

	document.addEventListener('submit', function (e) {
		const form = e.target;
		if (!form.enctype || form.enctype !== 'multipart/form-data') {
			return;
		}

		const fileInput = form.querySelector('input[type="file"]');
		if (!fileInput || !fileInput.files || !fileInput.files.length) {
			return;
		}

		const file = fileInput.files[0];
		if (file.size < 512 * 1024) {
			return;
		}

		// Mostrar overlay
		fnEl.textContent = file.name;
		szEl.textContent = fmb(file.size);
		overlay.classList.add('active');

		// Redirigir el formulario al iframe
		const originalTarget = form.target;
		form.target = 'uploadFrame';

		// Cuando el iframe cargue (servidor terminó de procesar), recargar la página
		iframe.onload = function () {
			form.target = originalTarget || '';
			iframe.onload = null;
			overlay.classList.remove('active');
			window.location.reload();
		};

		// NO hacemos preventDefault — el formulario se envía normalmente al iframe
	});
})();
