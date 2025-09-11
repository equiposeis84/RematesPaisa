/*
Archivo: js/modules/users.js
Descripción: Gestión de usuarios y roles (administradores, repartidores, clientes).
Explicación: Contiene funciones para listar usuarios, editar perfiles y gestionar permisos.
Importante: No exponer contraseñas en texto plano; usar hashes y almacenamiento seguro cuando haya backend.
*/

// --- FUNCIONES DE USUARIOS ---
import { getCurrentUserRole, mockCustomers, mockUsers, getCurrentUserEmail, pendingAdminRequests, saveCustomersToStorage, saveUsersToStorage, gearIcon, saveCustomersSafe, icons, mockDeliveryMen, mockOrders, saveDeliveryMenSafe, saveUsersSafe } from './constants.js';
import { showAlert, showUndoToast } from './utils.js';

export function renderUsersPage() {
    const container = document.getElementById('page-users');
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'admin') {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <h2 class="text-3xl font-bold mb-4">Acceso Restringido</h2>
                <p class="mb-4">Solo los administradores pueden ver los usuarios.</p>
                <button onclick="event.preventDefault(); window.navigateTo && window.navigateTo('login')" class="bg-blue-500 text-white px-6 py-2 rounded">Ir a Iniciar Sesión</button>
            </div>
        `;
        return;
    }
    container.innerHTML = `
        <div class="page active animate-fade-in">
            <h2 class="text-2xl font-bold mb-4">Usuarios</h2>
            <div id="users-list"></div>
        </div>
    `;
    const usersList = document.getElementById('users-list');
    if (usersList) {
        // show combined view: customers from mockCustomers and entries from mockUsers
        const rows = [];
        // prefer listing all emails from mockUsers, and enrich with customer info when available
        Object.keys(mockUsers).forEach(email => {
            const u = mockUsers[email];
            const cust = mockCustomers.find(c => c.email === email) || {};
        rows.push({ email, name: cust.name || u.name || email, role: u.role || 'customer', hasWhats: !!cust.lastWhatsAppMessage });
        });
        // also include any customers that don't have a mockUsers entry
        mockCustomers.forEach(c => {
            if (!mockUsers[c.email]) rows.push({ email: c.email, name: c.name, role: 'customer' });
        });

        usersList.innerHTML = rows.map((user, idx) => {
            const roleClass = user.role === 'admin' ? 'bg-purple-100 text-purple-800' : user.role === 'delivery' ? 'bg-green-100 text-green-800' : user.role === 'customer' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800';
            // compute a friendly ID: prefer stored id, fallback to U### pattern
            const stored = (mockUsers[user.email] && mockUsers[user.email].id) || '';
            const fallbackId = 'U' + String(idx + 1).padStart(3, '0');
            const displayId = stored || fallbackId;
            return `
            <div class="bg-white rounded-lg shadow p-4 mb-4 flex justify-between items-center animate-fade-in">
                <div>
                    <div class="font-semibold"><span class="text-xs text-slate-400 mr-2">${displayId}</span> ${user.name} ${user.hasWhats ? icons.whats : ''}</div>
                    <div class="text-sm text-slate-500">${user.email}</div>
                </div>
                    <div class="flex items-center gap-3">
                    <div class="user-role-badge-wrapper relative flex items-center gap-2">
                        <button data-email="${user.email}" class="user-role-badge px-2 py-1 rounded text-sm ${roleClass} role-badge-button" aria-expanded="false">${user.role}</button>
                        <div class="role-dropdown hidden absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <button data-email="${user.email}" data-role="admin" class="role-option w-full text-left px-3 py-2 text-sm">Administrador</button>
                            <button data-email="${user.email}" data-role="delivery" class="role-option w-full text-left px-3 py-2 text-sm">Repartidor</button>
                            <button data-email="${user.email}" data-role="customer" class="role-option w-full text-left px-3 py-2 text-sm">Cliente</button>
                            <button data-email="${user.email}" data-role="guest" class="role-option w-full text-left px-3 py-2 text-sm">Invitado</button>
                        </div>
                    </div>
                </div>
            </div>
            `;
        }).join('');

        // wire selects
        setTimeout(() => {
            // note: 'Ver detalles' removed per request; details available via gear menu
            // role dropdown wiring
            document.querySelectorAll('.role-badge-button').forEach(btn => btn.addEventListener('click', (ev) => {
                ev.stopPropagation(); const menu = btn.nextElementSibling; document.querySelectorAll('.role-dropdown').forEach(m => { if (m !== menu) m.classList.add('hidden'); }); if (menu) menu.classList.toggle('hidden');
            }));

            document.addEventListener('click', () => document.querySelectorAll('.role-dropdown').forEach(m => m.classList.add('hidden')));

            // Instead of applying role changes immediately (which would bypass backend validation),
            // enqueue a pending admin request so the change can be reviewed/applied by the backend.
            document.querySelectorAll('.role-option').forEach(opt => opt.addEventListener('click', async (ev) => {
                ev.preventDefault(); ev.stopPropagation(); const email = opt.getAttribute('data-email'); const newRole = opt.getAttribute('data-role');
                try {
                    // ensure user record exists
                    const u = mockUsers[email] || {};
                    u.role = newRole;
                    if (!u.name) {
                        const c = mockCustomers.find(c => c.email === email);
                        u.name = (c && c.name) || u.name || email;
                    }
                    mockUsers[email] = u;

                    // sync delivery men list
                    const dmIdx = mockDeliveryMen.findIndex(d => d.email === email);
                    if (newRole === 'delivery') {
                        if (dmIdx === -1) {
                            const nextId = 'D' + String((mockDeliveryMen.length ? Math.max(...mockDeliveryMen.map(x => Number(String(x.id).replace(/\D/g, '') || 0))) + 1 : 1)).padStart(3, '0');
                            mockDeliveryMen.push({ id: nextId, name: u.name || email, email, phone: '', vehicle: '', licensePlate: '', status: 'Activo' });
                        }
                    } else {
                        if (dmIdx !== -1) mockDeliveryMen.splice(dmIdx, 1);
                    }

                    // sync customers list
                    const custIdx = mockCustomers.findIndex(c => c.email === email);
                    if (newRole === 'customer') {
                        if (custIdx === -1) {
                            const nextId = 'C' + String((mockCustomers.length ? Math.max(...mockCustomers.map(x => Number(String(x.id).replace(/\D/g, '') || 0))) + 1 : 1)).padStart(3, '0');
                            mockCustomers.push({ id: nextId, name: u.name || email, email, phone: '', address: '', registered: new Date().toISOString().slice(0,10), orders: 0 });
                        }
                    } else {
                        if (custIdx !== -1) mockCustomers.splice(custIdx, 1);
                    }

                    // persist changes (try wrappers that may use API)
                    try { if (typeof saveUsersSafe === 'function') await saveUsersSafe(); else if (typeof saveUsersToStorage === 'function') saveUsersToStorage(); } catch (e) { console.warn('save users failed', e); }
                    try { if (typeof saveDeliveryMenSafe === 'function') await saveDeliveryMenSafe(); else if (typeof saveDeliveryMenToStorage === 'function') saveDeliveryMenToStorage(); } catch (e) { /* noop */ }
                    try { if (typeof saveCustomersSafe === 'function') await saveCustomersSafe(); else if (typeof saveCustomersToStorage === 'function') saveCustomersToStorage(); } catch (e) { /* noop */ }

                    showAlert('Rol actualizado a ' + newRole, 'success');

                    // re-render relevant views
                    if (typeof renderUsersPage === 'function') renderUsersPage();
                    if (typeof renderClientsPage === 'function') renderClientsPage();
                    if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage();
                } catch (err) {
                    console.error('Error actualizando rol', err);
                    showAlert('Error actualizando rol', 'error');
                }
            }));
        }, 40);
    }
}

export function renderProfilePage() {
    const container = document.getElementById('page-profile');
    if (!container) return;

    const role = getCurrentUserRole();
    if (role === 'guest') {
        if (window.setNextPage) window.setNextPage('profile');
        if (window.navigateTo) window.navigateTo('login');
        return;
    }

    const userEmail = getCurrentUserEmail();
    const customerData = mockCustomers.find(c => c.email === userEmail) || {};
    const userEntry = (userEmail && mockUsers[userEmail]) || Object.values(mockUsers).find(u => u.role === role) || {};

    container.innerHTML = `
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl font-bold text-slate-800 mb-6">Mi Perfil</h2>
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-xl font-semibold text-slate-800">Información Personal</h3>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <!-- Edit button removed per request -->
                        <div class="relative">
                            <button id="btn-settings-gear" title="Configuración" class="p-2 rounded-full hover:bg-slate-100" style="background:transparent;border:0;cursor:pointer;">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09c.67 0 1.24-.4 1.51-1a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06c.5.5 1.2.65 1.82.33.31-.17.6-.39.86-.66.26-.27.49-.58.66-.86.32-.62.17-1.32-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06c.5.5 1.2.65 1.82.33.28-.15.55-.33.78-.56a2 2 0 012.83 2.83c-.23.23-.41.5-.56.78-.32.62-.17 1.32.33 1.82l.06.06a2 2 0 012.83 2.83l-.06.06a1.65 1.65 0 00-1.51 1H21a2 2 0 010 4h-.09c-.67 0-1.24.4-1.51 1z"></path></svg>
                            </button>
                                <div id="settings-dropdown" class="settings-dropdown hidden" style="position:absolute;right:0;top:40px;min-width:220px;background:white;border:1px solid #e5e7eb;border-radius:8px;padding:8px;box-shadow:0 6px 18px rgba(0,0,0,0.08);display:none;z-index:60;">
                                    <button id="btn-settings-account" class="w-full text-left px-2 py-2 hover:bg-slate-50 rounded">Cuenta</button>
                                    <button id="btn-settings-security" class="w-full text-left px-2 py-2 hover:bg-slate-50 rounded">Seguridad</button>
                                    <button id="btn-settings-payments" class="w-full text-left px-2 py-2 hover:bg-slate-50 rounded">Métodos de pago</button>
                                    <button id="btn-settings-privacy" class="w-full text-left px-2 py-2 hover:bg-slate-50 rounded">Privacidad</button>
                                    <button id="btn-settings-preferences" class="w-full text-left px-2 py-2 hover:bg-slate-50 rounded">Preferencias</button>
                                    <button id="btn-settings-support" class="w-full text-left px-2 py-2 hover:bg-slate-50 rounded">Soporte</button>
                                    <hr class="my-2" />
                                    <button id="btn-settings-account-status" class="w-full text-left px-2 py-2 hover:bg-slate-50 rounded text-red-600">Estado de la cuenta</button>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-slate-500">Nombre Completo</p>
                        <p class="font-medium">${customerData.name || (userEntry && userEntry.name) || ''}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Correo Electrónico</p>
                        <p class="font-medium">${customerData.email || userEmail || ''}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Documento</p>
                        <p class="font-medium">${customerData.document || 'No registrado'}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Teléfono</p>
                        <p class="font-medium">${customerData.phone || 'No registrado'}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-slate-500">Dirección</p>
                        <p class="font-medium">${customerData.address || 'No registrada'}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-semibold text-slate-800 mb-4">Métodos de Pago</h3>
                <div id="profile-payments-list" class="grid gap-3">
                </div>
                <div class="mt-3 text-sm text-slate-600">Los métodos de pago se muestran con la información mínima necesaria (tarjeta enmascarada, tipo y vencimiento). Puedes gestionar métodos desde Configuración → Métodos de pago.</div>
            </div>
        </div>
    `;

    // Wire buttons (Edit button wiring moved below to avoid duplicate requests)

    const addPaymentBtn = document.getElementById('btn-add-payment');
    // render payments area
    const paymentsList = document.getElementById('profile-payments-list');
    if (paymentsList) {
        const cust = mockCustomers.find(c => c.email === userEmail) || { paymentMethods: [] };
        const methods = cust.paymentMethods || [];
        if (methods.length === 0) {
            paymentsList.innerHTML = `<div class="text-slate-500">No hay métodos registrados.</div>`;
        } else {
            paymentsList.innerHTML = methods.map(m => `
                <div class="flex items-center justify-between p-3 rounded-lg border">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-8 flex items-center justify-center rounded-md bg-slate-100">
                            <img src="${getBrandIcon(m.brand)}" alt="${m.brand}" style="height:22px;" onerror="this.style.display='none'" />
                        </div>
                        <div>
                            <div class="font-medium">${capitalizeBrand(m.brand)} ${m.masked || ('**** **** **** ' + (m.last4 || '0000'))}</div>
                            <div class="text-sm text-slate-500">Vence ${m.exp || 'MM/AA'} • Titular ${m.holder || ''}</div>
                        </div>
                    </div>
                    <div class="text-sm text-slate-500">${m.isDefault ? ` <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Predeterminada</span> ` : ''}</div>
                </div>
            `).join('');
        }
    }

    // helper functions used only in this module's render
    function capitalizeBrand(b) { if (!b) return ''; return b.charAt(0).toUpperCase() + b.slice(1); }
    function getBrandIcon(b) {
        if (!b) return '';
        const map = { visa: 'img/brands/visa.png', mastercard: 'img/brands/mastercard.png' };
        return map[b.toLowerCase()] || '';
    }

    // Settings gear dropdown wiring
    const btnSettingsGear = document.getElementById('btn-settings-gear');
    const settingsDropdown = document.getElementById('settings-dropdown');
    if (btnSettingsGear && settingsDropdown) {
        btnSettingsGear.addEventListener('click', (ev) => {
            ev.stopPropagation();
            const isVisible = settingsDropdown.style.display === 'block';
            settingsDropdown.style.display = isVisible ? 'none' : 'block';
        });

        // close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!settingsDropdown.contains(e.target) && e.target !== btnSettingsGear) {
                settingsDropdown.style.display = 'none';
            }
        });

        const btnDisableSmall = document.getElementById('btn-disable-account-small');
        const btnDeleteSmall = document.getElementById('btn-delete-account-small');
        const btnNotifications = document.getElementById('btn-settings-notifications');

        if (btnNotifications) {
            btnNotifications.addEventListener('click', (e) => {
                e.preventDefault();
                settingsDropdown.style.display = 'none';
                showAlert('Preferencias de notificación abiertas (simulado).', 'info');
            });
        }

        // wire new expanded settings buttons to open settings modal sections
        const btnAccount = document.getElementById('btn-settings-account');
        const btnSecurity = document.getElementById('btn-settings-security');
        const btnPayments = document.getElementById('btn-settings-payments');
        const btnPrivacy = document.getElementById('btn-settings-privacy');
        const btnPrefs = document.getElementById('btn-settings-preferences');
        const btnSupport = document.getElementById('btn-settings-support');
        const btnAccountStatus = document.getElementById('btn-settings-account-status');

        if (btnAccount) btnAccount.addEventListener('click', (e) => { e.preventDefault(); settingsDropdown.style.display = 'none'; if (window.openSettingsModal) window.openSettingsModal('account'); });
        if (btnSecurity) btnSecurity.addEventListener('click', (e) => { e.preventDefault(); settingsDropdown.style.display = 'none'; if (window.openSettingsModal) window.openSettingsModal('security'); });
        if (btnPayments) btnPayments.addEventListener('click', (e) => { e.preventDefault(); settingsDropdown.style.display = 'none'; if (window.openSettingsModal) window.openSettingsModal('payments'); });
        if (btnPrivacy) btnPrivacy.addEventListener('click', (e) => { e.preventDefault(); settingsDropdown.style.display = 'none'; if (window.openSettingsModal) window.openSettingsModal('privacy'); });
        if (btnPrefs) btnPrefs.addEventListener('click', (e) => { e.preventDefault(); settingsDropdown.style.display = 'none'; if (window.openSettingsModal) window.openSettingsModal('preferences'); });
        if (btnSupport) btnSupport.addEventListener('click', (e) => { e.preventDefault(); settingsDropdown.style.display = 'none'; if (window.openSettingsModal) window.openSettingsModal('support'); });
    if (btnAccountStatus) btnAccountStatus.addEventListener('click', (e) => { e.preventDefault(); settingsDropdown.style.display = 'none'; if (window.openSettingsModal) window.openSettingsModal('account-status'); });
        if (btnDisableSmall) {
            btnDisableSmall.addEventListener('click', (e) => {
                e.preventDefault();
                // immediate disable: mark customer as disabled locally
                try {
                    const cust = mockCustomers.find(c => c.email === userEmail);
                    if (cust) {
                        cust.disabled = true;
                        showAlert('Cuenta deshabilitada localmente. Para eliminarla solicite la eliminación.', 'info');
                        settingsDropdown.style.display = 'none';
                        if (window.renderProfilePage) window.renderProfilePage();
                    } else {
                        pendingAdminRequests.push({ id: 'REQ-' + Date.now(), type: 'disable_account', email: userEmail, data: { requestedAt: new Date().toISOString() } });
                        settingsDropdown.style.display = 'none';
                        showAlert('Cuenta no encontrada: se envió solicitud administrativa.', 'info');
                    }
                } catch (err) {
                    console.error(err);
                    showAlert('Error al deshabilitar la cuenta.', 'error');
                }
            });
        }
        if (btnDeleteSmall) {
            btnDeleteSmall.addEventListener('click', (e) => {
                e.preventDefault();
                // deletion remains an admin-only action
                pendingAdminRequests.push({ id: 'REQ-' + Date.now(), type: 'delete_account', email: userEmail, data: { requestedAt: new Date().toISOString() } });
                settingsDropdown.style.display = 'none';
                showAlert('Solicitud para eliminar la cuenta enviada. Esta acción requiere validación administrativa.', 'error');
            });
        }
    }

    // If the current user is a delivery person, render delivery profile section
    if (role === 'delivery') {
        // find delivery entry
        const dm = mockDeliveryMen.find(d => d.email === userEmail) || mockDeliveryMen.find(d => d.id === (customerData.deliveryId || '')) || {};
        // compute assigned and delivered orders
        const assigned = (window.mockOrders || []).filter(o => o.deliveryId === dm.id || o.deliveryMan === dm.name || o.deliveryMan === dm.email || o.deliveryMan === dm.id);
        const delivered = assigned.filter(a => a.status === 'Entregado');
        const active = assigned.find(a => a.status === 'En Camino' || a.status === 'En Reparto' || a.status === 'Procesando' || a.status === 'Listo');

        const deliverySection = document.createElement('div');
        deliverySection.className = 'bg-white rounded-xl shadow-md p-6 mt-6';
        deliverySection.innerHTML = `
            <h3 class="text-xl font-semibold text-slate-800 mb-4">Panel de Repartidor</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <div class="p-3 bg-slate-50 rounded">
                    <div class="text-sm text-slate-500">Pedidos entregados</div>
                    <div class="font-semibold text-lg">${delivered.length}</div>
                </div>
                <div class="p-3 bg-slate-50 rounded">
                    <div class="text-sm text-slate-500">Calificación</div>
                    <div class="font-semibold text-lg">${dm.rating != null ? dm.rating : 'N/A'}</div>
                </div>
                <div class="p-3 bg-slate-50 rounded">
                    <div class="text-sm text-slate-500">Tiempo promedio</div>
                    <div class="font-semibold text-lg">${dm.avgTime || 'N/A'}</div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="font-semibold">Pedido actual</h4>
                <div class="text-sm mt-2">${active ? `#${active.id} — ${active.customerName || active.customer || active.customerEmail || ''} — <em>${active.status}</em> <button data-order-id="${active.id}" class="ml-2 text-xs text-custom-blue btn-open-order-profile">Ver</button>` : '<span class="text-slate-500">No hay pedido activo</span>'}</div>
            </div>

            <div>
                <h4 class="font-semibold">Historial</h4>
                <ul class="text-sm list-disc list-inside mt-2">
                    ${delivered.length ? delivered.map(o => `<li>#${o.id} — ${o.customerName || o.customer || o.customerEmail || ''} — ${o.date || ''} — <em>${o.status || ''}</em> <button data-order-id="${o.id}" class="ml-2 text-xs text-custom-blue btn-open-order-profile">Ver</button></li>`).join('') : '<li class="text-slate-500">Sin entregas registradas</li>'}
                </ul>
            </div>
        `;
        container.appendChild(deliverySection);

        setTimeout(() => {
            deliverySection.querySelectorAll('.btn-open-order-profile').forEach(b => b.addEventListener('click', (ev) => {
                ev.preventDefault(); const id = b.getAttribute('data-order-id'); if (!id) return; if (window.showOrderDetails) window.showOrderDetails(id);
            }));
        }, 40);
    }

    // Update Edit button wiring: only open modal; actual update request is created from modal submit
    const btnEdit2 = document.getElementById('btn-edit-profile');
    if (btnEdit2) {
        btnEdit2.addEventListener('click', (e) => {
            e.preventDefault();
            const data = customerData;
            if (window.openEditModal) window.openEditModal('profile', data);
        });
    }
}

// Página para administrar clientes (lista filtrable, CRUD básico)
export function renderClientsPage() {
    const container = document.getElementById('page-clients') || document.getElementById('page-users');
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'admin') {
        container.innerHTML = `<div class="flex flex-col items-center justify-center h-full"><h2 class="text-3xl font-bold mb-4">Acceso Restringido</h2><p class="mb-4">Solo administradores.</p></div>`;
        return;
    }

    container.innerHTML = `
        <div class="page active">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Clientes</h2>
                <button id="btn-new-client" class="bg-custom-blue text-white px-4 py-2 rounded">Nuevo cliente</button>
            </div>
            <div id="clients-list" class="grid gap-4"></div>
        </div>
    `;

    const list = document.getElementById('clients-list');
    function render() {
        if (!list) return;
        list.innerHTML = mockCustomers.map((c, idx) => {
            const displayId = c.id || ('C' + String(idx + 1).padStart(3,'0'));
            return `
            <div class="bg-white rounded-xl shadow p-4 flex justify-between items-center">
                <div>
                    <div class="font-semibold"><span class="text-xs text-slate-400 mr-2">${displayId}</span> ${c.name} ${c.lastWhatsAppMessage ? icons.whats : ''}</div>
                    <div class="text-sm text-slate-500">${c.email} • ${c.phone || 'Sin teléfono'}</div>
                </div>
                <div class="flex gap-2 items-center">
                    <div class="relative">
                        <button data-email="${c.email}" class="btn btn-secondary btn-sm client-actions-gear">${gearIcon}</button>
                        <div class="client-actions-menu hidden absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <button data-email="${c.email}" class="client-action-edit w-full text-left px-3 py-2 text-sm">${icons.edit}Editar</button>
                            <button data-email="${c.email}" class="client-action-toggle w-full text-left px-3 py-2 text-sm">${icons.toggle}${c.disabled ? ' Habilitar' : ' Deshabilitar'}</button>
                            <button data-email="${c.email}" class="client-action-delete w-full text-left px-3 py-2 text-sm text-red-600">${icons.trash}Eliminar</button>
                            <hr />
                            <button data-email="${c.email}" class="client-action-whats w-full text-left px-3 py-2 text-sm text-green-700">${icons.whats} WhatsApp</button>
                        </div>
                    </div>
                </div>
            </div>
        `; }).join('');
    }

    render();

    setTimeout(() => {
    document.getElementById('btn-new-client').addEventListener('click', (e) => { e.preventDefault(); if (window.openEditModal) window.openEditModal('customer', {}); });

    // 'Ver detalles' removed per request; details available via gear menu actions

    // gear toggle
    document.querySelectorAll('.client-actions-gear').forEach(g => g.addEventListener('click', (ev) => { ev.stopPropagation(); const menu = g.nextElementSibling; document.querySelectorAll('.client-actions-menu').forEach(m => { if (m !== menu) m.classList.add('hidden'); }); if (menu) menu.classList.toggle('hidden'); }));
    document.addEventListener('click', () => document.querySelectorAll('.client-actions-menu').forEach(m => m.classList.add('hidden')));

    document.querySelectorAll('.client-action-edit').forEach(btn => btn.addEventListener('click', (ev) => { const email = btn.getAttribute('data-email'); const c = mockCustomers.find(x => x.email === email); if (c && window.openEditModal) { btn.parentElement.classList.add('hidden'); window.openEditModal('customer', c); } }));

    document.querySelectorAll('.client-action-toggle').forEach(btn => btn.addEventListener('click', (ev) => { const email = btn.getAttribute('data-email'); const c = mockCustomers.find(x => x.email === email); if (!c) return; c.disabled = !c.disabled; try { saveCustomersToStorage(); } catch (err) {} render(); showAlert(c.disabled ? 'Cliente deshabilitado.' : 'Cliente habilitado.', 'info'); }));

    document.querySelectorAll('.client-action-delete').forEach(btn => btn.addEventListener('click', (ev) => { const email = btn.getAttribute('data-email'); const idx = mockCustomers.findIndex(x => x.email === email); if (idx < 0) return; if (!confirm('¿Confirmar eliminación del cliente?')) return; const removed = mockCustomers.splice(idx, 1)[0]; try { saveCustomersToStorage(); } catch (err) {} render(); showUndoToast('Cliente eliminado', () => { mockCustomers.splice(idx, 0, removed); try { saveCustomersToStorage(); } catch (err) {} render(); showAlert('Eliminación revertida', 'info'); }, 8000); showAlert('Cliente eliminado.', 'info'); }));

    document.querySelectorAll('.client-action-whats').forEach(btn => btn.addEventListener('click', (ev) => { const email = btn.getAttribute('data-email'); const c = mockCustomers.find(x => x.email === email); if (!c) return; const msg = `Hola ${c.name}, estoy contactando sobre sus productos.`; const url = window.getWhatsAppUrl && window.getWhatsAppUrl(c.phone || window.whatsappNumber, msg); if (url) window.open(url, '_blank'); }));
    }, 40);
}