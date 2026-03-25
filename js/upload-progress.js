/**
 * upload-progress.js — Barra de progreso real con % y MB
 * Usa iframe para envío real + XHR duplicado solo para progreso visual.
 * El iframe garantiza que el archivo se suba; el XHR solo muestra el avance.
 */
(function () {
	const overlay = document.createElement('div');
	overlay.className = 'upload-overlay';
	overlay.innerHTML =
		'<div class="upload-box">' +
		'<p style="font-weight:600;font-size:1rem;margin-bottom:0.5rem;">Subiendo archivo...</p>' +
		'<p class="upload-filename text-muted small mb-1"></p>' +
		'<div class="progress"><div class="progress-bar" role="progressbar" style="width:0%">0%</div></div>' +
		'<p class="upload-size text-muted small mt-2"></p>' +
		'<p class="text-muted small">Por favor espere, no cierre la página.</p>' +
		'</div>';
	document.body.appendChild(overlay);

	const bar = overlay.querySelector('.progress-bar');
	const fnEl = overlay.querySelector('.upload-filename');
	const szEl = overlay.querySelector('.upload-size');

	function fmb(b) {
		return (b / 1048576).toFixed(1) + ' MB';
	}

	// Iframe oculto para envío real
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

		e.preventDefault();

		fnEl.textContent = file.name;
		szEl.textContent = '0 MB / ' + fmb(file.size);
		bar.style.width = '0%';
		bar.textContent = '0%';
		overlay.classList.add('active');

		// Enviar via XHR para tener progreso
		const fd = new FormData(form);
		let action = form.action || window.location.href;

		// Asegurar URL absoluta
		if (action.indexOf('http') !== 0) {
			const a = document.createElement('a');
			a.href = action;
			action = a.href;
		}

		const xhr = new XMLHttpRequest();

		xhr.upload.addEventListener('progress', function (ev) {
			if (ev.lengthComputable) {
				const pct = Math.round((ev.loaded / ev.total) * 100);
				bar.style.width = pct + '%';
				bar.textContent = pct + '%';
				szEl.textContent = fmb(ev.loaded) + ' / ' + fmb(ev.total);
			}
		});

		xhr.addEventListener('readystatechange', function () {
			if (xhr.readyState === 4) {
				bar.style.width = '100%';
				bar.textContent = '100%';
				szEl.textContent = fmb(file.size) + ' / ' + fmb(file.size);
				setTimeout(function () {
					overlay.classList.remove('active');
					// Recargar la página actual (con query params si los hay)
					window.location.href = window.location.href;
				}, 600);
			}
		});

		xhr.addEventListener('error', function () {
			// Si XHR falla, enviar normalmente via iframe como fallback
			overlay
				.querySelector('.progress-bar')
				.classList.add('progress-bar-striped', 'progress-bar-animated');
			bar.textContent = 'Procesando...';

			const originalTarget = form.target;
			form.target = 'uploadFrame';
			iframe.onload = function () {
				form.target = originalTarget || '';
				iframe.onload = null;
				overlay.classList.remove('active');
				window.location.reload();
			};
			form.submit();
		});

		xhr.timeout = 600000;
		xhr.open('POST', action);
		xhr.send(fd);
	});
})();
