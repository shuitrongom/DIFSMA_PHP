/**
 * upload-progress.js — Indicador visual de subida para archivos grandes
 * Muestra overlay con spinner cuando se envía un formulario con archivo > 1MB
 * NO intercepta el envío — deja que el formulario se envíe normalmente.
 */
(function () {
	const overlay = document.createElement('div');
	overlay.className = 'upload-overlay';
	overlay.innerHTML =
		'<div class="upload-box">' +
		'<p style="font-weight:600;font-size:1rem;margin-bottom:0.5rem;">Subiendo archivo...</p>' +
		'<p class="upload-filename text-muted small mb-2"></p>' +
		'<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:100%">Procesando...</div></div>' +
		'<p class="text-muted small mt-2">Por favor espere, no cierre la página.</p>' +
		'</div>';
	document.body.appendChild(overlay);

	const fnEl = overlay.querySelector('.upload-filename');

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
		if (file.size < 1024 * 1024) {
			return;
		}

		const sizeMB = (file.size / 1024 / 1024).toFixed(1);
		fnEl.textContent = file.name + ' (' + sizeMB + ' MB)';
		overlay.classList.add('active');
		// El formulario se envía normalmente — no hacemos preventDefault
	});
})();
