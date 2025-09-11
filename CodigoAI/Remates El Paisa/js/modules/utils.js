/**
 * utils.js
 *
 * Descripción:
 * Funciones utilitarias reutilizables usadas en la aplicación.
 * Incluye formateo de moneda, creación de toasts/avisos, animaciones y
 * otras ayudas pequeñas para la UI.
 *
 * Todas las funciones están documentadas en español.
 */

/*
Archivo: js/modules/utils.js
Descripción: Utilidades para UI y formateo (toasts, formato moneda, animaciones simples).
Explicación: Contiene funciones reutilizables como `formatCurrency`, `showAlert`, `showUndoToast`, `animateBadge`, `closeModal`.
Notas: Mantener separados los helpers de DOM y la lógica de negocio para pruebas.
*/

// --- FUNCIONES DE UTILIDAD ---
/**
 * formatea un número a moneda local (COP) con símbolo.
 * @param {number|string} value - valor numérico a formatear
 * @returns {string} cadena formateada, por ejemplo "$12.500"
 */
export function formatCurrency(value) {
    return '$' + Number(value).toLocaleString('es-CO');
}

/**
 * Muestra una alerta/toast simple en pantalla.
 * @param {string} message - Mensaje a mostrar.
 * @param {'info'|'success'|'error'} [type='info'] - Tipo visual de la alerta.
 */
export function showAlert(message, type = 'info') {
    // Sistema de toast minimal: crea un contenedor si no existe
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
    // asegurar que está por encima de otros elementos
    container.style.zIndex = '99999';
    document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    // icon per type
    const icon = type === 'success' ? '✓' : type === 'error' ? '!' : 'i';
    toast.innerHTML = `<div class="toast-icon">${icon}</div><div class="toast-inner">${message}</div>`;
    container.appendChild(toast);

    // force reflow to allow animation
    void toast.offsetWidth;
    toast.classList.add('toast-show');

    // avoid verbose logs in production; keep silent by default

    // auto dismiss
    setTimeout(() => {
        toast.classList.remove('toast-show');
        toast.classList.add('toast-hide');
        setTimeout(() => toast.remove(), 300);
    }, 2600);
}

/**
 * Muestra un toast con acción de 'Deshacer'.
 * @param {string} message - Mensaje del toast.
 * @param {Function} undoCallback - Función llamada si el usuario presiona 'Deshacer'.
 * @param {number} [timeout=5000] - Tiempo en ms antes de que el toast desaparezca.
 */
export function showUndoToast(message, undoCallback, timeout = 5000) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
    // position fixed at bottom-right so action toasts are visible and clickable
    container.style.position = 'fixed';
    container.style.right = '20px';
    container.style.bottom = '20px';
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.alignItems = 'flex-end';
    container.style.gap = '8px';
    container.style.zIndex = '99999';
    document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast toast-action';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.justifyContent = 'space-between';
    toast.style.gap = '12px';
    toast.style.padding = '10px 14px';
    toast.style.background = '#ffffff';
    toast.style.border = '1px solid rgba(0,0,0,0.06)';
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 6px 18px rgba(0,0,0,0.08)';
    toast.style.marginBottom = '8px';

    const msg = document.createElement('div');
    msg.textContent = message;
    msg.style.color = '#0f172a';
    msg.style.flex = '1';
    msg.style.fontSize = '14px';

    const undoBtn = document.createElement('button');
    undoBtn.textContent = 'Deshacer';
    undoBtn.style.background = 'transparent';
    undoBtn.style.border = 'none';
    undoBtn.style.color = '#2563eb';
    undoBtn.style.fontWeight = '600';
    undoBtn.style.cursor = 'pointer';

    let finalized = false;
    const timer = setTimeout(() => {
        if (!finalized) {
            toast.remove();
            finalized = true;
        }
    }, timeout);

    undoBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (finalized) return;
        clearTimeout(timer);
        try { if (typeof undoCallback === 'function') undoCallback(); } catch (err) { console.error(err); }
        toast.remove();
        finalized = true;
    });

    toast.appendChild(msg);
    toast.appendChild(undoBtn);
    container.appendChild(toast);
}

/**
 * Animación de pulso para la badge del carrito.
 */
export function animateBadge() {
    try {
        const badge = document.getElementById('nav-cart-badge');
        if (!badge) return;
        badge.classList.remove('badge-pulse');
        // trigger reflow
        void badge.offsetWidth;
        badge.classList.add('badge-pulse');
    } catch (e) {
        // noop
    }
}

/**
 * Cierra un modal dado su id.
 * @param {string} modalId - Id del elemento modal a cerrar.
 */
export function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
}

// ...otras utilidades...