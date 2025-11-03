/*
Archivo: js/main.js
Descripción: Script de arranque que puede contener inicializaciones rápidas para la página.
Explicación: Suele enlazar elementos del DOM con listeners y lanzar la función de inicio.
Importante: No incluir lógica pesada aquí; usar módulos importados cuando sea posible.
*/

import { renderNav, toggleSubMenu as navToggleSubMenu } from './modules/navigation.js';
import { renderHomePage } from './modules/home.js';
import { renderCatalogPage } from './modules/catalog.js';
import { openProductModal, closeModal, openEditModal, openSettingsModal, openDetailsModal } from './modules/modals.js';
import { renderCartPage, addToCart, updateCartQuantity, removeFromCart, completeCheckout } from './modules/cart.js';
import { renderOrdersPage, renderAdminOrdersPage, showOrderDetails, approveValidation, rejectValidation } from './modules/orders.js';
import { processAdminRequests } from './modules/orders.js';
import { renderProfilePage, renderClientsPage } from './modules/users.js';
import { renderAdminDeliveryMenPage } from './modules/delivery.js';
import { renderInventoryPage } from './modules/inventory.js';
import { renderUsersPage } from './modules/users.js';
import { renderDeliveryTasksPage as renderDeliveryPage } from './modules/delivery.js';
import { renderProvidersPage } from './modules/providers.js';
import { renderReportsPage } from './modules/reports.js';
import { renderHelpPage } from './modules/help.js';
import { renderLoginPage, renderRegisterPage, renderPasswordRecovery, logout } from './modules/auth.js';
import apiAdapter from './modules/api.js';
import { getCurrentPage, setCurrentPage, whatsappNumber, getWhatsAppUrl, setCurrentUserRole, setCurrentUserEmail, setNextPage, saveProvidersSafe, mockProviders, mockDeliveryMen } from './modules/constants.js';
import { showAlert as toastAlert } from './modules/utils.js';

// Exponer funciones que el HTML usa
window.toggleSubMenu = navToggleSubMenu;
window.renderNav = renderNav;
window.addToCart = addToCart;
window.updateCartQuantity = updateCartQuantity;
window.removeFromCart = removeFromCart;
window.showAlert = toastAlert;
window.completeCheckout = completeCheckout;
window.openProductModal = openProductModal;
window.closeModal = closeModal;
window.openEditModal = openEditModal;
window.openSettingsModal = openSettingsModal;
window.openDetailsModal = openDetailsModal;
window.approveValidation = approveValidation;
window.rejectValidation = rejectValidation;
window.logout = logout;
window.processAdminRequests = processAdminRequests;
// expose delivery save helper
import { saveDeliveryMenSafe, saveDeliveryMenToStorage } from './modules/constants.js';
window.saveDeliveryMenSafe = saveDeliveryMenSafe;
window.saveDeliveryMenToStorage = saveDeliveryMenToStorage;
// expose provider save helper
window.saveProvidersSafe = saveProvidersSafe;
// expose order details and renderers explicitly so inline handlers can call them
window.showOrderDetails = showOrderDetails;
window.renderAdminDeliveryMenPage = renderAdminDeliveryMenPage;
window.renderProvidersPage = renderProvidersPage;

// --- Lightweight wrappers to ensure modal focus and compatibility for legacy inline handlers ---
;(function attachWrappers(){
    try {
        if (window.openEditModal && typeof window.openEditModal === 'function') {
            const real = window.openEditModal;
            window.openEditModal = function(type, item){ const r = real(type, item); try { if (window.focusModalFirstInput) window.focusModalFirstInput(); } catch(e){}; return r; };
        }
        if (window.openDetailsModal && typeof window.openDetailsModal === 'function') {
            const realD = window.openDetailsModal;
            window.openDetailsModal = function(type, item){ return realD(type, item); };
        }
        if (window.showOrderDetails && typeof window.showOrderDetails === 'function') {
            const realS = window.showOrderDetails;
            window.showOrderDetails = function(id){ return realS(id); };
        }
        if (window.renderProvidersPage && typeof window.renderProvidersPage === 'function') {
            const rp = window.renderProvidersPage;
            window.renderProvidersPage = function(){ return rp(); };
        }
        if (window.renderAdminDeliveryMenPage && typeof window.renderAdminDeliveryMenPage === 'function') {
            const rd = window.renderAdminDeliveryMenPage;
            window.renderAdminDeliveryMenPage = function(){ return rd(); };
        }
    } catch (e) { /* noop */ }
})();

// (debug click logger removed to reduce console noise)


export function navigateTo(page) {
    // Oculta todas las páginas
    // map special pages to physical containers
    const displayPage = page === 'password-recovery' ? 'login' : page;
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    const target = document.getElementById(`page-${displayPage}`);
    if (target) target.classList.add('active');

    // Actualiza estado
    setCurrentPage(page);

    // Renderiza contenido
    switch (page) {
        case 'home': renderHomePage(); break;
        case 'catalog': renderCatalogPage(); break;
        case 'cart': renderCartPage(); break;
    case 'orders': renderOrdersPage(); break;
    case 'profile': renderProfilePage && renderProfilePage(); break;
    case 'clients': renderClientsPage && renderClientsPage(); break;
    case 'repartidores': renderAdminDeliveryMenPage && renderAdminDeliveryMenPage(); break;
    case 'providers': renderProvidersPage && renderProvidersPage(); break;
        case 'inventory': renderInventoryPage(); break;
        case 'users': renderUsersPage(); break;
        case 'delivery': renderDeliveryPage(); break;
        case 'reports': renderReportsPage(); break;
    case 'admin-orders': renderAdminOrdersPage && renderAdminOrdersPage(); break;
    case 'help': renderHelpPage(); break;
    case 'login': renderLoginPage(); break;
    case 'register': renderRegisterPage(); break;
    case 'password-recovery': renderPasswordRecovery(); break;
        default: renderHomePage();
    }
    // re-render nav
    renderNav();

    // Mostrar u ocultar botón de WhatsApp según la página
    try {
        if (page === 'help') {
            createWhatsAppFab();
        } else {
            removeWhatsAppFab();
        }
    } catch (e) {
        console.error('whatsapp fab toggle error', e);
    }
}

window.navigateTo = navigateTo;

// expose API adapter for safe save wrappers
window.api = apiAdapter;

// protect navigation: if user uses back/forward, request validation (simple re-login) to be safe
window.addEventListener('popstate', (e) => {
    // simple security measure: if a user attempts history navigation, force a quick validation
    try {
        const needValidation = true;
        if (needValidation) {
            const page = getCurrentPage && getCurrentPage();
            if (page && setNextPage) setNextPage(page);
            window.showAlert && window.showAlert('Por seguridad debes validar tu sesión', 'info');
            if (window.openRevalidateModal) {
                // open revalidation modal and pass intended page
                window.openRevalidateModal(page);
            } else {
                window.navigateTo && window.navigateTo('login');
            }
        }
    } catch (err) { console.error('popstate validation error', err); }
});

// Simple helper to simulate or integrate Google Sign-In
window.googleSignIn = async function mockGoogleSignIn() {
    // In a production flow you'd use Google's Identity Services.
    // Here we simulate a popup where the user picks an email.
    const email = prompt('Simular Google Sign-In: ingresa tu correo (ej: juan@example.com)');
    if (!email) return null;
    // Return a minimal profile object
    return { email, name: email.split('@')[0].replace('.', ' ').toUpperCase() };
};

document.addEventListener('DOMContentLoaded', () => {
    renderNav();
    const page = getCurrentPage() || 'home';
    navigateTo(page);
    // start automatic processing of admin requests in background (simulated)
    try {
        setInterval(() => {
            if (window.processAdminRequests) window.processAdminRequests();
        }, 4000);
    } catch (e) { console.error('auto admin proc error', e); }
});

// Delegated click handler to ensure dynamically created buttons open the correct modals
document.addEventListener('click', function delegatedHandler(e) {
    try {
        const el = e.target.closest && e.target.closest('[data-order-id], .btn-view-order, .view-provider, .view-delivery-details, [data-provider-id], [data-id]');
        if (!el) return;
        // order detail
        const orderId = el.getAttribute('data-order-id') || el.dataset.orderId;
        if (orderId && window.showOrderDetails) { e.preventDefault(); return window.showOrderDetails(orderId); }

        // provider view/edit
        if (el.classList && el.classList.contains('view-provider')) {
            const pid = el.getAttribute('data-id') || el.dataset.id || el.getAttribute('data-provider-id');
            const p = (mockProviders && mockProviders.find && mockProviders.find(x => x.id === pid)) || { id: pid };
            if (window.openDetailsModal) { e.preventDefault(); return window.openDetailsModal('provider', p); }
        }

        // delivery details
        if (el.classList && el.classList.contains('view-delivery-details')) {
            const did = el.getAttribute('data-id') || el.dataset.id;
            const d = (mockDeliveryMen && mockDeliveryMen.find && mockDeliveryMen.find(x => x.id === did)) || { id: did };
            if (window.openDetailsModal) { e.preventDefault(); return window.openDetailsModal('delivery', d); }
        }
    } catch (err) { /* noop */ }
}, true);

// ensure help button state is correct on first load

// Helper: crear/eliminar FAB de WhatsApp
function createWhatsAppFab() {
    const existing = document.getElementById('whatsapp-fab');
    if (existing) return;
    const a = document.createElement('a');
    a.id = 'whatsapp-fab';
    a.className = 'whatsapp-fab';
    a.href = getWhatsAppUrl(whatsappNumber, 'Hola, necesito ayuda con mi pedido');
    a.target = '_blank';
    a.rel = 'noopener noreferrer';
    a.title = 'Chatear por WhatsApp';
    a.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 21l1.5-5.5A9 9 0 1118.5 19.5L13 21l-1-4-4 1z" fill="rgba(0,0,0,0.06)"/><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.672.15-.198.297-.768.967-.94 1.165-.173.198-.347.223-.644.075-.297-.149-1.255-.462-2.39-1.475-.884-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.52-.075-.149-.672-1.62-.92-2.219-.242-.583-.487-.503-.672-.513l-.572-.01c-.198 0-.52.074-.793.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.077 4.487 0 0 .001.001.002.001.298.13.53.206.712.265.299.09.57.077.786.047.24-.032.758-.31.866-.61.109-.298.109-.553.076-.606-.033-.053-.12-.09-.297-.149z" fill="#fff"/></svg>
    `;
    document.body.appendChild(a);
}

function removeWhatsAppFab() {
    const el = document.getElementById('whatsapp-fab');
    if (el) el.remove();
}