/**
 * upload-progress.js — Iframe + progreso simulado basado en tamaño
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

		const totalMB = file.size / 1048576;
		fnEl.textContent = file.name;
		szEl.textContent = '0 MB / ' + fmb(file.size);
		bar.style.width = '0%';
		bar.textContent = '0%';
		overlay.classList.add('active');

		// Enviar al iframe (funciona siempre)
		const originalTarget = form.target;
		form.target = 'uploadFrame';

		// Simular progreso: ~500KB/s estimado para hosting compartido
		const speedBps = 500 * 1024;
		const estimatedSec = file.size / speedBps;
		const startTime = Date.now();
		let done = false;

		const timer = setInterval(function () {
			if (done) {
				return;
			}
			const elapsed = (Date.now() - startTime) / 1000;
			const pct = Math.min(Math.round((elapsed / estimatedSec) * 95), 95);
			const uploaded = Math.min(file.size * (pct / 100), file.size);
			bar.style.width = pct + '%';
			bar.textContent = pct + '%';
			szEl.textContent = fmb(uploaded) + ' / ' + fmb(file.size);
		}, 300);

		iframe.onload = function () {
			done = true;
			clearInterval(timer);
			form.target = originalTarget || '';
			iframe.onload = null;
			bar.style.width = '100%';
			bar.textContent = '100%';
			szEl.textContent = fmb(file.size) + ' / ' + fmb(file.size);
			setTimeout(function () {
				overlay.classList.remove('active');
				window.location.reload();
			}, 500);
		};

		// NO preventDefault — el formulario se envía normalmente al iframe
	});
})();
