/*
Archivo: js/modules/providers.js
Descripción: Gestión de proveedores y relaciones de suministro.
Explicación: Funciones para listar proveedores, crear/editar información de contacto y vincular productos.
Nota: Mantener datos de contacto separados y validados.
*/

import { getCurrentUserRole, mockProviders, gearIcon, getWhatsAppUrl, saveProvidersSafe, icons } from './constants.js';

export function renderProvidersPage() {
    const container = document.getElementById('page-providers');
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'admin') {
        container.innerHTML = `<div class="flex flex-col items-center justify-center h-full"><h2 class="text-3xl font-bold mb-4">Acceso Restringido</h2><p class="mb-4">Solo los administradores pueden ver los proveedores.</p></div>`;
        return;
    }

    container.innerHTML = `
        <div class="page active animate-fade-in">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Proveedores</h2>
                <button id="btn-new-provider" class="btn btn-primary">Nuevo Proveedor</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="providers-list">
                ${mockProviders.map(p => `
                    <div class="bg-white rounded-xl shadow p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold">${p.name} <span class="text-xs text-slate-400">(${p.id || ''})</span></div>
                                <div class="text-sm text-slate-500">Contacto: ${p.contact} • ${p.phone}</div>
                            </div>
                            <div class="flex gap-2 items-center">
                                <button data-id="${p.id}" class="btn btn-secondary btn-sm view-provider">Ver detalles</button>
                                <div class="relative">
                                    <button data-id="${p.id}" class="btn btn-secondary btn-sm provider-actions-gear" aria-haspopup="true" aria-expanded="false">${gearIcon}</button>
                                    <div class="provider-actions-menu hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                        <button data-id="${p.id}" class="provider-action-edit w-full text-left px-3 py-2 text-sm">${icons.edit}Editar</button>
                                        <button data-id="${p.id}" class="provider-action-delete w-full text-left px-3 py-2 text-sm text-red-600">${icons.trash}Eliminar</button>
                                    </div>
                                </div>
                                <a href="${getWhatsAppUrl ? getWhatsAppUrl(p.phone, 'Hola, quisiera pedir información sobre sus productos') : '#'}" target="_blank" title="Contactar por WhatsApp">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white">${icons.whats}</span>
                                </a>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-slate-600">Productos: ${p.products.join(', ')}</div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;

    setTimeout(() => {
        const newBtn = document.getElementById('btn-new-provider');
        if (newBtn) newBtn.addEventListener('click', (e) => { e.preventDefault(); if (window.openEditModal) window.openEditModal('provider', {}); });
        document.querySelectorAll('.view-provider').forEach(b => b.addEventListener('click', (ev) => { const id = b.getAttribute('data-id'); const p = mockProviders.find(x => x.id === id); if (p && window.openDetailsModal) window.openDetailsModal('provider', p); }));

        // gear menu wiring: edit / delete
        document.querySelectorAll('.provider-actions-gear').forEach(gear => gear.addEventListener('click', (ev) => {
            ev.stopPropagation(); const menu = gear.nextElementSibling; document.querySelectorAll('.provider-actions-menu').forEach(m => { if (m !== menu) m.classList.add('hidden'); }); if (menu) menu.classList.toggle('hidden');
        }));
        document.addEventListener('click', () => document.querySelectorAll('.provider-actions-menu').forEach(m => m.classList.add('hidden')));

        document.querySelectorAll('.provider-action-edit').forEach(btn => btn.addEventListener('click', (ev) => {
            const id = btn.getAttribute('data-id'); const p = mockProviders.find(x => x.id === id); if (!p) return; if (window.openEditModal) { btn.parentElement.classList.add('hidden'); window.openEditModal('provider', Object.assign({}, p)); }
        }));

        document.querySelectorAll('.provider-action-delete').forEach(btn => btn.addEventListener('click', async (ev) => {
            ev.preventDefault(); const id = btn.getAttribute('data-id'); const idx = mockProviders.findIndex(x => x.id === id); if (idx < 0) return; if (!confirm('¿Confirmar eliminación del proveedor?')) return; const removed = mockProviders.splice(idx, 1)[0];
            try { await saveProvidersSafe(); } catch (err) { console.warn(err); }
            if (window.renderProvidersPage) window.renderProvidersPage();
            // show undo toast
            try {
                if (window.showUndoToast) {
                    window.showUndoToast('Proveedor eliminado', async () => {
                        mockProviders.splice(idx, 0, removed);
                        try { await saveProvidersSafe(); } catch (err) { console.warn(err); }
                        if (window.renderProvidersPage) window.renderProvidersPage();
                        if (window.showAlert) window.showAlert('Eliminación revertida', 'info');
                    }, 8000);
                }
            } catch (e) { console.warn(e); }
        }));
    }, 40);
}
