/*
Archivo: js/modules/delivery.js
Descripción: Lógica relacionada con repartidores y entregas.
Explicación: Maneja asignación de repartidores, estados de entrega y métricas.
Importante: Incluir validaciones para estados y registro de timestamps para auditoría.
*/

// --- FUNCIONES DE REPARTIDORES ---
import { getCurrentUserRole, mockDeliveryMen, mockOrders, mockCustomers, saveOrdersToStorage, gearIcon, saveDeliveryMenSafe, saveDeliveryMenToStorage, getWhatsAppUrl, icons, deliveryTasks, deliveryHistory } from './constants.js';
import { formatCurrency, showAlert, showUndoToast } from './utils.js';

export function renderAdminDeliveryMenPage() {
    // choose container: prefer admin container for admins, otherwise use public `page-repartidores` when available
    const adminContainer = document.getElementById('page-admin-delivery-men');
    const publicContainer = document.getElementById('page-repartidores');
    const role = getCurrentUserRole();
    // LOG de diagnóstico para confirmar ejecución
    try { console.log('[DEBUG] renderAdminDeliveryMenPage called, role=', role, 'adminContainerPresent=', !!adminContainer, 'publicContainerPresent=', !!publicContainer); } catch (e) {}
    let container = null;
    if (role === 'admin' && adminContainer) container = adminContainer;
    else container = publicContainer || adminContainer;

    // if using admin container, enforce admin role
    const isAdminContainer = container === adminContainer;
    // If we're rendering the admin view, ensure the admin page container is the one shown.
    // navigateTo() may have activated the public container (`page-repartidores`) so make
    // the admin container visible and hide the public one to avoid rendering into a hidden node.
    try {
        if (isAdminContainer && adminContainer) {
            // hide the public repartidores container if present
            if (publicContainer && publicContainer.classList) publicContainer.classList.remove('active');
            // show the admin container
            if (adminContainer.classList && !adminContainer.classList.contains('active')) adminContainer.classList.add('active');
        }
    } catch (e) { /* noop */ }
    if (isAdminContainer && role !== 'admin') {
        container.innerHTML = `
            <div class="text-center mt-10">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Acceso Restringido</h2>
                <p class="text-slate-600 mb-6">Solo los administradores pueden acceder a esta sección.</p>
            </div>
        `;
        return;
    }
    
    // ensure sample data exists so the page never appears empty
    if (!Array.isArray(mockDeliveryMen) || mockDeliveryMen.length === 0) {
        const demos = [
            { id: 'D001', name: 'Juan Pérez', email: 'repartidor@remates.com', phone: '3007654321', vehicle: 'Moto', licensePlate: 'ABC123', status: 'Activo' }
        ];
        try {
            mockDeliveryMen.splice(0, mockDeliveryMen.length, ...demos);
        } catch (e) {
            try { window.mockDeliveryMen = demos; } catch (err) { /* noop */ }
        }
        try { if (typeof saveDeliveryMenSafe === 'function') saveDeliveryMenSafe(); else if (typeof saveDeliveryMenToStorage === 'function') saveDeliveryMenToStorage(); } catch (e) { /* noop */ }
    } else {
        // ensure Juan Pérez exists as a demo entry for DB integration tests
        try {
            const exists = mockDeliveryMen.find(d => d.name === 'Juan Pérez' || d.email === 'repartidor@remates.com');
            if (!exists) {
                const nextId = 'D' + String((mockDeliveryMen.length ? Math.max(...mockDeliveryMen.map(x => Number(x.id.replace(/\D/g, '') || 0))) + 1 : 1)).padStart(3, '0');
                mockDeliveryMen.push({ id: nextId, name: 'Juan Pérez', email: 'repartidor@remates.com', phone: '3007654321', vehicle: 'Moto', licensePlate: 'ABC123', status: 'Activo' });
                try { if (typeof saveDeliveryMenSafe === 'function') saveDeliveryMenSafe(); else if (typeof saveDeliveryMenToStorage === 'function') saveDeliveryMenToStorage(); } catch (e) { /* noop */ }
            }
        } catch (err) { /* noop */ }
    }

    const enriched = (mockDeliveryMen || []).map(d => {
        const assigned = mockOrders.filter(o => o.deliveryMan === d.name || o.deliveryMan === d.email || o.deliveryMan === d.id);
        return { ...d, assignedOrders: assigned };
    });

    // --- Admin view state (search, filter, pagination) ---
    // Persisted per module so re-renders keep UI state
    window.__adminDeliveryState = window.__adminDeliveryState || { search: '', status: 'all', page: 1, pageSize: 8 };
    const adminState = window.__adminDeliveryState;

    // Filtering
    const normalizedSearch = (adminState.search || '').toLowerCase();
    const filtered = enriched.filter(d => {
        if (adminState.status && adminState.status !== 'all' && d.status !== adminState.status) return false;
        if (!normalizedSearch) return true;
        return (d.name && d.name.toLowerCase().includes(normalizedSearch)) || (d.id && d.id.toLowerCase().includes(normalizedSearch)) || (d.phone && d.phone.toLowerCase().includes(normalizedSearch)) || (d.email && d.email.toLowerCase().includes(normalizedSearch));
    });

    const total = filtered.length;
    const pageSize = Number(adminState.pageSize) || 8;
    const totalPages = Math.max(1, Math.ceil(total / pageSize));
    if (adminState.page > totalPages) adminState.page = totalPages;
    if (adminState.page < 1) adminState.page = 1;
    const start = (adminState.page - 1) * pageSize;
    const visible = filtered.slice(start, start + pageSize);

    // render differently if this is the public container (simpler view) vs admin container (management view)
        if (container === document.getElementById('page-repartidores')) {
        container.innerHTML = `
            <div class="page active animate-fade-in">
                <h2 class="text-2xl font-bold mb-4">Repartidores</h2>
                <div id="public-delivery-list" class="grid md:grid-cols-2 gap-4">
                    ${enriched.map(d => `
                        <div class="bg-white rounded-lg shadow p-4 flex justify-between items-center">
                            <div>
                                <div class="font-semibold">${d.name} <span class="text-xs text-slate-400">${d.id || ''}</span></div>
                                <div class="text-sm text-slate-500">${d.vehicle} • ${d.phone || d.email}</div>
                                <div class="text-sm mt-1">Entregas: <strong>${d.assignedOrders.length}</strong></div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm ${d.status === 'Activo' ? 'text-green-600' : 'text-red-600'}">${d.status}</div>
                                ${d.phone ? `<a href="${getWhatsAppUrl(d.phone)}" target="_blank" class="inline-block mt-2 text-sm text-green-600">${icons.whats}</a>` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    } else {
        // Admin table layout with search, filters and pagination
        container.innerHTML = `
            <div class="page active animate-fade-in">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                <h2 class="text-3xl font-bold text-slate-800">Gestión de Repartidores</h2>
                <div class="flex gap-3 items-center">
                    <button id="btn-add-delivery" class="bg-custom-blue text-white font-semibold py-2 px-4 rounded-lg hover:bg-custom-blue-dark transition-colors">Añadir Repartidor</button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow overflow-hidden p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 delivery-controls">
                    <div class="flex items-center gap-3">
                        <input id="delivery-search" type="search" placeholder="Buscar por nombre, id, email o teléfono" class="border rounded-lg px-3 py-2 w-72" value="${adminState.search || ''}" />
                        <select id="delivery-filter-status" class="border rounded-lg px-3 py-2">
                            <option value="all" ${adminState.status === 'all' ? 'selected' : ''}>Todos</option>
                            <option value="Activo" ${adminState.status === 'Activo' ? 'selected' : ''}>Activo</option>
                            <option value="Inactivo" ${adminState.status === 'Inactivo' ? 'selected' : ''}>Inactivo</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-slate-600">Mostrar</label>
                        <select id="delivery-page-size" class="border rounded-lg px-3 py-2">
                            <option value="5" ${String(pageSize) === '5' ? 'selected' : ''}>5</option>
                            <option value="8" ${String(pageSize) === '8' ? 'selected' : ''}>8</option>
                            <option value="12" ${String(pageSize) === '12' ? 'selected' : ''}>12</option>
                        </select>
                        <div class="text-sm text-slate-500">${total} resultado(s)</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left table-min" role="table" aria-label="Tabla de repartidores - administración">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4 font-semibold text-slate-600">ID</th>
                                <th class="p-4 font-semibold text-slate-600">Nombre</th>
                                <th class="p-4 font-semibold text-slate-600">Vehículo</th>
                                <th class="p-4 font-semibold text-slate-600">Estado</th>
                                <th class="p-4 font-semibold text-slate-600">Entregas</th>
                                <th class="p-4 font-semibold text-slate-600">Teléfono</th>
                                <th class="p-4 font-semibold text-slate-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${visible.map((d, idx) => `
                                <tr class="border-b hover:bg-slate-50">
                                    <td class="p-4 font-medium">${d.id || ('R' + String(start + idx + 1).padStart(2,'0'))}</td>
                                    <td class="p-4">
                                        <div class="font-semibold">${d.name}</div>
                                        <div class="text-sm text-slate-500">${d.email || ''}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-medium">${d.vehicle || '—'}</div>
                                        <div class="text-sm text-slate-500">${d.licensePlate || ''}</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full ${d.status === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-sm">${d.status}</span>
                                    </td>
                                    <td class="p-4">${d.assignedOrders.length}</td>
                                    <td class="p-4">${d.phone || '—'}</td>
                                    <td class="p-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <div class="relative inline-block">
                                                <button data-id="${d.id}" class="btn btn-secondary btn-sm delivery-actions-gear" aria-haspopup="true" aria-expanded="false" title="Acciones">${gearIcon}</button>
                                                <div class="delivery-actions-menu hidden absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-40">
                                                    <button data-id="${d.id}" class="delivery-action-edit w-full text-left px-3 py-2 text-sm">${icons.edit}Editar</button>
                                                    <button data-id="${d.id}" class="delivery-action-toggle w-full text-left px-3 py-2 text-sm">${icons.toggle}${d.status === 'Activo' ? ' Deshabilitar' : ' Habilitar'}</button>
                                                    <button data-id="${d.id}" class="delivery-action-delete w-full text-left px-3 py-2 text-sm text-red-600">${icons.trash}Eliminar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-slate-600">Página ${adminState.page} de ${totalPages}</div>
                    <div class="flex items-center gap-2">
                        <button id="delivery-page-prev" class="btn btn-secondary btn-sm" ${adminState.page <= 1 ? 'disabled' : ''}>Anterior</button>
                        ${Array.from({ length: totalPages }).map((_, p) => `<button class="btn btn-secondary btn-sm delivery-page-btn" data-page="${p+1}" ${adminState.page === p+1 ? 'aria-current="true"' : ''}>${p+1}</button>`).join('')}
                        <button id="delivery-page-next" class="btn btn-secondary btn-sm" ${adminState.page >= totalPages ? 'disabled' : ''}>Siguiente</button>
                    </div>
                </div>
            </div>
            </div>
        `;
    }

    // wire add and delete actions
    setTimeout(() => {
        const addBtn = document.getElementById('btn-add-delivery');
        if (addBtn) addBtn.addEventListener('click', (e) => { e.preventDefault(); if (window.openEditModal) window.openEditModal('delivery', {}); });
    // WhatsApp links are anchor tags now; keep compatibility for any old buttons
    document.querySelectorAll('.open-whats').forEach(btn => btn.addEventListener('click', (ev) => { ev.preventDefault(); const phone = btn.getAttribute('data-phone'); const url = (getWhatsAppUrl && getWhatsAppUrl(phone || window.whatsappNumber, 'Hola, estoy interesado en coordinar entregas.')) || '#'; if (url) window.open(url, '_blank'); }));

    // 'Ver' button removed; details are available from the gear menu actions (Editar)

        // gear menu actions for delivery men
        document.querySelectorAll('.delivery-actions-gear').forEach(gear => gear.addEventListener('click', (e) => {
            e.stopPropagation(); const menu = gear.nextElementSibling; document.querySelectorAll('.delivery-actions-menu').forEach(m => { if (m !== menu) m.classList.add('hidden'); }); if (menu) menu.classList.toggle('hidden');
        }));
        document.addEventListener('click', () => document.querySelectorAll('.delivery-actions-menu').forEach(m => m.classList.add('hidden')));

        document.querySelectorAll('.delivery-action-edit').forEach(btn => btn.addEventListener('click', (ev) => {
            const id = btn.getAttribute('data-id'); const d = mockDeliveryMen.find(x => x.id === id); if (!d) return; if (window.openEditModal) { btn.parentElement.classList.add('hidden'); window.openEditModal('delivery', Object.assign({}, d)); }
        }));

        document.querySelectorAll('.delivery-action-toggle').forEach(btn => btn.addEventListener('click', async (ev) => {
            const id = btn.getAttribute('data-id'); const d = mockDeliveryMen.find(x => x.id === id); if (!d) return; d.status = (d.status === 'Activo') ? 'Inactivo' : 'Activo';
            try { await saveDeliveryMenSafe(); } catch (err) { console.warn(err); }
            if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage();
            showAlert('Estado actualizado.', 'info');
        }));

        document.querySelectorAll('.delivery-action-delete').forEach(btn => btn.addEventListener('click', async (ev) => {
            const id = btn.getAttribute('data-id'); const idx = mockDeliveryMen.findIndex(x => x.id === id); if (idx < 0) return; if (!confirm('¿Confirmar eliminar repartidor?')) return; const removed = mockDeliveryMen.splice(idx, 1)[0]; try { await saveDeliveryMenSafe(); } catch (err) { console.warn(err); }
            if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage();
            showUndoToast('Repartidor eliminado', async () => { mockDeliveryMen.splice(idx, 0, removed); try { await saveDeliveryMenSafe(); } catch (err) {} if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage(); showAlert('Eliminación revertida', 'info'); }, 8000);
            showAlert('Repartidor eliminado', 'info');
        }));

        // --- Controls: search, filter, page size and pagination wiring ---
        const searchInput = document.getElementById('delivery-search');
        const statusSelect = document.getElementById('delivery-filter-status');
        const pageSizeSelect = document.getElementById('delivery-page-size');
        const prevBtn = document.getElementById('delivery-page-prev');
        const nextBtn = document.getElementById('delivery-page-next');

        if (searchInput) searchInput.addEventListener('input', (e) => { window.__adminDeliveryState.search = e.target.value || ''; window.__adminDeliveryState.page = 1; if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage(); });
        if (statusSelect) statusSelect.addEventListener('change', (e) => { window.__adminDeliveryState.status = e.target.value || 'all'; window.__adminDeliveryState.page = 1; if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage(); });
        if (pageSizeSelect) pageSizeSelect.addEventListener('change', (e) => { window.__adminDeliveryState.pageSize = Number(e.target.value) || 8; window.__adminDeliveryState.page = 1; if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage(); });

        document.querySelectorAll('.delivery-page-btn').forEach(b => b.addEventListener('click', (ev) => {
            const p = Number(b.getAttribute('data-page')) || 1; window.__adminDeliveryState.page = p; if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage();
        }));
        if (prevBtn) prevBtn.addEventListener('click', () => { if (window.__adminDeliveryState.page > 1) { window.__adminDeliveryState.page--; if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage(); } });
        if (nextBtn) nextBtn.addEventListener('click', () => { window.__adminDeliveryState.page = (window.__adminDeliveryState.page || 1) + 1; if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage(); });
    }, 40);
}

// expose for navigation/main
window.renderAdminDeliveryMenPage = window.renderAdminDeliveryMenPage || renderAdminDeliveryMenPage;

// Función de depuración: fuerza sesión de admin en localStorage y navega a repartidores
window.__debug_setAdmin = function(){
    try {
        localStorage.setItem('remates_session_v1', JSON.stringify({ email: 'admin@remates.com', role: 'admin' }));
        console.log('[DEBUG] Sesión establecida como admin en localStorage');
        if (window.renderNav) window.renderNav();
        if (window.navigateTo) { window.navigateTo('repartidores'); } else { console.warn('navigateTo no disponible'); }
    } catch (e) { console.error('debug_setAdmin failed', e); }
};

export function renderDeliveryTasksPage() {
    const container = document.getElementById('page-delivery-tasks');
    
    const role = getCurrentUserRole();
    if (role !== 'delivery') {
        container.innerHTML = `
            <div class="text-center mt-10">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Acceso Restringido</h2>
                <p class="text-slate-600 mb-6">Solo los repartidores pueden acceder a esta sección.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <h2 class="text-3xl font-bold text-slate-800 mb-6">Entregas Activas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            ${deliveryTasks.map(task => `
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-slate-800">Pedido #${task.id}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${task.status === 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                                ${task.status}
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-slate-500">Cliente</p>
                            <p class="font-medium">${task.customerName}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-slate-500">Dirección</p>
                            <p class="font-medium">${task.address}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-slate-500">Teléfono</p>
                            <p class="font-medium">${task.phone}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-slate-500">Hora estimada</p>
                            <p class="font-medium">${task.estimatedTime}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-slate-500">Productos</p>
                            <ul class="list-disc list-inside text-sm">
                                ${task.items.map(item => `<li>${item}</li>`).join('')}
                            </ul>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg text-custom-blue">${formatCurrency(task.total)}</span>
                            <div class="flex gap-2">
                                <button class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">
                                    Llamar
                                </button>
                                <button onclick="completeDelivery('${task.id}')" class="bg-green-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                                    Completar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

export function renderDeliveryHistoryPage() {
    const container = document.getElementById('page-delivery-history');
    
    const role = getCurrentUserRole();
    if (role !== 'delivery') {
        container.innerHTML = `
            <div class="text-center mt-10">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Acceso Restringido</h2>
                <p class="text-slate-600 mb-6">Solo los repartidores pueden acceder a esta sección.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <h2 class="text-3xl font-bold text-slate-800 mb-6">Historial de Entregas</h2>
        
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-4 font-semibold text-slate-600">ID Pedido</th>
                            <th class="p-4 font-semibold text-slate-600">Cliente</th>
                            <th class="p-4 font-semibold text-slate-600">Fecha</th>
                            <th class="p-4 font-semibold text-slate-600">Total</th>
                            <th class="p-4 font-semibold text-slate-600">Estado</th>
                            <th class="p-4 font-semibold text-slate-600">Completado</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${deliveryHistory.map(delivery => `
                            <tr class="border-b border-slate-200 hover:bg-slate-50">
                                <td class="p-4 font-medium">${delivery.id}</td>
                                <td class="p-4">${delivery.customerName}</td>
                                <td class="p-4">${formatCurrency(delivery.total)}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">${delivery.status}</span>
                                </td>
                                <td class="p-4">${delivery.completedAt}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

export function renderDeliveryDashboardPage() {
    const container = document.getElementById('page-delivery-dashboard');
    
    const role = getCurrentUserRole();
    if (role !== 'delivery') {
        container.innerHTML = `
            <div class="text-center mt-10">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Acceso Restringido</h2>
                <p class="text-slate-600 mb-6">Solo los repartidores pueden acceder a esta sección.</p>
            </div>
        `;
        return;
    }
    
        const deliveriesCompleted = deliveryHistory.length; // Fix typo here
    const deliveriesPending = deliveryTasks.length;
    const totalEarnings = deliveryHistory.reduce((sum, d) => sum + (d.total * 0.1), 0); // Suponiendo 10% de comisión
    
    container.innerHTML = `
        <h2 class="text-3xl font-bold text-slate-800 mb-6">Dashboard de Repartidor</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold text-slate-800 mb-2">Entregas Pendientes</h3>
                <p class="text-3xl font-bold text-blue-600">${deliveriesPending}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold text-slate-800 mb-2">Entregas Completadas</h3>
                    <p class="text-3xl font-bold text-green-600">${deliveriesCompleted}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold text-slate-800 mb-2">Ganancias Totales</h3>
                <p class="text-3xl font-bold text-purple-600">${formatCurrency(totalEarnings)}</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-xl font-semibold text-slate-800 mb-4">Próximas Entregas</h3>
            <div class="space-y-4">
                ${deliveryTasks.slice(0, 3).map(task => `
                    <div class="border border-slate-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-slate-800 mb-2">Pedido #${task.id}</h4>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">${task.status}</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-1">${task.customerName} - ${task.address}</p>
                        <p class="text-sm text-slate-500">Hora estimada: ${task.estimatedTime}</p>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function completeDelivery(orderId) {
    // Simular completar entrega
    const taskIndex = deliveryTasks.findIndex(t => t.id === orderId);
    if (taskIndex !== -1) {
        const completedTask = deliveryTasks.splice(taskIndex, 1)[0];
        completedTask.status = 'Entregado';
        completedTask.completedAt = new Date().toLocaleString();
        deliveryHistory.unshift(completedTask);
        
        showAlert(`Entrega #${orderId} completada`, 'success');
        renderDeliveryTasksPage();
        renderDeliveryDashboardPage();
    }
}

export function renderDeliveryPage() {
    const container = document.getElementById('page-delivery');
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'delivery') {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <h2 class="text-3xl font-bold mb-4">Acceso Restringido</h2>
                <p class="mb-4">Solo los repartidores pueden ver sus entregas.</p>
                <button onclick="event.preventDefault(); window.navigateTo && window.navigateTo('login')" class="bg-blue-500 text-white px-6 py-2 rounded">Ir a Iniciar Sesión</button>
            </div>
        `;
        return;
    }
    container.innerHTML = `
        <div class="page active animate-fade-in">
            <h2 class="text-2xl font-bold mb-4">Mis Entregas</h2>
            <div id="delivery-tasks-list"></div>
        </div>
    `;
    const tasksList = document.getElementById('delivery-tasks-list');
    if (tasksList) {
        tasksList.innerHTML = deliveryTasks.length === 0
            ? `<p class="text-gray-500">No hay tareas de entrega asignadas.</p>`
            : deliveryTasks.map(task => `
                <div class="bg-white rounded-lg shadow p-4 mb-4 animate-fade-in">
                    <div><strong>ID:</strong> ${task.id}</div>
                    <div><strong>Fecha:</strong> ${task.deliveryDate}</div>
                    <div><strong>Cliente:</strong> ${task.customer}</div>
                    <div><strong>Dirección:</strong> ${task.address}</div>
                    <div><strong>Estado:</strong> ${task.status}</div>
                </div>
            `).join('');
    }
}

// ------------------ Repartidor: Perfil y vistas (Dashboard / Pedidos / Entregas / Historial) ------------------
// Las funciones siguientes renderizan contenido en `#page-delivery-dashboard` y `#page-repartidores`
// y están pensadas para usarse por el rol 'delivery'.

function makeDeliveryMenu(active) {
    return `
        <div class="bg-white rounded-xl shadow mb-6 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-bold">Perfil Repartidor</h2>
                <div class="text-sm text-slate-500">Panel de control</div>
            </div>
            <div class="flex gap-2">
                <button id="tab-dashboard" class="tab-btn px-4 py-2 rounded ${active === 'dashboard' ? 'bg-custom-blue text-white' : 'bg-gray-100'}">Dashboard</button>
                <button id="tab-pedidos" class="tab-btn px-4 py-2 rounded ${active === 'pedidos' ? 'bg-custom-blue text-white' : 'bg-gray-100'}">Pedidos</button>
                <button id="tab-activas" class="tab-btn px-4 py-2 rounded ${active === 'activas' ? 'bg-custom-blue text-white' : 'bg-gray-100'}">Entregas activas</button>
                <button id="tab-historial" class="tab-btn px-4 py-2 rounded ${active === 'historial' ? 'bg-custom-blue text-white' : 'bg-gray-100'}">Historial</button>
            </div>
        </div>
    `;
}

// Mostrar perfil del repartidor (main entry). targetContainer puede ser page-repartidores o page-delivery-dashboard
export async function cargarDashboard(targetContainerId = 'page-repartidores') {
    const container = document.getElementById(targetContainerId);
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'delivery') {
        container.innerHTML = `<div class="text-center mt-10"><h2 class="text-2xl font-bold">Acceso Restringido</h2><p class="text-slate-600">Solo usuarios con rol repartidor pueden ver este panel.</p></div>`;
        return;
    }

    // basic stats
    const myEmail = (typeof getCurrentUserEmail === 'function') ? getCurrentUserEmail() : null;
    const myName = (mockDeliveryMen.find(d => d.email === myEmail) || mockDeliveryMen.find(d => d.name && d.name.includes(myEmail)) || { name: myEmail || 'Repartidor' }).name;
    const assigned = mockOrders.filter(o => o.deliveryId && (o.deliveryMan === myName || o.deliveryId));
    const active = assigned.filter(o => ['En camino','En ruta','Enviado','En curso'].includes(o.status));
    const completed = assigned.filter(o => o.status === 'Entregado');

    container.innerHTML = `
        ${makeDeliveryMenu('dashboard')}
        <div id="delivery-content">
            <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="text-sm text-slate-500">Pedidos asignados</div>
                    <div class="text-2xl font-bold">${assigned.length}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="text-sm text-slate-500">Entregas en curso</div>
                    <div class="text-2xl font-bold">${active.length}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="text-sm text-slate-500">Historial (entregadas)</div>
                    <div class="text-2xl font-bold">${completed.length}</div>
                </div>
            </div>

            <div id="delivery-dashboard-cards" class="grid md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4 shadow">
                    <h3 class="font-semibold mb-2">Actividades recientes</h3>
                    <ul class="text-sm text-slate-600" id="recent-activities">
                        ${assigned.slice(0,6).map(o => `<li>#${o.id} — ${o.customerName || o.customer || o.customerEmail || ''} — <em>${o.status}</em></li>`).join('')}
                    </ul>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <h3 class="font-semibold mb-2">Siguientes entregas</h3>
                    <div id="next-deliveries">${active.length ? active.slice(0,6).map(o => `<div class="p-2 border-b">#${o.id} — ${o.address || o.customerAddress || ''} — <span class="text-sm text-slate-500">${o.status}</span></div>`).join('') : '<div class="text-slate-500">Sin entregas activas</div>'}</div>
                </div>
            </div>
        </div>
    `;

    // attach tab handlers
    setTimeout(() => {
        const tDash = document.getElementById('tab-dashboard');
        const tPed = document.getElementById('tab-pedidos');
        const tAct = document.getElementById('tab-activas');
        const tHis = document.getElementById('tab-historial');
        if (tPed) tPed.addEventListener('click', () => listarPedidos(targetContainerId));
        if (tAct) tAct.addEventListener('click', () => mostrarEntregasActivas(targetContainerId));
        if (tHis) tHis.addEventListener('click', () => mostrarHistorial(targetContainerId));
    }, 20);
}

export function listarPedidos(targetContainerId = 'page-repartidores') {
    const container = document.getElementById(targetContainerId);
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'delivery') { container.innerHTML = '<div class="text-center mt-8">Acceso restringido</div>'; return; }

    const myEmail = (typeof getCurrentUserEmail === 'function') ? getCurrentUserEmail() : null;
    const myName = (mockDeliveryMen.find(d => d.email === myEmail) || { name: myEmail || 'Repartidor' }).name;
    const assigned = mockOrders.filter(o => o.deliveryMan === myName || o.deliveryId);

    container.innerHTML = `
        ${makeDeliveryMenu('pedidos')}
        <div id="delivery-content">
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50"><tr><th class="p-3">ID</th><th>Cliente</th><th>Dirección</th><th>Estado</th><th class="text-right">Acciones</th></tr></thead>
                    <tbody>
                        ${assigned.map(o => `
                            <tr class="border-b hover:bg-slate-50">
                                <td class="p-3">#${o.id}</td>
                                <td class="p-3">${o.customerName || o.customer || o.customerEmail || ''}</td>
                                <td class="p-3">${o.address || ''}</td>
                                <td class="p-3">${o.status || ''}</td>
                                <td class="p-3 text-right">
                                    ${o.status === 'Pendiente' ? `<button data-id="${o.id}" class="btn-accept bg-green-500 text-white px-3 py-1 rounded mr-2">Aceptar</button><button data-id="${o.id}" class="btn-reject bg-red-500 text-white px-3 py-1 rounded">Rechazar</button>` : `<button data-id="${o.id}" class="btn-view text-sm text-custom-blue">Ver</button>`}
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;

    setTimeout(() => {
        document.querySelectorAll('.btn-accept').forEach(b => b.addEventListener('click', async (e) => {
            const id = b.getAttribute('data-id');
            if (!id) return; await cambiarEstadoEntrega(id, 'En camino');
            mostrarEntregasActivas(targetContainerId);
        }));
        document.querySelectorAll('.btn-reject').forEach(b => b.addEventListener('click', async (e) => {
            const id = b.getAttribute('data-id'); if (!id) return; await cambiarEstadoEntrega(id, 'Rechazado'); listarPedidos(targetContainerId);
        }));
    }, 40);
}

export function mostrarEntregasActivas(targetContainerId = 'page-repartidores') {
    const container = document.getElementById(targetContainerId);
    if (!container) return;
    if (getCurrentUserRole() !== 'delivery') { container.innerHTML = '<div class="text-center mt-8">Acceso restringido</div>'; return; }

    const myEmail = (typeof getCurrentUserEmail === 'function') ? getCurrentUserEmail() : null;
    const myName = (mockDeliveryMen.find(d => d.email === myEmail) || { name: myEmail || 'Repartidor' }).name;
    const active = mockOrders.filter(o => (o.deliveryMan === myName || o.deliveryId) && ['En camino','En ruta','En curso','Enviado'].includes(o.status));

    container.innerHTML = `
        ${makeDeliveryMenu('activas')}
        <div id="delivery-content">
            <div class="grid gap-4">
                ${active.length ? active.map(o => `
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="flex justify-between items-center mb-2">
                            <div><strong>Pedido #${o.id}</strong><div class="text-sm text-slate-500">${o.customerName || o.customer || ''}</div></div>
                            <div>
                                <select data-id="${o.id}" class="change-status border p-1 rounded">
                                    <option ${o.status === 'En camino' ? 'selected' : ''}>En camino</option>
                                    <option ${o.status === 'Entregado' ? 'selected' : ''}>Entregado</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-sm text-slate-600">Dirección: ${o.address || ''}</div>
                        <div class="mt-3 text-sm text-slate-500">Detalles: ${o.items ? (o.items.map(i => i.name + ' x' + i.qty).join(', ')) : ''}</div>
                    </div>
                `).join('') : `<div class="bg-white rounded-lg p-4 shadow text-slate-500">No hay entregas activas</div>`}
            </div>
        </div>
    `;

    setTimeout(() => {
        document.querySelectorAll('.change-status').forEach(sel => sel.addEventListener('change', async (ev) => {
            const id = sel.getAttribute('data-id'); const nuevo = sel.value; if (!id) return; await cambiarEstadoEntrega(id, nuevo); mostrarEntregasActivas(targetContainerId);
        }));
    }, 20);
}

export function mostrarHistorial(targetContainerId = 'page-repartidores') {
    const container = document.getElementById(targetContainerId);
    if (!container) return;
    if (getCurrentUserRole() !== 'delivery') { container.innerHTML = '<div class="text-center mt-8">Acceso restringido</div>'; return; }

    const myEmail = (typeof getCurrentUserEmail === 'function') ? getCurrentUserEmail() : null;
    const myName = (mockDeliveryMen.find(d => d.email === myEmail) || { name: myEmail || 'Repartidor' }).name;
    const completed = mockOrders.filter(o => (o.deliveryMan === myName || o.deliveryId) && o.status === 'Entregado');

    container.innerHTML = `
        ${makeDeliveryMenu('historial')}
        <div id="delivery-content">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <ul class="divide-y">
                    ${completed.length ? completed.map(o => `<li class="p-4"><div class="flex justify-between"><div><strong>#${o.id}</strong> — ${o.customerName || o.customer || ''}<div class="text-sm text-slate-500">${o.date || o.completedAt || ''}</div></div><div class="text-sm text-slate-500">${o.total ? formatCurrency(o.total) : ''}</div></div></li>`).join('') : '<li class="p-4 text-slate-500">No hay entregas completadas</li>'}
                </ul>
            </div>
        </div>
    `;
}

export async function cambiarEstadoEntrega(pedidoId, nuevoEstado) {
    try {
        const idx = mockOrders.findIndex(o => String(o.id) === String(pedidoId));
        if (idx === -1) { showAlert('Pedido no encontrado', 'error'); return false; }
        const order = mockOrders[idx];
        order.status = nuevoEstado;
        // registrar timestamp para entregado
        if (nuevoEstado === 'Entregado') order.completedAt = new Date().toLocaleString();
        try { await saveOrdersToStorage(mockOrders); } catch (e) { /* noop */ }
        showAlert('Estado actualizado', 'success');
        return true;
    } catch (err) { console.error(err); showAlert('Error actualizando estado', 'error'); return false; }
}

// Exponer funciones en window para uso desde navegación u otros módulos
window.cargarDeliveryDashboard = window.cargarDeliveryDashboard || cargarDashboard;
window.listarPedidosDelivery = window.listarPedidosDelivery || listarPedidos;
window.mostrarEntregasActivas = window.mostrarEntregasActivas || mostrarEntregasActivas;
window.mostrarHistorialDelivery = window.mostrarHistorialDelivery || mostrarHistorial;
window.cambiarEstadoEntrega = window.cambiarEstadoEntrega || cambiarEstadoEntrega;
