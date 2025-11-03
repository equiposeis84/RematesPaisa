/*
Archivo: js/modules/navigation.js
Descripción: Manejo de la navegación interna y switching entre secciones.
Explicación: Contiene funciones para cambiar vistas, actualizar el estado activo del menú y manejar el historial.
Notas: Mantén accesibilidad (focus management) al cambiar secciones.
*/

import { navMenus, getCurrentUserRole, getCurrentPage, getCart, icons, mockUsers, getCurrentUserEmail } from './constants.js';

let activeSubMenu = null;

export function renderNav() {
    const navContainer = document.getElementById('main-nav');
    const sessionContainer = document.getElementById('session-controls');
    if (!navContainer || !sessionContainer) return;
    navContainer.innerHTML = '';
    sessionContainer.innerHTML = '';

    const role = getCurrentUserRole();
    const page = getCurrentPage();
    const menu = navMenus[role] || navMenus['guest'];

    // Separar items principales de los de 'bottom' (ej. login/cerrar sesión, ayuda)
    const bottomItems = menu.filter(i => i.bottom);
    // Evitar duplicados: excluir del topItems cualquier página listada en bottomItems
    const bottomPages = bottomItems.map(i => i.page);
    const topItems = menu.filter(i => !i.bottom && !bottomPages.includes(i.page));

    // Renderizar menú principal (solo topItems)
    topItems.forEach(item => {
        // Si el item es el carrito, calcular contador y renderizar badge (siempre)
        if (item.page === 'cart' && ['guest', 'customer'].includes(role)) {
            const cartItems = getCart();
            const cartItemCount = (cartItems || []).reduce((sum, it) => sum + (it.quantity || 0), 0);
            const cartIndicator = `<span class="badge${cartItemCount === 0 ? ' visually-hidden' : ''}" id="nav-cart-badge" role="status" aria-live="polite" aria-atomic="true" aria-label="Productos en el carrito: ${cartItemCount}">${cartItemCount}</span>`;
            navContainer.insertAdjacentHTML('beforeend', `
                <a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('${item.page}')" class="relative flex items-center gap-3 py-2.5 px-4 mb-2 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors ${page === item.page ? 'bg-custom-nav-active' : ''}">
                    ${icons[item.icon] || ''}
                    ${cartIndicator}
                    <span>${item.label}</span>
                </a>
            `);
        } else {
            // Si el item tiene children, renderizamos como desplegable
            if (item.children && Array.isArray(item.children) && item.children.length) {
                const isOpen = activeSubMenu === item.page;
                navContainer.insertAdjacentHTML('beforeend', `
                    <div class="mb-2">
                        <button onclick="event.preventDefault(); window.toggleSubMenu && window.toggleSubMenu('${item.page}')" class="w-full flex items-center justify-between gap-3 py-2.5 px-4 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors ${isOpen ? 'bg-custom-nav-active' : ''}">
                            <div class="flex items-center gap-3">${icons[item.icon] || ''}<span>${item.label}</span></div>
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        ${isOpen ? `<div class="mt-2 ml-4">` + item.children.map(ch => `
                            <a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('${ch.page}')" class="flex items-center gap-3 py-2 px-3 mb-1 rounded-lg text-sm text-slate-200 hover:bg-slate-700">${icons[ch.icon] || ''}<span>${ch.label}</span></a>
                        `).join('') + `</div>` : ''}
                    </div>
                `);
            } else {
                navContainer.insertAdjacentHTML('beforeend', `
                    <a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('${item.page}')" class="flex items-center gap-3 py-2.5 px-4 mb-2 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors ${page === item.page ? 'bg-custom-nav-active' : ''}">
                        ${icons[item.icon] || ''}
                        <span>${item.label}</span>
                    </a>
                `);
            }
        }
    });

    // Renderizar controles en la parte inferior (bottomItems) y un panel de sesión
    // Usaremos los bottomItems definidos en navMenus para evitar duplicados (ej. Ayuda)
    let sessionHTML = '';

    if (role === 'guest') {
        // Mostrar todos los bottomItems para invitados (help + login u otros)
        bottomItems.forEach(item => {
            if (item.page === 'login') {
                sessionHTML += `<a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('login');" class="flex items-center gap-3 py-2.5 px-4 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors">${icons.login || ''}<span>${item.label}</span></a>`;
            } else {
                sessionHTML += `<a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('${item.page}');" class="flex items-center gap-3 py-2.5 px-4 mb-2 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors ${page === item.page ? 'bg-custom-nav-active' : ''}">${icons[item.icon] || ''}<span>${item.label}</span></a>`;
            }
        });
        } else {
            // Prefer showing the exact logged-in user by email when available
        const currentEmail = getCurrentUserEmail();
        let displayName = 'Usuario';
        if (currentEmail && mockUsers[currentEmail]) {
            displayName = mockUsers[currentEmail].name;
        } else {
            const userData = Object.values(mockUsers).find(u => u.role === role);
            displayName = userData ? userData.name : displayName;
        }

        sessionHTML = `
            <div class="mb-2 p-3 bg-slate-600 rounded-lg">
                <p class="text-xs text-slate-300">Conectado como:</p>
                <p class="font-semibold">${displayName}</p>
            </div>
        ` + sessionHTML;

        // Añadir bottom items (Cerrar sesión u otros)
        bottomItems.forEach(item => {
            if (item.page === 'help') {
                // single help link
                sessionHTML += `<a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('help')" class="flex items-center gap-3 py-2.5 px-4 mb-2 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors ${page === 'help' ? 'bg-custom-nav-active' : ''}">${icons.help || ''}<span>${item.label}</span></a>`;
            } else if (item.page === 'login') {
                // use logout helper when named 'Cerrar Sesión'
                sessionHTML += `<a href="#" onclick="event.preventDefault(); window.logout && window.logout();" class="flex items-center gap-3 py-2.5 px-4 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors">${icons.login || ''}<span>${item.label}</span></a>`;
            } else {
                sessionHTML += `<a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('${item.page}');" class="flex items-center gap-3 py-2.5 px-4 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors">${icons[item.icon] || ''}<span>${item.label}</span></a>`;
            }
        });
    }

    sessionContainer.innerHTML = sessionHTML;

    // Asegurar que el badge (si existe) siempre refleje el estado actual del carrito
    try {
        const badge = document.getElementById('nav-cart-badge');
        if (badge) {
            const cartItems = getCart();
            const cartItemCount = (cartItems || []).reduce((sum, it) => sum + (it.quantity || 0), 0);
            badge.textContent = cartItemCount;
            badge.setAttribute('aria-label', `Productos en el carrito: ${cartItemCount}`);
            if (cartItemCount === 0) {
                badge.classList.add('visually-hidden');
            } else {
                badge.classList.remove('visually-hidden');
            }
        }
    } catch (e) {
        // noop
    }
}

export function toggleSubMenu(menuId) {
    if (activeSubMenu === menuId) {
        activeSubMenu = null;
    } else {
        activeSubMenu = menuId;
    }
    renderNav();
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}