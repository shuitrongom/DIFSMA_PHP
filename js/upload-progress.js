/**
 * upload-progress.js — Barra de progreso para subida de archivos en admin
 * Intercepta formularios con enctype="multipart/form-data" y muestra progreso via XMLHttpRequest.
 */
(function () {
	// Crear overlay
	const overlay = document.createElement('div');
	overlay.className = 'upload-overlay';
	overlay.innerHTML =
		'<div class="upload-box">' +
		'<p style="font-weight:600;font-size:1rem;margin-bottom:0.5rem;">Subiendo archivo...</p>' +
		'<p class="upload-filename text-muted small mb-2"></p>' +
		'<div class="progress"><div class="progress-bar" role="progressbar" style="width:0%">0%</div></div>' +
		'<p class="upload-size text-muted small mt-2"></p>' +
		'</div>';
	document.body.appendChild(overlay);

	const bar = overlay.querySelector('.progress-bar');
	const fnEl = overlay.querySelector('.upload-filename');
	const szEl = overlay.querySelector('.upload-size');

	function formatMB(bytes) {
		return (bytes / 1024 / 1024).toFixed(1) + ' MB';
	}

	document.addEventListener('submit', function (e) {
		const form = e.target;
		if (!form.enctype || form.enctype !== 'multipart/form-data') {
			return;
		}

		// Verificar si hay archivo seleccionado
		const fileInput = form.querySelector('input[type="file"]');
		if (!fileInput || !fileInput.files || !fileInput.files.length) {
			return;
		}

		const file = fileInput.files[0];
		// Solo mostrar progreso para archivos > 1MB
		if (file.size < 1024 * 1024) {
			return;
		}

		e.preventDefault();

		fnEl.textContent = file.name;
		szEl.textContent = '0 / ' + formatMB(file.size);
		bar.style.width = '0%';
		bar.textContent = '0%';
		overlay.classList.add('active');

		const formData = new FormData(form);
		const xhr = new XMLHttpRequest();

		xhr.upload.addEventListener('progress', function (ev) {
			if (ev.lengthComputable) {
				const pct = Math.round((ev.loaded / ev.total) * 100);
				bar.style.width = pct + '%';
				bar.textContent = pct + '%';
				szEl.textContent =
					formatMB(ev.loaded) + ' / ' + formatMB(ev.total);
			}
		});

		xhr.addEventListener('load', function () {
			bar.style.width = '100%';
			bar.textContent = '100%';
			// Redirigir a la misma página (el servidor hace redirect via header)
			window.location.href = form.action || window.location.href;
		});

		xhr.addEventListener('error', function () {
			overlay.classList.remove('active');
			alert('Error al subir el archivo. Intente de nuevo.');
		});

		xhr.open('POST', form.action || window.location.href);
		xhr.send(formData);
	});
})();
