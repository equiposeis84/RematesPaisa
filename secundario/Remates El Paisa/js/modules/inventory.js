/*
Archivo: js/modules/inventory.js
Descripción: Gestión de inventario: entradas, salidas y stock.
Explicación: Funciones para listar inventario, ajustar cantidades y sincronizar con productos.
Nota: Validar operaciones concurrentes y mantener integridad de stock.
*/

import { getCurrentUserRole, mockProducts, mockOrders, saveProductsToStorage, gearIcon, saveProductsSafe, icons, normalizeImagePath } from './constants.js';
import { formatCurrency, showAlert, showUndoToast } from './utils.js';

// --- FUNCIONES DE INVENTARIO ---
export function renderInventoryPage() {
    const container = document.getElementById('page-inventory');
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'admin') {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <h2 class="text-3xl font-bold mb-4">Acceso Restringido</h2>
                <p class="mb-4">Solo los administradores pueden ver el inventario.</p>
                <button onclick="event.preventDefault(); window.navigateTo && window.navigateTo('login')" class="bg-blue-500 text-white px-6 py-2 rounded">Ir a Iniciar Sesión</button>
            </div>
        `;
        return;
    }
    container.innerHTML = `
        <div class="page active animate-fade-in">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Inventario</h2>
                <div class="flex gap-2">
                    <button id="btn-new-product" class="bg-custom-blue text-white px-4 py-2 rounded-lg">Nuevo Producto</button>
                </div>
            </div>
            <div id="inventory-list" class="space-y-3"></div>
        </div>
    `;

    const inventoryList = document.getElementById('inventory-list');
    function renderList() {
        if (!inventoryList) return;
        inventoryList.innerHTML = mockProducts.map(product => `
            <div class="bg-white rounded-xl shadow p-3 flex items-center gap-4 animate-fade-in product-card" data-product-id="${product.id}">
                <div class="w-20 h-20 flex-shrink-0 bg-slate-100 rounded overflow-hidden flex items-center justify-center">
                    <img src="${normalizeImagePath(product.image || 'img/placeholder.svg')}" alt="${product.name}" class="object-cover w-full h-full" />
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold text-slate-800">${product.name}</div>
                                                <div class="text-sm text-slate-500">${product.category || ''} • ${(() => {
                                                    const s = Number(product.stock || 0);
                                                    const cls = s <= 5 ? 'bg-red-100 text-red-800' : s <= 20 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                                                    return `<span class="px-2 py-0.5 rounded ${cls} text-xs font-medium">${s} en stock</span>`;
                                                })()}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm ${product.disabled ? 'text-red-600' : 'text-green-600'}">${product.disabled ? 'Deshabilitado' : 'Activo'}</div>
                            <div class="text-sm text-slate-500">${formatCurrency(product.price)}</div>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-slate-600">${product.description || ''}</div>
                    <div class="mt-2 text-xs text-slate-700 flex gap-2 items-center">
                        <button data-id="${product.id}" class="action-edit text-left px-2 py-1 rounded bg-yellow-50 border">${icons.edit}Editar</button>
                        <button data-id="${product.id}" class="action-toggle text-left px-2 py-1 rounded bg-blue-50 border">${icons.toggle}${product.disabled ? 'Habilitar' : 'Deshabilitar'}</button>
                        <button data-id="${product.id}" class="action-delete text-left px-2 py-1 rounded bg-red-50 border text-red-600">${icons.trash}Eliminar</button>
                        <span class="ml-auto text-xs text-slate-400">ID: ${product.id}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderList();

    // new product button
    const newBtn = document.getElementById('btn-new-product');
    if (newBtn) newBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (window.openEditModal) window.openEditModal('product', { id: null });
    });

    // delegated event handler for product actions so handlers survive re-renders
    inventoryList.addEventListener('click', async (ev) => {
        const gearBtn = ev.target.closest && ev.target.closest('.product-actions-gear');
        if (gearBtn) {
            ev.stopPropagation();
            const card = gearBtn.closest('.product-card');
            const menu = card && card.querySelector('.product-actions-menu');
            // close other menus
            document.querySelectorAll('.product-actions-menu').forEach(m => { if (m !== menu) m.classList.add('hidden'); });
            if (menu) menu.classList.toggle('hidden');
            return;
        }

        const editBtn = ev.target.closest && ev.target.closest('.action-edit');
        if (editBtn) {
            ev.preventDefault();
            const id = editBtn.getAttribute('data-id');
            const p = mockProducts.find(x => String(x.id) === String(id));
            if (p && window.openEditModal) window.openEditModal('product', p);
            return;
        }

        const toggleBtn = ev.target.closest && ev.target.closest('.action-toggle');
        if (toggleBtn) {
            ev.preventDefault();
            const id = toggleBtn.getAttribute('data-id');
            const p = mockProducts.find(x => String(x.id) === String(id));
            if (!p) return;
            const prev = !!p.disabled;
            p.disabled = !prev;
            try { if (typeof saveProductsSafe === 'function') await saveProductsSafe(); } catch (err) { /* noop */ }
            renderList();
            showAlert(p.disabled ? 'Producto deshabilitado.' : 'Producto habilitado.', 'info');
            // provide undo action
            showUndoToast(p.disabled ? 'Producto deshabilitado' : 'Producto habilitado', async () => {
                p.disabled = prev;
                try { if (typeof saveProductsSafe === 'function') await saveProductsSafe(); } catch (err) {}
                renderList();
                showAlert('Cambio revertido', 'info');
            }, 8000);
            return;
        }

        const deleteBtn = ev.target.closest && ev.target.closest('.action-delete');
        if (deleteBtn) {
            ev.preventDefault();
            const id = deleteBtn.getAttribute('data-id');
            const idx = mockProducts.findIndex(x => String(x.id) === String(id));
            if (idx < 0) return;
            if (!confirm('¿Confirmar eliminación del producto?')) return;
            const removed = mockProducts.splice(idx, 1)[0];
            try { if (typeof saveProductsSafe === 'function') await saveProductsSafe(); } catch (err) {}
            renderList();
            showUndoToast('Producto eliminado', async () => {
                mockProducts.splice(idx, 0, removed);
                try { if (typeof saveProductsSafe === 'function') await saveProductsSafe(); } catch (err) {}
                renderList();
                showAlert('Eliminación de producto revertida', 'info');
            }, 8000);
            showAlert('Producto eliminado.', 'info');
            return;
        }

    });

    // close menus on outside click
    document.addEventListener('click', () => document.querySelectorAll('.product-actions-menu').forEach(m => m.classList.add('hidden')));
}

export function renderAdminOrdersPage() {
    const container = document.getElementById('page-admin-orders');
    const role = getCurrentUserRole();
    
    if (role !== 'admin') {
        container.innerHTML = `
            <div class="text-center mt-10">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Acceso Restringido</h2>
                <p class="text-slate-600 mb-6">Solo los administradores pueden acceder a esta sección.</p>
            </div>
        `;
        return;
    }
    
    const statusColors = { 
        Enviado: 'bg-blue-100 text-blue-800', 
        Entregado: 'bg-green-100 text-green-800', 
        Procesando: 'bg-yellow-100 text-yellow-800', 
        Cancelado: 'bg-red-100 text-red-800' 
    };
    
    container.innerHTML = `
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-slate-800">Gestión de Pedidos</h2>
            <div class="flex gap-2">
                <select class="px-4 py-2 border rounded-lg">
                    <option>Filtrar por estado</option>
                    <option>Procesando</option>
                    <option>Enviado</option>
                    <option>Entregado</option>
                    <option>Cancelado</option>
                </select>
            </div>
        </div>
        
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
                            <th class="p-4 font-semibold text-slate-600">Repartidor</th>
                            <th class="p-4 font-semibold text-slate-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${mockOrders.map(o => `
                            <tr class="border-b border-slate-200 hover:bg-slate-50">
                                <td class="p-4 font-medium">${o.id}</td>
                                <td class="p-4">${o.customerName}</td>
                                <td class="p-4">${o.date}</td>
                                <td class="p-4">${formatCurrency(o.total)}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusColors[o.status] || 'bg-gray-100 text-gray-800'}">${o.status}</span>
                                </td>
                                <td class="p-4">${o.deliveryMan || 'Sin asignar'}</td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <button data-order-id="${o.id}" class="text-blue-500 hover:text-blue-700 btn-view-order">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <button class="text-green-500 hover:text-green-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;

    // attach delegated handler for view details buttons
    setTimeout(() => {
        document.querySelectorAll('.btn-view-order').forEach(btn => btn.addEventListener('click', (ev) => {
            ev.preventDefault();
            const id = btn.getAttribute('data-order-id');
            if (!id) return;
            if (window.showOrderDetails) window.showOrderDetails(id);
        }));
    }, 20);
}