/**
 * admin-tooltips.js — Asigna tooltips automáticos a los botones de acción del admin
 * Se ejecuta una vez que el DOM está listo.
 */
document.addEventListener('DOMContentLoaded', function () {
    var tooltips = {
        'btn-action-edit':       'Editar registro',
        'btn-action-key':        'Cambiar / Subir archivo',
        'btn-action-pause':      'Desactivar',
        'btn-action-play':       'Activar',
        'btn-action-delete':     'Eliminar registro',
        'btn-action-pdf-delete': 'Eliminar PDF'
    };

    Object.keys(tooltips).forEach(function (cls) {
        document.querySelectorAll('.' + cls).forEach(function (btn) {
            // Solo asignar si no tiene ya un data-tooltip personalizado
            if (!btn.getAttribute('data-tooltip')) {
                // Usar el title existente si lo tiene, si no el genérico
                var label = btn.getAttribute('title') || tooltips[cls];
                btn.setAttribute('data-tooltip', label);
                btn.removeAttribute('title'); // evitar doble tooltip nativo
            }
        });
    });
});
