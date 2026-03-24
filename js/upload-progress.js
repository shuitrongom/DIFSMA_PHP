/**
 * upload-progress.js — Barra de progreso con % y MB para subida de archivos
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

		const fd = new FormData(form);
		const xhr = new XMLHttpRequest();

		xhr.upload.addEventListener('progress', function (ev) {
			if (ev.lengthComputable) {
				const pct = Math.round((ev.loaded / ev.total) * 100);
				bar.style.width = pct + '%';
				bar.textContent = pct + '%';
				szEl.textContent = fmb(ev.loaded) + ' / ' + fmb(ev.total);
			}
		});

		xhr.addEventListener('load', function () {
			bar.style.width = '100%';
			bar.textContent = '100%';
			szEl.textContent = fmb(file.size) + ' / ' + fmb(file.size);
			setTimeout(function () {
				overlay.classList.remove('active');
				window.location.reload();
			}, 500);
		});

		xhr.addEventListener('error', function () {
			overlay.classList.remove('active');
			alert('Error al subir el archivo. Intente de nuevo.');
		});

		xhr.addEventListener('timeout', function () {
			overlay.classList.remove('active');
			alert(
				'La subida tardó demasiado. Intente con un archivo más pequeño.'
			);
		});

		xhr.timeout = 600000;
		xhr.open('POST', form.action || window.location.href);
		xhr.send(fd);
	});
})();
