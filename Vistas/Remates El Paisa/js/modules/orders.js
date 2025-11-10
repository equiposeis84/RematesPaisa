/*
Archivo: js/modules/orders.js
Descripción: Lógica y render para la sección de pedidos (listar, filtrar, ver detalles).
Explicación: Contiene funciones para renderizar la lista de pedidos, mostrar detalles y cambiar estados.
Importante: Cuando implementes integración con backend, mantener los puntos que llaman a `api.js` para persistencia.
*/

/**
 * orders.js
 *
 * Descripción:
 * Maneja la representación y operaciones relacionadas con pedidos.
 * Exporta funciones para renderizar la página de pedidos, procesar
 * solicitudes administrativas y mostrar detalles de un pedido.
 *
 * Notas:
 * - Actualmente opera sobre `mockOrders` (datos de ejemplo) y guarda
 *   cambios en localStorage cuando es posible.
 */
// --- FUNCIONES DE PEDIDOS ---
import { getCurrentUserRole, mockOrders, setNextPage, getCurrentUserEmail, pendingValidations, pendingAdminRequests, mockCustomers, mockUsers, saveOrdersToStorage, icons, mockDeliveryMen, normalizeImagePath } from './constants.js';
import { showAlert } from './utils.js';

export function renderOrdersPage() {
    const container = document.getElementById('page-orders');
    if (!container) return;
    const role = getCurrentUserRole();
    // allow customers and admins to view orders page (admin has own admin-orders page but should also be able to view)
    if (!['customer','admin'].includes(role)) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <h2 class="text-3xl font-bold mb-4">Acceso Restringido</h2>
                <p class="mb-4">Debes iniciar sesión como cliente para ver tus pedidos.</p>
                <button id="orders-login-cta" class="bg-blue-500 text-white px-6 py-2 rounded">Ir a Iniciar Sesión</button>
            </div>
        `;
        const cta = document.getElementById('orders-login-cta');
        if (cta) cta.onclick = (e) => { e.preventDefault(); setNextPage('orders'); window.navigateTo && window.navigateTo('login'); };
        return;
    }
    container.innerHTML = `
        <div class="page active animate-fade-in">
            <h2 class="text-2xl font-bold mb-4">Mis Pedidos</h2>
            <div id="orders-list"></div>
        </div>
    `;
    const ordersList = document.getElementById('orders-list');
    if (ordersList) {
        const userEmail = getCurrentUserEmail();
        const userOrders = mockOrders.filter(o => o.customerEmail === userEmail || o.customer === userEmail || !userEmail);
        // Also include pending validations submitted by this user
        const pendingByUser = pendingValidations.filter(v => v.email === userEmail);

    const ordersHtml = [];
        pendingByUser.forEach(v => {
            ordersHtml.push(`
                <div class="bg-yellow-50 rounded-lg p-4 mb-3 animate-fade-in">
                    <div class="font-semibold">Solicitud: ${v.id}</div>
                    <div class="text-sm">Fecha: ${new Date(v.submittedAt).toLocaleDateString()}</div>
                    <div class="text-sm">Estado: <span class="font-medium">${v.status || 'Pendiente de validación'}</span></div>
                </div>
            `);
        });

        // if user has no orders, inject a demo order to avoid empty view
        if (userOrders.length === 0) {
            const demo = {
                id: 'ORD-DEMO-001', date: new Date().toISOString().split('T')[0], total: 29500, status: 'Procesando', items: [ { name: 'Limpiador Multiusos Floral', qty: 2, price: 12500 } ]
            };
            userOrders.push(demo);
            // also add demo to global orders store so detail modal can open it
            try {
                if (!mockOrders.find(o => o.id === demo.id)) {
                    mockOrders.unshift(Object.assign({}, demo));
                    try { if (typeof saveOrdersToStorage === 'function') saveOrdersToStorage(mockOrders); } catch (e) { /* noop */ }
                }
            } catch (e) { /* noop */ }
        }

        ordersHtml.push(...userOrders.map(order => `
            <div class="bg-white rounded-lg shadow p-4 mb-4 animate-fade-in flex items-center justify-between">
                <div>
                    <div class="font-semibold">ID: ${order.id}</div>
                    <div class="text-sm">Fecha: ${order.date}</div>
                    <div class="text-sm">Total: ${order.total}</div>
                    <div class="text-sm">Estado: <span class="font-medium">${order.status}</span></div>
                </div>
                <div>
                    <button data-order-id="${order.id}" class="text-custom-blue btn-view-order">Ver detalles</button>
                </div>
            </div>
        `));

        ordersList.innerHTML = ordersHtml.join('') || '<div class="text-slate-600">No tienes pedidos aún.</div>';
    }
}

// Procesador simulado de peticiones administrativas (aplica deshabilitar/habilitar/eliminar)
export function processAdminRequests(auto = true) {
    if (!pendingAdminRequests || pendingAdminRequests.length === 0) return;
    // procesar copia para evitar modificaciones durante la iteración
    const toProcess = pendingAdminRequests.slice();
    toProcess.forEach((req, idx) => {
        // simular revisión con retraso corto
        setTimeout(() => {
            try {
                if (req.type === 'disable_account') {
                    const cust = mockCustomers.find(c => c.email === req.email);
                    if (cust) cust.disabled = true;
                    if (mockUsers[req.email]) mockUsers[req.email].disabled = true;
                    showAlert('Solicitud administrativa procesada: cuenta deshabilitada', 'info');
                } else if (req.type === 'enable_account') {
                    const cust = mockCustomers.find(c => c.email === req.email);
                    if (cust) cust.disabled = false;
                    if (mockUsers[req.email]) mockUsers[req.email].disabled = false;
                    showAlert('Cuenta re-habilitada por proceso administrativo', 'success');
                } else if (req.type === 'delete_account') {
                    // eliminar customer y usuario
                    const ci = mockCustomers.findIndex(c => c.email === req.email);
                    if (ci !== -1) mockCustomers.splice(ci, 1);
                    if (mockUsers[req.email]) delete mockUsers[req.email];
                    showAlert('Cuenta eliminada por proceso administrativo', 'error');
                }
            } catch (err) {
                console.error('Error procesando petición administrativa', err);
            }
            // remover la petición procesada
            const pidx = pendingAdminRequests.findIndex(r => r.id === req.id);
            if (pidx !== -1) pendingAdminRequests.splice(pidx, 1);
            // refrescar UI
            if (window.renderAdminOrdersPage) window.renderAdminOrdersPage();
            if (window.renderProfilePage) window.renderProfilePage();
        }, 1200 + idx * 300);
    });
}

export function showOrderDetails(orderId) {
    const order = mockOrders.find(o => o.id === orderId);
    if (!order) return;
    const modal = document.getElementById('order-modal');
    if (!modal) return;
    modal.classList.add('active');
    const customer = mockCustomers.find(c => c.email === order.customerEmail) || { name: order.customer, email: order.customerEmail, phone: order.phone, address: order.address };
        const whatsappNote = (customer.lastWhatsAppMessage ? `<div class="text-sm text-green-700">${icons.whats} Último WhatsApp: ${customer.lastWhatsAppMessage.message} <span class="text-xs text-slate-500">(${new Date(customer.lastWhatsAppMessage.at).toLocaleString()})</span></div>` : '');
    const contentEl = modal.querySelector('#order-modal-content') || modal.querySelector('.bg-white') || modal;
    contentEl.innerHTML = `\
        <div class="p-4">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold">Detalles del Pedido #${order.id}</h3>
                <div class="text-sm text-slate-500">${order.date}</div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-slate-50 p-3 rounded">
                    <h4 class="font-semibold">Cliente</h4>
                    <div>${customer.name}</div>
                    <div class="text-sm text-slate-500">${customer.email} • ${customer.phone || 'Sin teléfono'}</div>
                    <div class="text-sm text-slate-500">Dirección: ${order.address || customer.address || 'No registrada'}</div>
                    ${whatsappNote}
                </div>
                <div class="bg-slate-50 p-3 rounded">
                    <h4 class="font-semibold">Estado</h4>
                    <div class="mt-2"><span class="px-2 py-1 rounded ${order.status === 'Procesando' ? 'bg-yellow-100 text-yellow-800' : order.status === 'Listo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">${order.status}</span></div>
                    <div class="mt-3"><strong>Total:</strong> ${order.total}</div>
                    <div class="mt-2 text-sm">Fecha pedido: ${order.date || ''}</div>
                    <div class="mt-2 text-sm">Última actualización: ${order.updatedAt || order.date || ''}</div>
                </div>
            </div>
                <div class="mb-4">
                <h4 class="font-semibold mb-2">Items</h4>
                <div class="bg-white rounded shadow overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-100 text-sm text-slate-600"><tr><th class="p-3">Producto</th><th class="p-3">Cantidad</th><th class="p-3">Precio</th></tr></thead>
                        <tbody>
                            ${order.items.map(it => `<tr class="border-b"><td class="p-3 flex items-center gap-3"><img src="${normalizeImagePath(it.image || 'img/placeholder.svg')}" alt="${it.name}" class="w-12 h-12 object-cover rounded"> <span>${it.name}</span></td><td class="p-3">${it.quantity || it.qty || 1}</td><td class="p-3">${it.price}</td></tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Repartidor asignado</h4>
                    <div class="bg-white rounded p-3">
                        ${getCurrentUserRole() === 'admin' ? `
                            <select id="assign-delivery-select" class="w-full p-2 border rounded">
                                <option value="">-- Seleccionar repartidor --</option>
                                ${mockDeliveryMen.map(dm => `<option value="${dm.id}" ${dm.name === order.deliveryMan || dm.id === order.deliveryMan ? 'selected' : ''}>${dm.name} (${dm.vehicle})</option>`).join('')}
                            </select>
                            <div class="mt-2 text-sm text-slate-500">Tel: ${order.deliveryPhone || ''}</div>
                        ` : `<div>${order.deliveryMan || 'No asignado'}</div>`}
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <!-- Status controls removed from action area. Status is shown clearly above in the body. -->
                    ${getCurrentUserRole() === 'admin' ? `<button onclick="event.preventDefault(); closeModal('order-modal'); if(window.openEditModal) { window.openEditModal('order', Object.assign({}, order)); window.focusModalFirstInput && window.focusModalFirstInput(); }" class="bg-custom-blue text-white px-3 py-2 rounded">Editar</button>` : ''}
                    <button onclick="event.preventDefault(); closeModal('order-modal')" class="px-3 py-2 bg-gray-200 rounded">Cerrar</button>
                </div>
        </div>
    `;
    // attach assign delivery handler for admin
    setTimeout(() => {
        const sel = document.getElementById('assign-delivery-select');
        if (sel && typeof window.assignOrderToDelivery === 'function') {
            sel.addEventListener('change', (e) => {
                const val = sel.value;
                if (!val) return;
                const ok = window.assignOrderToDelivery && window.assignOrderToDelivery(order.id, val);
                if (ok) showAlert('Repartidor asignado y pedido marcado como En Camino', 'success');
            });
        }
    }, 40);
}

export function renderAdminOrdersPage() {
    const container = document.getElementById('page-admin-orders');
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'admin') {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <h2 class="text-3xl font-bold mb-4">Acceso Restringido</h2>
                <p class="mb-4">Debes iniciar sesión como administrador para ver esta página.</p>
                <button onclick="event.preventDefault(); window.navigateTo && window.navigateTo('login')" class="bg-blue-500 text-white px-6 py-2 rounded">Ir a Iniciar Sesión</button>
            </div>
        `;
        return;
    }
    container.innerHTML = `
        <div class="page active animate-fade-in">
            <h2 class="text-2xl font-bold mb-4">Gestión de Pedidos</h2>
            <div id="admin-orders-list"></div>
        </div>
    `;
    const adminOrdersList = document.getElementById('admin-orders-list');
    if (adminOrdersList) {
        adminOrdersList.innerHTML = `
            <h3 class="font-semibold mb-3">Pedidos existentes</h3>
            ${mockOrders.map(order => {
        const customer = mockCustomers.find(c => c.email === order.customerEmail) || { name: order.customer, email: order.customerEmail };
        const hasWhats = customer.lastWhatsAppMessage ? `${icons.whats}` : '';
        const testBadge = order.test ? ' <span class="text-xs px-2 py-1 bg-slate-100 text-slate-600 rounded">PRUEBA</span>' : '';
                return `
                    <div class="bg-white rounded-lg shadow p-4 mb-4 animate-fade-in">
            <div><strong>ID:</strong> ${order.id} ${testBadge} <span class="text-sm text-slate-500">(${order.date})</span></div>
            <div><strong>Cliente:</strong> ${customer.name} ${hasWhats}</div>
            <div><strong>Estado:</strong> ${order.status}</div>
            <div><strong>Total:</strong> ${order.total}</div>
                        <div class="mt-2">
                            <button data-order-id="${order.id}" class="bg-blue-500 text-white px-4 py-2 rounded btn-view-order">Ver Detalles</button>
                        </div>
                    </div>
                `;
            }).join('')}
            <h3 class="font-semibold mt-6 mb-3">Validaciones pendientes</h3>
            ${pendingValidations.map(v => `
                <div class="bg-yellow-50 rounded-lg p-4 mb-3">
                    <div><strong>ID:</strong> ${v.id}</div>
                    <div><strong>Email:</strong> ${v.email}</div>
                    <div><strong>Estado:</strong> ${v.status}</div>
                    <div class="mt-2 space-x-2">
                        <button onclick="event.preventDefault(); window.approveValidation && window.approveValidation('${v.id}')" class="bg-green-500 text-white px-3 py-1 rounded">Aprobar</button>
                        <button onclick="event.preventDefault(); window.rejectValidation && window.rejectValidation('${v.id}')" class="bg-red-500 text-white px-3 py-1 rounded">Rechazar</button>
                    </div>
                </div>
            `).join('')}
        `;
    }

    // expose changeOrderStatus globally for modal buttons
    window.changeOrderStatus = function(orderId, newStatus) {
        const order = mockOrders.find(o => o.id === orderId);
        if (!order) return;
        order.status = newStatus;
        // try to persist orders if helper available
        try {
            if (typeof saveOrdersToStorage === 'function') saveOrdersToStorage(mockOrders);
        } catch (e) {
            // noop
        }
        // re-render admin list and any open modal
        if (window.renderAdminOrdersPage) window.renderAdminOrdersPage();
        const modal = document.getElementById('order-modal');
        if (modal && modal.classList.contains('active')) {
            window.showOrderDetails && window.showOrderDetails(orderId);
        }
    };

    // attach click handlers to the dynamically created "Ver detalles" buttons
    setTimeout(() => {
        document.querySelectorAll('.btn-view-order').forEach(btn => btn.addEventListener('click', (ev) => {
            ev.preventDefault();
            const id = btn.getAttribute('data-order-id');
            if (!id) return;
            if (window.showOrderDetails) window.showOrderDetails(id);
        }));
    }, 30);
}

// expose functions globally for templates
window.showOrderDetails = window.showOrderDetails || showOrderDetails;
window.renderAdminOrdersPage = window.renderAdminOrdersPage || renderAdminOrdersPage;

export function approveValidation(validationId) {
    const idx = pendingValidations.findIndex(v => v.id === validationId);
    if (idx === -1) return;
    const v = pendingValidations.splice(idx, 1)[0];
    // Crear pedido final
    const newOrderId = 'ORD-' + (mockOrders.length + 1).toString().padStart(3, '0');
    const newOrder = {
        id: newOrderId,
        customer: v.email,
        customerEmail: v.email,
        date: new Date().toISOString().split('T')[0],
        total: v.subtotal + v.shipping,
        status: 'Aprobado',
        items: v.items,
        ticket: null
    };
    mockOrders.unshift(newOrder);
    // re-render admin page
    if (window.renderAdminOrdersPage) window.renderAdminOrdersPage();
}

export function rejectValidation(validationId) {
    const idx = pendingValidations.findIndex(v => v.id === validationId);
    if (idx === -1) return;
    pendingValidations.splice(idx, 1);
    if (window.renderAdminOrdersPage) window.renderAdminOrdersPage();
}