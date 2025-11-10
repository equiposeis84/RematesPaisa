/**
 * modals.js
 *
 * Descripción:
 * Contiene funciones para abrir y gestionar los modales de la aplicación:
 * - edición de perfil, productos, pedidos, repartidores y proveedores
 * - modales de revalidación de sesión y formularios de acción
 *
 * Cada modal está renderizado en HTML desde plantillas incluidas y usa
 * los datos mock definidos en `constants.js` cuando el backend no está
 * disponible.
 */
// --- FUNCIONES DE MODALES ---
import { mockProducts, mockCustomers, mockProviders, mockDeliveryMen, mockUsers, pendingAdminRequests, saveProductsToStorage, saveCustomersToStorage, saveProvidersToStorage, saveDeliveryMenToStorage, saveOrdersToStorage, saveProvidersSafe, saveDeliveryMenSafe, getWhatsAppUrl, icons, getCurrentUserRole, normalizeImagePath } from './constants.js';
import { formatCurrency, showAlert } from './utils.js';

/*
Archivo: js/modules/modals.js
Descripción: Manejo de modales y formularios emergentes en la UI.
Explicación: Exporta funciones para abrir/cerrar modales, renderizar contenido dinámico (editar producto, ver pedido, etc.).
Partes importantes: asegurarse de limpiar listeners al cerrar modales para evitar duplicaciones.
*/

// small helpers
function capitalizeBrand(b) { if (!b) return ''; return b.charAt(0).toUpperCase() + b.slice(1); }
function getBrandIcon(b) {
    if (!b) return '';
    const map = { visa: 'img/brands/visa.png', mastercard: 'img/brands/mastercard.png', amex: 'img/brands/amex.png' };
    return map[b.toLowerCase()] || '';
}

// Focus the first interactive element in the modal and scroll it into view for better UX
function focusModalFirstInput() {
    try {
        setTimeout(() => {
            const modal = document.getElementById('edit-modal');
            if (!modal) return;
            const el = modal.querySelector('input, textarea, select, button:not(.btn-secondary)');
            if (el && typeof el.focus === 'function') {
                try { el.focus(); } catch (e) { /* ignore */ }
                try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (e) { /* ignore */ }
            }
        }, 80);
    } catch (err) { /* noop */ }
}

// Simple loading state for save buttons (adds spinner and disables button)
function setButtonLoading(btn, isLoading, label) {
    if (!btn) return;
    try {
        if (isLoading) {
            if (!btn.dataset._orig) btn.dataset._orig = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-wait');
            btn.innerHTML = `<span class="inline-block w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin" style="vertical-align:middle"></span>${label || 'Guardando...'} `;
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-70', 'cursor-wait');
            if (btn.dataset._orig) { btn.innerHTML = btn.dataset._orig; delete btn.dataset._orig; }
        }
    } catch (e) { /* noop */ }
}

/**
 * Cierra un modal por id.
 * @param {string} modalId - Id del modal a cerrar.
 */
export function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

/**
 * Abre un modal para revalidar la sesión del usuario.
 * @param {string} intendedPage - Página destino después de validar.
 */
export function openRevalidateModal(intendedPage) {
    const modal = document.getElementById('edit-modal');
    if (!modal) return;
    modal.classList.add('active');
    const wrapper = modal.querySelector('.bg-white') || modal.querySelector('#edit-modal-content') || modal;
    const currentEmail = (typeof getCurrentUserEmail === 'function') ? getCurrentUserEmail() : null;
    wrapper.innerHTML = `
        <h3 class="text-xl font-bold mb-2">Validación de sesión</h3>
        <p class="text-sm text-slate-600 mb-4">Por seguridad, ingresa tu contraseña para continuar.</p>
        <form id="reval-form">
            <label class="text-sm">Usuario</label>
            <input type="text" id="reval-email" class="border p-2 mb-2 w-full" value="${currentEmail || ''}" readonly />
            <label class="text-sm">Contraseña</label>
            <input type="password" id="reval-pass" class="border p-2 mb-3 w-full" required />
            <div class="flex gap-2">
                <button id="reval-submit" class="bg-custom-blue text-white px-4 py-2 rounded">Validar</button>
                <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-200 px-4 py-2 rounded">Cancelar</button>
            </div>
        </form>
    `;

    setTimeout(() => {
        const form = document.getElementById('reval-form');
        if (!form) return;
        form.onsubmit = function(e) {
            e.preventDefault();
            const email = document.getElementById('reval-email').value.trim();
            const pass = document.getElementById('reval-pass').value.trim();
            const user = mockUsers[email];
            if (user && user.password === pass) {
                // success: close modal and navigate to intended page
                closeModal('edit-modal');
                if (intendedPage && window.navigateTo) window.navigateTo(intendedPage);
                if (window.showAlert) window.showAlert('Validación correcta', 'success');
            } else {
                if (window.showAlert) window.showAlert('Contraseña incorrecta', 'error');
            }
        };
    }, 20);
}

/**
 * Abre el modal de edición para el tipo especificado (profile, product, order, delivery).
 * @param {string} type - Tipo de edición ('profile'|'product'|'order'|'delivery' etc.).
 * @param {Object} item - Objeto de datos a editar (puede ser null para nueva entidad).
 */
export function openEditModal(type, item) {
    const modal = document.getElementById('edit-modal');
    if (!modal) return;
    modal.classList.add('active');
    // wrapper is the inner content container used by varias branches
    const wrapper = modal.querySelector('.bg-white') || modal.querySelector('#edit-modal-content') || modal;
    if (type === 'profile' || type === 'customer') {
        wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">Editar Perfil</h3>
            <form id="edit-form">
                <label class="block text-sm text-slate-600">Nombre completo</label>
                <input type="text" id="edit-name" value="${item.name || ''}" class="border p-2 mb-2 w-full" placeholder="Nombre" />

                <label class="block text-sm text-slate-600">Correo electrónico</label>
                <input type="email" id="edit-email" value="${item.email || ''}" class="border p-2 mb-2 w-full" placeholder="correo@ejemplo.com" />

                <label class="block text-sm text-slate-600">Documento</label>
                <input type="text" id="edit-document" value="${item.document || ''}" class="border p-2 mb-2 w-full" placeholder="Número de documento" />

                <label class="block text-sm text-slate-600">Teléfono</label>
                <input type="text" id="edit-phone" value="${item.phone || ''}" class="border p-2 mb-2 w-full" placeholder="Teléfono" />

                <label class="block text-sm text-slate-600">Dirección</label>
                <input type="text" id="edit-address" value="${item.address || ''}" class="border p-2 mb-2 w-full" placeholder="Dirección" />

                <label class="block text-sm text-slate-600">Método de pago (descripción)</label>
                <input type="text" id="edit-payment" value="" class="border p-2 mb-2 w-full" placeholder="Ej: Visa ****1234" />

                <div class="mt-3 flex items-center gap-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Enviar solicitud</button>
                    <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </form>
        `;

        const form = document.getElementById('edit-form');
        if (form) {
            form.onsubmit = function(e) {
                e.preventDefault();
                const payload = {
                    name: document.getElementById('edit-name').value,
                    email: document.getElementById('edit-email').value,
                    document: document.getElementById('edit-document').value,
                    phone: document.getElementById('edit-phone').value,
                    address: document.getElementById('edit-address').value,
                    paymentMethod: document.getElementById('edit-payment').value,
                    requestedAt: new Date().toISOString()
                };
                try {
                    const oldEmail = item && item.email ? item.email : null;
                    // Ensure a customer entry exists and update it
                    let cust = mockCustomers.find(c => c.email === (oldEmail || payload.email));
                    if (!cust && payload.email) {
                        const newId = 'C' + (mockCustomers.length + 1).toString().padStart(3, '0');
                        cust = { id: newId, name: payload.name, email: payload.email, phone: payload.phone || '', address: payload.address || '' };
                        mockCustomers.push(cust);
                    }
                    if (cust) {
                        cust.name = payload.name || cust.name;
                        cust.email = payload.email || cust.email;
                        cust.document = payload.document || cust.document;
                        cust.phone = payload.phone || cust.phone;
                        cust.address = payload.address || cust.address;
                    }

                    // If email changed, update mockUsers mapping and session helper
                    if (oldEmail && payload.email && oldEmail !== payload.email) {
                        if (mockUsers[oldEmail]) {
                            mockUsers[payload.email] = Object.assign({}, mockUsers[oldEmail]);
                            delete mockUsers[oldEmail];
                        }
                        if (typeof setCurrentUserEmail === 'function') setCurrentUserEmail(payload.email);
                    }

                    try { if (typeof saveCustomersToStorage === 'function') saveCustomersToStorage(); } catch (e) { /* noop */ }
                    closeModal('edit-modal');
                    showAlert('Perfil actualizado correctamente.', 'success');
                    if (window.renderProfilePage) window.renderProfilePage();
                } catch (err) {
                    console.error('Error applying profile update', err);
                    showAlert('Ocurrió un error al actualizar el perfil.', 'error');
                }
            };
        }
        return;
    }

    // fallback: product editor
    if (type === 'product') {
    wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">${item && item.id ? 'Editar Producto' : 'Nuevo Producto'}</h3>
            <form id="product-form" class="grid gap-2">
                <label class="text-sm">Nombre</label>
                <input id="prod-name" class="border p-2" value="${item.name || ''}" />
                <label class="text-sm">Imagen (ruta o archivo)</label>
                <input id="prod-image" class="border p-2" value="${item.image || ''}" placeholder="img/ejemplo.jpg" />
                <input id="prod-image-file" type="file" accept="image/*" class="mt-2" />
                <label class="text-sm">Categoría</label>
                <input id="prod-category" class="border p-2" value="${item.category || ''}" />
                <label class="text-sm">Precio</label>
                <input id="prod-price" class="border p-2" value="${item.price || ''}" />
                <label class="text-sm">Stock</label>
                <input id="prod-stock" class="border p-2" value="${item.stock || ''}" />
                <label class="text-sm">Descripción</label>
                <textarea id="prod-desc" class="border p-2">${item.description || ''}</textarea>
                <div class="flex gap-2 mt-2">
                    <button id="btn-save-product" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                    <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </form>
        `;

        const saveBtn = document.getElementById('btn-save-product');
        if (saveBtn) saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            setButtonLoading(saveBtn, true, 'Guardando...');
            const name = document.getElementById('prod-name').value.trim();
            const image = document.getElementById('prod-image').value.trim();
            const imageFileInput = document.getElementById('prod-image-file');
            const category = document.getElementById('prod-category').value.trim();
            const price = Number(document.getElementById('prod-price').value) || 0;
            const stock = Number(document.getElementById('prod-stock').value) || 0;
            const description = document.getElementById('prod-desc').value.trim();
            try {
                async function finalizeSave(finalImage) {
                    if (item && item.id) {
                        // update existing
                        const p = mockProducts.find(x => String(x.id) === String(item.id));
                        if (p) {
                            p.name = name; p.image = finalImage; p.category = category; p.price = price; p.stock = stock; p.description = description;
                            showAlert('Producto actualizado.', 'success');
                        }
                    } else {
                        // new product
                        const newId = (mockProducts.length ? Math.max(...mockProducts.map(x => Number(x.id))) + 1 : 1);
                        mockProducts.push({ id: newId, name, image: finalImage, category, price, stock, description });
                        showAlert('Producto creado.', 'success');
                    }
                    try { saveProductsToStorage(); } catch (err) { /* noop */ }
                    showAlert('Producto guardado.', 'success');
                    await new Promise(r => setTimeout(r, 120));
                    closeModal('edit-modal');
                    if (window.renderInventoryPage) window.renderInventoryPage();
                    setButtonLoading(saveBtn, false);
                }

                if (imageFileInput && imageFileInput.files && imageFileInput.files.length) {
                    const file = imageFileInput.files[0];
                    const reader = new FileReader();
                    reader.onload = function(ev) { finalizeSave(ev.target.result); };
                    reader.onerror = function() { showAlert('No se pudo leer la imagen seleccionada.', 'error'); finalizeSave(image || ''); };
                    reader.readAsDataURL(file);
                } else {
                    finalizeSave(image || '');
                }
            } catch (err) {
                console.error(err);
                showAlert('Error guardando el producto.', 'error');
            }
        });
        return;
    }

    // Order editor
    if (type === 'order') {
        const o = item || {};
        // build a simple form to change status and assign repartidor
        const deliveryOptions = (mockDeliveryMen || []).map(d => `<option value="${d.id}">${d.name} (${d.email || d.phone || d.id})</option>`).join('');
        wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">Editar Pedido ${o.id || ''}</h3>
            <form id="order-edit-form" class="grid gap-2">
                <label class="text-sm">Estado</label>
                <select id="order-status" class="border p-2">
                    <option value="Pendiente" ${o.status === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                    <option value="Enviado" ${o.status === 'Enviado' ? 'selected' : ''}>Enviado</option>
                    <option value="Entregado" ${o.status === 'Entregado' ? 'selected' : ''}>Entregado</option>
                    <option value="Cancelado" ${o.status === 'Cancelado' ? 'selected' : ''}>Cancelado</option>
                </select>
                <label class="text-sm">Asignar repartidor</label>
                <select id="order-delivery" class="border p-2">
                    <option value="">-- Ninguno --</option>
                    ${deliveryOptions}
                </select>
                <div class="flex gap-2 mt-2">
                    <button id="btn-save-order" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                    <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </form>
        `;

        const saveBtn = document.getElementById('btn-save-order');
        if (saveBtn) saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            setButtonLoading(saveBtn, true, 'Guardando...');
            const status = document.getElementById('order-status').value;
            const deliveryId = document.getElementById('order-delivery').value;
            try {
                const existing = mockOrders.find(x => x.id === o.id);
                if (existing) {
                    existing.status = status;
                    if (deliveryId) {
                        const d = mockDeliveryMen.find(dd => dd.id === deliveryId);
                        existing.deliveryMan = d ? d.name : deliveryId;
                        existing.deliveryId = deliveryId;
                    } else {
                        existing.deliveryMan = existing.deliveryMan || '';
                        existing.deliveryId = null;
                    }
                }
                try { if (typeof saveOrdersToStorage === 'function') await saveOrdersToStorage(); } catch (err) { /* noop */ }
                showAlert('Pedido actualizado.', 'success');
                await new Promise(r => setTimeout(r, 120));
                closeModal('edit-modal');
                if (window.renderOrdersPage) window.renderOrdersPage();
                if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage();
            } catch (err) {
                console.error(err);
                showAlert('Error guardando pedido.', 'error');
            }
            setButtonLoading(saveBtn, false);
        });

        focusModalFirstInput();
        return;
    }

    // Delivery editor
    if (type === 'delivery') {
        const d = item || {};
        wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">${d && d.id ? 'Editar Repartidor' : 'Nuevo Repartidor'}</h3>
            <form id="delivery-form" class="grid gap-2">
                <label class="text-sm">Nombre</label>
                <input id="delivery-name" class="border p-2" value="${d.name || ''}" />
                <label class="text-sm">Email</label>
                <input id="delivery-email" class="border p-2" value="${d.email || ''}" />
                <label class="text-sm">Teléfono</label>
                <input id="delivery-phone" class="border p-2" value="${d.phone || ''}" />
                <label class="text-sm">Vehículo</label>
                <input id="delivery-vehicle" class="border p-2" value="${d.vehicle || ''}" />
                <label class="text-sm">Placa</label>
                <input id="delivery-plate" class="border p-2" value="${d.licensePlate || ''}" />
                <label class="text-sm">Avatar (URL)</label>
                <input id="delivery-avatar" class="border p-2" value="${d.avatar || ''}" placeholder="https://..." />
                <label class="text-sm">Calificación (0-5)</label>
                <input id="delivery-rating" type="number" min="0" max="5" step="0.1" class="border p-2" value="${d.rating != null ? d.rating : ''}" />
                <label class="text-sm">Disponibilidad</label>
                <select id="delivery-availability" class="border p-2">
                    <option value="available" ${d.availability === 'available' ? 'selected' : ''}>Disponible</option>
                    <option value="busy" ${d.availability === 'busy' ? 'selected' : ''}>Ocupado</option>
                    <option value="offline" ${d.availability === 'offline' ? 'selected' : ''}>Offline</option>
                </select>
                <label class="text-sm">Notas internas</label>
                <textarea id="delivery-notes" class="border p-2">${d.notes || ''}</textarea>
                <label class="text-sm">Estado</label>
                <select id="delivery-status" class="border p-2">
                    <option value="Activo" ${d.status === 'Activo' ? 'selected' : ''}>Activo</option>
                    <option value="Inactivo" ${d.status === 'Inactivo' ? 'selected' : ''}>Inactivo</option>
                </select>
                <div class="flex gap-2 mt-2">
                    <button id="btn-save-delivery" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                    <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </form>
        `;

        const saveBtn = document.getElementById('btn-save-delivery');
        if (saveBtn) saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            setButtonLoading(saveBtn, true, 'Guardando...');
                const name = document.getElementById('delivery-name').value.trim();
            const email = document.getElementById('delivery-email').value.trim();
            const phone = document.getElementById('delivery-phone').value.trim();
            const vehicle = document.getElementById('delivery-vehicle').value.trim();
            const plate = document.getElementById('delivery-plate').value.trim();
            const avatar = document.getElementById('delivery-avatar').value.trim();
            const rating = parseFloat(document.getElementById('delivery-rating').value) || 0;
            const availability = document.getElementById('delivery-availability').value;
            const notes = document.getElementById('delivery-notes').value.trim();
            const status = document.getElementById('delivery-status').value;
            try {
                if (d && d.id) {
                    const existing = mockDeliveryMen.find(x => x.id === d.id);
                        if (existing) {
                            existing.name = name; existing.email = email; existing.phone = phone; existing.vehicle = vehicle; existing.licensePlate = plate; existing.status = status;
                            existing.avatar = avatar || existing.avatar;
                            existing.rating = isNaN(rating) ? existing.rating : rating;
                            existing.availability = availability || existing.availability;
                            existing.notes = notes || existing.notes;
                            existing.lastUpdated = new Date().toISOString();
                        showAlert('Repartidor actualizado.', 'success');
                    }
                } else {
                    // create new id D + 3 digits
                    const nextIdNum = mockDeliveryMen.length ? Math.max(...mockDeliveryMen.map(x => Number(x.id.replace(/\D/g, '') || 0))) + 1 : 1;
                    const newId = 'D' + String(nextIdNum).padStart(3, '0');
                    mockDeliveryMen.push({ id: newId, name, email, phone, vehicle, licensePlate: plate, status, avatar: avatar || '', rating: isNaN(rating) ? 0 : rating, availability: availability || 'available', notes: notes || '' });
                    showAlert('Repartidor creado.', 'success');
                }
        try { if (window.saveDeliveryMenSafe) await window.saveDeliveryMenSafe(); else if (typeof saveDeliveryMenSafe === 'function') await saveDeliveryMenSafe(); } catch (err) { /* noop */ }
        showAlert('Repartidor guardado.', 'success');
        await new Promise(r => setTimeout(r, 120));
        closeModal('edit-modal');
        if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage();
        setButtonLoading(saveBtn, false);
            } catch (err) {
                console.error(err); showAlert('Error guardando repartidor.', 'error');
            }
        });
    focusModalFirstInput();
    return;
    }

    // Provider editor
    if (type === 'provider' || type === 'providers') {
        const p = item || {};
        wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">${p && p.id ? 'Editar Proveedor' : 'Nuevo Proveedor'}</h3>
            <form id="provider-form" class="grid gap-2">
                <label class="text-sm">Nombre</label>
                <input id="prov-name" class="border p-2" value="${p.name || ''}" />
                <label class="text-sm">Contacto</label>
                <input id="prov-contact" class="border p-2" value="${p.contact || ''}" />
                <label class="text-sm">Teléfono</label>
                <input id="prov-phone" class="border p-2" value="${p.phone || ''}" />
                <label class="text-sm">Email</label>
                <input id="prov-email" class="border p-2" value="${p.email || ''}" />
                <label class="text-sm">Dirección</label>
                <input id="prov-address" class="border p-2" value="${p.address || ''}" />
                <label class="text-sm">Productos / Servicios (coma separada)</label>
                <input id="prov-products" class="border p-2" value="${Array.isArray(p.products) ? p.products.join(', ') : (p.products || '')}" />
                <div class="flex gap-2 mt-2">
                    <button id="btn-save-provider" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                    <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </form>
        `;

        const saveBtn = document.getElementById('btn-save-provider');
        if (saveBtn) saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            setButtonLoading(saveBtn, true, 'Guardando...');
            const name = document.getElementById('prov-name').value.trim();
            const contact = document.getElementById('prov-contact').value.trim();
            const phone = document.getElementById('prov-phone').value.trim();
            const email = document.getElementById('prov-email').value.trim();
            const products = document.getElementById('prov-products').value.split(',').map(s => s.trim()).filter(Boolean);
            try {
                    if (p && p.id) {
                        const existing = mockProviders.find(x => x.id === p.id);
                        if (existing) { existing.name = name; existing.contact = contact; existing.phone = phone; existing.email = email; existing.address = document.getElementById('prov-address').value.trim(); existing.products = products; }
                } else {
                        const nextIdNum = mockProviders.length ? Math.max(...mockProviders.map(x => Number(x.id.replace(/\D/g, '') || 0))) + 1 : 1;
                        const newId = 'P' + String(nextIdNum).padStart(3, '0');
                        mockProviders.push({ id: newId, name, contact, phone, email, address: document.getElementById('prov-address').value.trim(), products });
                }
        try { if (typeof saveProvidersSafe === 'function') await saveProvidersSafe(); } catch (err) { /* noop */ }
    // debug: saveProvidersSafe called for provider (silent in prod)
        showAlert('Proveedor guardado.', 'success');
        await new Promise(r => setTimeout(r, 120));
        closeModal('edit-modal');
        if (window.renderProvidersPage) window.renderProvidersPage();
        setButtonLoading(saveBtn, false);
            } catch (err) {
                console.error(err); showAlert('Error guardando proveedor.', 'error');
            }
        });
    focusModalFirstInput();
    return;
    }
}

export function saveProductChanges() { /* ... */ }
export function saveCustomerChanges() { /* ... */ }
export function saveDeliveryChanges() { /* ... */ }
export function saveProfileChanges() { /* ... */ }

// Abrir modal de producto con detalles
export function openProductModal(productId) {
    const product = mockProducts.find(p => p.id === productId);
    if (!product) return;
    const modal = document.getElementById('product-modal');
    const content = document.getElementById('product-modal-content');
    if (!modal || !content) return;
    content.innerHTML = `
        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="md:w-1/2">
                    <img src="${normalizeImagePath(product.image)}" alt="${product.name}" class="w-full h-64 object-cover rounded-lg">
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-2xl font-bold mb-2">${product.name}</h2>
                    <p class="text-gray-600 mb-4">${product.description || ''}</p>
                    <p class="text-sm text-gray-500 mb-3">Categoría: <span class="font-medium text-gray-700">${product.category || 'General'}</span></p>
                    <div class="mb-4">
                        <h4 class="font-semibold mb-1">Descripción extendida</h4>
                        <p class="text-sm text-gray-600">${(product.longDescription || product.description || '') + ' ' + (product.features || '')}</p>
                    </div>
                    <p class="text-xl font-semibold text-blue-600 mb-4">${formatCurrency(product.price)}</p>
                    <p class="mb-4 text-sm text-gray-500">Stock: ${product.stock || 0}</p>
                    <div class="flex items-center gap-3">
                        ${ (getCurrentUserRole && getCurrentUserRole() !== 'admin') ? `
                        <button onclick="event.preventDefault(); window.addToCart && window.addToCart(${product.id}); window.closeModal && window.closeModal('product-modal');" class="bg-blue-500 text-white px-4 py-2 rounded">Añadir al Carrito</button>
                        ` : `<div class="text-sm text-slate-500">Vista administrativa — acciones de compra ocultas</div>` }
                        <button onclick="event.preventDefault(); window.closeModal && window.closeModal('product-modal')" class="bg-gray-200 px-4 py-2 rounded">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    modal.classList.add('active');
}

// Abre el modal de configuración con secciones (Cuenta, Seguridad, Pagos, Privacidad, Preferencias, Soporte, Estado)
export function openSettingsModal(section = 'account') {
    const modal = document.getElementById('edit-modal');
    if (!modal) return;
    modal.classList.add('active');
    const wrapper = modal.querySelector('.bg-white') || modal.querySelector('#edit-modal-content') || modal;
    const userEmail = (typeof getCurrentUserEmail === 'function') ? getCurrentUserEmail() : null;
    const customer = mockCustomers.find(c => c.email === userEmail) || {};

    function closeAndRefresh(msg, type = 'info') {
        modal.classList.remove('active');
        if (window.renderProfilePage) window.renderProfilePage();
        if (msg) showAlert(msg, type);
    }

    switch (section) {
        case 'account':
            wrapper.innerHTML = `
                <h3 class="text-xl font-bold mb-2">Cuenta — Editar datos</h3>
                <form id="settings-account-form">
                    <label class="block text-sm text-slate-600">Nombre completo</label>
                    <input type="text" id="settings-name" value="${customer.name || ''}" class="border p-2 mb-2 w-full" />
                    <label class="block text-sm text-slate-600">Correo</label>
                    <input type="email" id="settings-email" value="${customer.email || ''}" class="border p-2 mb-2 w-full" />
                    <label class="block text-sm text-slate-600">Teléfono</label>
                    <input type="text" id="settings-phone" value="${customer.phone || ''}" class="border p-2 mb-2 w-full" />
                    <label class="block text-sm text-slate-600">Dirección</label>
                    <input type="text" id="settings-address" value="${customer.address || ''}" class="border p-2 mb-2 w-full" />
                    <label class="block text-sm text-slate-600">Avatar (URL)</label>
                    <input type="text" id="settings-avatar" value="${customer.avatar || ''}" class="border p-2 mb-2 w-full" placeholder="https://..." />

                    <div class="mt-3 flex items-center gap-2">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                        <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                    </div>
                </form>
            `;

            const accForm = document.getElementById('settings-account-form');
            if (accForm) accForm.onsubmit = function(e) {
                e.preventDefault();
                try {
                    const payload = {
                        name: document.getElementById('settings-name').value,
                        email: document.getElementById('settings-email').value,
                        phone: document.getElementById('settings-phone').value,
                        address: document.getElementById('settings-address').value,
                        avatar: document.getElementById('settings-avatar').value
                    };
                    let cust = mockCustomers.find(c => c.email === userEmail);
                    if (!cust && payload.email) {
                        cust = { id: 'C' + (mockCustomers.length + 1).toString().padStart(3, '0'), name: payload.name, email: payload.email };
                        mockCustomers.push(cust);
                    }
                    if (cust) {
                        cust.name = payload.name;
                        cust.email = payload.email;
                        cust.phone = payload.phone;
                        cust.address = payload.address;
                        if (payload.avatar) cust.avatar = payload.avatar;
                        try { saveCustomersToStorage(); } catch (err) { /* noop */ }
                    }
                    // si cambió el email de login, actualizar mockUsers y la sesión
                    if (userEmail && payload.email && userEmail !== payload.email) {
                        if (mockUsers[userEmail]) {
                            mockUsers[payload.email] = Object.assign({}, mockUsers[userEmail]);
                            delete mockUsers[userEmail];
                        }
                        if (typeof setCurrentUserEmail === 'function') setCurrentUserEmail(payload.email);
                    }
                    closeAndRefresh('Datos de cuenta guardados.', 'success');
                } catch (err) {
                    console.error(err);
                    showAlert('No se pudo guardar los datos.', 'error');
                }
            };
            break;

        case 'security':
            wrapper.innerHTML = `
                <h3 class="text-xl font-bold mb-2">Seguridad</h3>
                <div class="mb-3">
                    <label class="flex items-center gap-3"><input id="settings-2fa" type="checkbox" ${customer.twoFA ? 'checked' : ''}/> Habilitar verificación en dos pasos (2FA)</label>
                </div>
                <div class="mb-3">
                    <h4 class="font-medium">Dispositivos conectados</h4>
                    <div id="settings-devices" class="text-sm text-slate-600 mt-2">Cargando dispositivos (simulado)...</div>
                </div>
                <div class="flex gap-2">
                    <button id="btn-logout-all" class="bg-red-500 text-white px-4 py-2 rounded">Cerrar todas las sesiones</button>
                    <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cerrar</button>
                </div>
            `;
            // simulate devices
            const devs = [{ id: 'd1', name: 'PC - Chrome', last: '2025-08-01 10:23' }, { id: 'd2', name: 'Móvil - Android', last: '2025-08-05 18:11' }];
            const devEl = document.getElementById('settings-devices');
            if (devEl) devEl.innerHTML = devs.map(d => `<div class="py-1">${d.name} <span class="text-xs text-slate-400">(${d.last})</span></div>`).join('');
            const logoutAll = document.getElementById('btn-logout-all');
            if (logoutAll) logoutAll.addEventListener('click', (e) => {
                e.preventDefault();
                // simulated: clear session tokens, here we'll push request
                pendingAdminRequests.push({ id: 'REQ-' + Date.now(), type: 'logout_all', email: userEmail, data: {} });
                showAlert('Se cerraron las sesiones en los dispositivos (simulado).', 'info');
                modal.classList.remove('active');
            });
            break;

        case 'payments':
            wrapper.innerHTML = `
                <h3 class="text-xl font-bold mb-2">Métodos de Pago</h3>
                <div id="settings-payments-list" class="mb-3 text-sm text-slate-700">Cargando...</div>
                <hr class="my-2" />
                <form id="settings-add-payment-form" class="grid gap-2">
                    <label class="text-sm">Titular</label>
                    <input id="pm-holder" class="border p-2" placeholder="NOMBRE EN LA TARJETA" />
                    <label class="text-sm">Número de tarjeta (solo números)</label>
                    <input id="pm-number" class="border p-2" placeholder="4242424242424242" />
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm">Expiración (MM/AA)</label>
                            <input id="pm-exp" class="border p-2" placeholder="12/26" />
                        </div>
                        <div>
                            <label class="text-sm">Marca</label>
                            <select id="pm-brand" class="border p-2">
                                <option value="visa">Visa</option>
                                <option value="mastercard">MasterCard</option>
                                <option value="amex">Amex</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-2">
                        <button id="btn-save-payment" class="bg-custom-blue text-white px-4 py-2 rounded">Guardar método</button>
                        <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                    </div>
                </form>
            `;

            const listEl = document.getElementById('settings-payments-list');
            const methods = (customer.paymentMethods && customer.paymentMethods.length) ? customer.paymentMethods : [];
            function renderMethods() {
                if (!listEl) return;
                if (!methods.length) {
                    listEl.innerHTML = '<div class="text-slate-500">No hay métodos de pago registrados.</div>';
                    return;
                }
                listEl.innerHTML = methods.map((m, i) => `
                    <div class="flex items-center justify-between py-2 border-b">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-8 flex items-center justify-center rounded-md bg-slate-100">
                                <img src="${getBrandIcon(m.brand)}" alt="${m.brand}" style="height:20px;" onerror="this.style.display='none'" />
                            </div>
                            <div>
                                <div class="font-medium">${capitalizeBrand(m.brand)} ${m.masked || ('**** **** **** ' + (m.last4 || '0000'))}</div>
                                <div class="text-sm text-slate-500">Vence ${m.exp || 'MM/AA'} • Titular ${m.holder || ''}</div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button data-i="${i}" class="btn-edit-payment text-sm text-custom-blue">Editar</button>
                            <button data-i="${i}" class="btn-del-payment text-sm text-red-600">Eliminar</button>
                        </div>
                    </div>
                `).join('');
            }

            renderMethods();

            // Save new card
            const saveBtn = document.getElementById('btn-save-payment');
            if (saveBtn) saveBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const holder = document.getElementById('pm-holder').value.trim();
                const number = document.getElementById('pm-number').value.replace(/\s+/g, '');
                const exp = document.getElementById('pm-exp').value.trim();
                const brand = document.getElementById('pm-brand').value;
                if (!number || number.length < 12) { showAlert('Número de tarjeta inválido.', 'error'); return; }
                const last4 = number.slice(-4);
                const masked = '**** **** **** ' + last4;
                const newCard = { id: 'pm_' + Date.now(), holder, last4, exp, brand, masked };
                customer.paymentMethods = customer.paymentMethods || [];
                customer.paymentMethods.push(newCard);
                showAlert('Método de pago añadido.', 'success');
                if (window.renderProfilePage) window.renderProfilePage();
                renderMethods();
            });

            // delegate actions (since list may be re-rendered)
            setTimeout(() => {
                document.querySelectorAll('.btn-edit-payment').forEach(btn => btn.addEventListener('click', (ev) => {
                    const idx = Number(btn.getAttribute('data-i'));
                    const m = methods[idx];
                    if (!m) return;
                    // open simple edit form via prompt for quickness
                    const newHolder = prompt('Titular', m.holder || '');
                    if (newHolder === null) return;
                    const newExp = prompt('Vencimiento (MM/AA)', m.exp || '');
                    if (newExp === null) return;
                    m.holder = newHolder;
                    m.exp = newExp;
                    showAlert('Método actualizado.', 'success');
                    if (window.renderProfilePage) window.renderProfilePage();
                    renderMethods();
                }));
                document.querySelectorAll('.btn-del-payment').forEach(btn => btn.addEventListener('click', (ev) => {
                    const idx = Number(btn.getAttribute('data-i'));
                    if (confirm('Eliminar método de pago?')) {
                        methods.splice(idx, 1);
                        showAlert('Método de pago eliminado.', 'info');
                        if (window.renderProfilePage) window.renderProfilePage();
                        renderMethods();
                    }
                }));
            }, 50);
            break;

        case 'privacy':
            wrapper.innerHTML = `
                <h3 class="text-xl font-bold mb-2">Privacidad</h3>
                <form id="settings-privacy-form">
                    <label class="flex items-center gap-3"><input id="vis-phone" type="checkbox" ${customer.visiblePhone !== false ? 'checked' : ''}/> Mostrar número de teléfono</label>
                    <label class="flex items-center gap-3 mt-2"><input id="vis-email" type="checkbox" ${customer.visibleEmail !== false ? 'checked' : ''}/> Mostrar correo</label>
                    <div class="mt-3">
                        <h4 class="font-medium">Notificaciones</h4>
                        <label class="flex items-center gap-3 mt-2"><input id="not-email" type="checkbox" ${customer.notifyEmail !== false ? 'checked' : ''}/> Notificaciones por correo</label>
                        <label class="flex items-center gap-3 mt-2"><input id="not-sms" type="checkbox" ${customer.notifySMS ? 'checked' : ''}/> Notificaciones por SMS</label>
                        <label class="flex items-center gap-3 mt-2"><input id="not-push" type="checkbox" ${customer.notifyPush ? 'checked' : ''}/> Notificaciones push</label>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                        <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                    </div>
                </form>
            `;
            const privForm = document.getElementById('settings-privacy-form');
            if (privForm) privForm.onsubmit = function(e) {
                e.preventDefault();
                customer.visiblePhone = !!document.getElementById('vis-phone').checked;
                customer.visibleEmail = !!document.getElementById('vis-email').checked;
                customer.notifyEmail = !!document.getElementById('not-email').checked;
                customer.notifySMS = !!document.getElementById('not-sms').checked;
                customer.notifyPush = !!document.getElementById('not-push').checked;
                closeAndRefresh('Preferencias de privacidad guardadas.', 'success');
            };
            break;

        case 'preferences':
            wrapper.innerHTML = `
                <h3 class="text-xl font-bold mb-2">Preferencias</h3>
                <form id="settings-prefs-form">
                    <label class="block text-sm text-slate-600">Idioma</label>
                    <select id="pref-lang" class="border p-2 mb-2 w-full">
                        <option value="es" ${customer.lang === 'es' ? 'selected' : ''}>Español</option>
                        <option value="en" ${customer.lang === 'en' ? 'selected' : ''}>English</option>
                    </select>
                    <label class="block text-sm text-slate-600">Tema</label>
                    <select id="pref-theme" class="border p-2 mb-2 w-full">
                        <option value="system">Sistema</option>
                        <option value="light" ${customer.theme === 'light' ? 'selected' : ''}>Claro</option>
                        <option value="dark" ${customer.theme === 'dark' ? 'selected' : ''}>Oscuro</option>
                    </select>
                    <label class="block text-sm text-slate-600">Zona horaria</label>
                    <input id="pref-tz" class="border p-2 mb-2 w-full" value="${customer.timezone || Intl.DateTimeFormat().resolvedOptions().timeZone || ''}" />
                    <div class="mt-3 flex gap-2">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                        <button type="button" onclick="closeModal('edit-modal')" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                    </div>
                </form>
            `;
            const prefsForm = document.getElementById('settings-prefs-form');
            if (prefsForm) prefsForm.onsubmit = function(e) {
                e.preventDefault();
                customer.lang = document.getElementById('pref-lang').value;
                customer.theme = document.getElementById('pref-theme').value;
                customer.timezone = document.getElementById('pref-tz').value;
                closeAndRefresh('Preferencias guardadas. Es posible que algunos cambios requieran recargar la página.', 'success');
            };
            break;

        case 'support':
            wrapper.innerHTML = `
                <h3 class="text-xl font-bold mb-2">Soporte</h3>
                <div class="mb-3">
                    <button id="btn-help-center" class="bg-custom-blue text-white px-4 py-2 rounded">Centro de ayuda</button>
                    <button id="btn-contact-support" class="bg-gray-100 px-4 py-2 rounded ml-2">Contactar soporte</button>
                </div>
                <div class="text-sm text-slate-600">También puedes escribirnos por WhatsApp o abrir un ticket desde la sección de soporte.</div>
            `;
            setTimeout(() => {
                const hc = document.getElementById('btn-help-center');
                const cs = document.getElementById('btn-contact-support');
                if (hc) hc.addEventListener('click', (e) => { e.preventDefault(); showAlert('Abriendo centro de ayuda (simulado).', 'info'); });
                if (cs) cs.addEventListener('click', (e) => { e.preventDefault(); pendingAdminRequests.push({ id: 'REQ-' + Date.now(), type: 'support_contact', email: userEmail }); showAlert('Ticket de soporte creado (simulado).', 'info'); modal.classList.remove('active'); });
            }, 40);
            break;

        case 'account-status':
            wrapper.innerHTML = `
                <h3 class="text-xl font-bold mb-2">Estado de la cuenta</h3>
                <div class="mb-3 text-sm text-slate-700">Aquí puedes deshabilitar, habilitar o solicitar la eliminación de tu cuenta. Algunas acciones requieren confirmar con correo y contraseña.</div>
                <div class="flex gap-2">
                    <button id="btn-disable-account" class="bg-yellow-500 text-white px-4 py-2 rounded">Deshabilitar cuenta</button>
                    <button id="btn-enable-account" class="bg-green-500 text-white px-4 py-2 rounded">Habilitar cuenta</button>
                    <button id="btn-delete-account" class="bg-red-600 text-white px-4 py-2 rounded">Eliminar cuenta</button>
                </div>
            `;
            setTimeout(() => {
                const dis = document.getElementById('btn-disable-account');
                const ena = document.getElementById('btn-enable-account');
                const del = document.getElementById('btn-delete-account');
                if (dis) dis.addEventListener('click', (e) => { e.preventDefault(); if (confirm('Confirmar deshabilitar cuenta?')) { customer.disabled = true; showAlert('Cuenta deshabilitada localmente.', 'info'); modal.classList.remove('active'); if (window.renderProfilePage) window.renderProfilePage(); } });
                if (ena) ena.addEventListener('click', (e) => { e.preventDefault(); customer.disabled = false; showAlert('Cuenta habilitada.', 'success'); modal.classList.remove('active'); if (window.renderProfilePage) window.renderProfilePage(); });
                if (del) del.addEventListener('click', (e) => { e.preventDefault(); if (confirm('Eliminar cuenta: acción irreversible. Confirmar de nuevo.')) { pendingAdminRequests.push({ id: 'REQ-' + Date.now(), type: 'delete_account', email: userEmail }); showAlert('Solicitud de eliminación enviada. Requiere validación administrativa.', 'error'); modal.classList.remove('active'); } });
            }, 40);
            break;

        default:
            wrapper.innerHTML = `<div>Sección no encontrada</div>`;
            break;
    }
}

// Abrir modal genérico de detalles (usuario, cliente, repartidor, proveedor, pedido)
export function openDetailsModal(type, item) {
    const modal = document.getElementById('edit-modal');
    if (!modal) return;
    modal.classList.add('active');
    // prefer explicit inner content elements to ensure we write into the right modal container
    const wrapper = modal.querySelector('#edit-modal-content') || modal.querySelector('.bg-white') || modal;
    if (!wrapper) return;

    function makeWhatsBtn(phone, text) {
        if (!phone && !window.whatsappNumber) return '';
        const url = (window.getWhatsAppUrl && window.getWhatsAppUrl(phone || window.whatsappNumber, text)) || '#';
            return `<a class="btn btn-sm" target="_blank" rel="noopener noreferrer" href="${url}">${icons.whats} WhatsApp</a>`;
    }

    if (type === 'user' || type === 'customer') {
        const cust = item || {};
        // render user details with role selector to change role in-place
        wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">Detalle de Usuario</h3>
            <div class="grid grid-cols-1 gap-3">
                <div><strong>Nombre:</strong> ${cust.name || ''}</div>
                <div><strong>Correo:</strong> ${cust.email || ''}</div>
                <div><strong>Teléfono:</strong> ${cust.phone || 'No registrado'}</div>
                <div><strong>Dirección:</strong> ${cust.address || 'No registrada'}</div>
                <div><strong>Registrado:</strong> ${cust.registered || ''}</div>
                <div class="mt-3">${makeWhatsBtn(cust.phone, 'Hola, te contacto desde Remates El Paisa.')}</div>
                <div class="mt-4">
                    <label class="block text-sm text-slate-600">Rol actual</label>
                    <div class="flex items-center gap-3 mt-2">
                        <span id="current-role-badge" class="px-2 py-1 rounded text-sm ${cust.role === 'admin' ? 'bg-purple-100 text-purple-800' : cust.role === 'delivery' ? 'bg-green-100 text-green-800' : cust.role === 'customer' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">${cust.role || 'customer'}</span>
                        <select id="details-role-select" class="border rounded px-2 py-1">
                            <option value="admin" ${cust.role === 'admin' ? 'selected' : ''}>Administrador</option>
                            <option value="delivery" ${cust.role === 'delivery' ? 'selected' : ''}>Repartidor</option>
                            <option value="customer" ${cust.role === 'customer' ? 'selected' : ''}>Cliente</option>
                            <option value="guest" ${cust.role === 'guest' ? 'selected' : ''}>Invitado</option>
                        </select>
                        <button id="btn-save-role" class="bg-custom-blue text-white px-3 py-1 rounded">Guardar</button>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button id="btn-close-details" class="btn btn-secondary">Cerrar</button>
                    <button id="btn-open-edit" class="bg-custom-blue text-white px-3 py-1 rounded">Editar</button>
                </div>
            </div>
        `;

        // wire role change
        setTimeout(() => {
            const sel = document.getElementById('details-role-select');
            const saveBtn = document.getElementById('btn-save-role');
            const badge = document.getElementById('current-role-badge');
            if (!sel || !saveBtn) return;
            saveBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const newRole = sel.value;
                const email = cust.email;
                // ensure mockUsers entry
                try {
                    if (!mockUsers[email]) mockUsers[email] = { role: newRole, name: cust.name || email };
                    const prevRole = mockUsers[email].role || 'customer';
                    mockUsers[email].role = newRole;

                    // if role is delivery ensure entry exists in mockDeliveryMen
                    if (newRole === 'delivery') {
                        const exists = mockDeliveryMen.find(d => d.email === email);
                        if (!exists) {
                            const nextId = 'D' + String((mockDeliveryMen.length ? Math.max(...mockDeliveryMen.map(x => Number(x.id.replace(/\D/g, '') || 0))) + 1 : 1)).padStart(3, '0');
                            mockDeliveryMen.push({ id: nextId, name: cust.name || email.split('@')[0], email: email, phone: cust.phone || '', vehicle: 'Moto', licensePlate: null, status: 'Activo' });
                        }
                    }

                    // if switched away from delivery we keep the data but it's acceptable; render pages to reflect changes
                    // if role is customer ensure customer entry
                    if (newRole === 'customer') {
                        const cExists = mockCustomers.find(c => c.email === email);
                        if (!cExists) {
                            const newId = 'C' + (mockCustomers.length + 1).toString().padStart(3, '0');
                            mockCustomers.push({ id: newId, name: cust.name || email.split('@')[0], email: email, phone: cust.phone || '', address: cust.address || '' });
                        }
                    }

                    // update badge and refresh related views
                    if (badge) {
                        const roleClassMap = { admin: 'bg-purple-100 text-purple-800', delivery: 'bg-green-100 text-green-800', customer: 'bg-blue-100 text-blue-800', guest: 'bg-gray-100 text-gray-800' };
                        badge.className = 'px-2 py-1 rounded text-sm ' + (roleClassMap[newRole] || roleClassMap.customer);
                        badge.textContent = newRole;
                    }

                    // trigger re-renders
                    if (window.renderUsersPage) window.renderUsersPage();
                    if (window.renderClientsPage) window.renderClientsPage();
                    if (window.renderAdminDeliveryMenPage) window.renderAdminDeliveryMenPage();

                    showAlert('Rol actualizado a ' + newRole, 'success');
                } catch (err) {
                    console.error('Error cambiando rol', err);
                    showAlert('No se pudo cambiar el rol', 'error');
                }
            });
            // buttons wired here to keep closure access to `cust` and `type`
            const closeBtn = document.getElementById('btn-close-details');
            const openEditBtn = document.getElementById('btn-open-edit');
            if (closeBtn) closeBtn.addEventListener('click', (e) => { e.preventDefault(); closeModal('edit-modal'); });
            if (openEditBtn) openEditBtn.addEventListener('click', (e) => { e.preventDefault(); closeModal('edit-modal'); if (window.openEditModal) { window.openEditModal(type === 'user' ? 'profile' : 'customer', Object.assign({}, cust)); window.focusModalFirstInput && window.focusModalFirstInput(); } });
        }, 20);
        return;
    }

    if (type === 'delivery') {
        const d = item || {};
        // compute assigned orders for this repartidor
        const assigned = (mockOrders || []).filter(o => o.deliveryMan === d.name || o.deliveryMan === d.email || o.deliveryId === d.id || o.deliveryMan === d.id);
        const delivered = assigned.filter(a => a.status === 'Entregado' || a.status === 'Entregado' || a.status === 'Entregado');
        const active = assigned.find(a => a.status === 'En Camino' || a.status === 'En Reparto' || a.status === 'Procesando' || a.status === 'Listo');
        // compute simple metrics — timeAvg is placeholder unless timestamps exist
        const totalDelivered = delivered.length;
        const avgRating = d.rating || (d.rating === 0 ? 0 : null);
        const avgTime = d.avgTime || null; // e.g., '00:30'
        wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">Detalle de Repartidor</h3>
            <div class="grid grid-cols-1 gap-2">
                <div><strong>Nombre:</strong> ${d.name || ''} <span class="text-sm text-slate-500">(${d.id || ''})</span></div>
                <div><strong>Email:</strong> ${d.email || 'No registrado'}</div>
                <div><strong>Teléfono:</strong> ${d.phone || 'No registrado'}</div>
                <div><strong>Vehículo:</strong> ${d.vehicle || 'No registrado'}</div>
                <div><strong>Placa:</strong> ${d.licensePlate || 'No registrada'}</div>
                <div><strong>Estado:</strong> <span class="px-2 py-1 rounded ${d.status === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${d.status || 'Desconocido'}</span></div>
                <div class="mt-2">${makeWhatsBtn(d.phone, 'Hola, te contacto sobre la logística de entregas.')}</div>

                <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="p-3 bg-slate-50 rounded">
                        <div class="text-sm text-slate-500">Total entregados</div>
                        <div class="font-semibold text-lg">${totalDelivered}</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded">
                        <div class="text-sm text-slate-500">Calificación promedio</div>
                        <div class="font-semibold text-lg">${avgRating != null ? avgRating : 'N/A'}</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded">
                        <div class="text-sm text-slate-500">Tiempo promedio</div>
                        <div class="font-semibold text-lg">${avgTime || 'N/A'}</div>
                    </div>
                </div>

                <div class="mt-3">
                    <h4 class="font-semibold">Pedido actual</h4>
                    <div class="text-sm mt-2">${active ? `#${active.id} — ${active.customerName || active.customer || active.customerEmail || ''} — <em>${active.status}</em> <button data-order-id="${active.id}" class="ml-2 text-xs text-custom-blue btn-open-order">Ver</button>` : '<span class="text-slate-500">No hay pedido activo</span>'}</div>
                </div>

                <div class="mt-3">
                    <h4 class="font-semibold">Historial de entregas</h4>
                    <ul class="text-sm list-disc list-inside mt-2">
                        ${delivered.length ? delivered.map(o => `<li>#${o.id} — ${o.customerName || o.customer || o.customerEmail || ''} — ${o.date || ''} — <em>${o.status || ''}</em> <button data-order-id="${o.id}" class="ml-2 text-xs text-custom-blue btn-open-order">Ver</button></li>`).join('') : '<li class="text-slate-500">Sin entregas registradas</li>'}
                    </ul>
                </div>

                <div class="mt-4 flex gap-2 justify-end">
                    <button onclick="closeModal('edit-modal')" class="btn btn-secondary">Cerrar</button>
                    <button onclick="event.preventDefault(); closeModal('edit-modal'); if(window.openEditModal) { window.openEditModal('delivery', Object.assign({}, d)); focusModalFirstInput(); }" class="bg-custom-blue text-white px-3 py-1 rounded">Editar</button>
                </div>
            </div>
        `;
        
        // attach handlers for the small "Ver" buttons inside the assigned list
        setTimeout(() => {
            wrapper.querySelectorAll('.btn-open-order').forEach(b => b.addEventListener('click', (ev) => {
                ev.preventDefault(); const id = b.getAttribute('data-order-id'); if (!id) return; // close this delivery modal and open the order modal
                closeModal('edit-modal'); if (window.showOrderDetails) window.showOrderDetails(id);
            }));
        }, 20);
        return;
    }

    if (type === 'provider' || type === 'providers') {
        const p = item || {};
        wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">Proveedor — ${p.name || ''}</h3>
            <div><strong>Contacto:</strong> ${p.contact || ''}</div>
            <div><strong>Teléfono:</strong> ${p.phone || ''}</div>
            <div><strong>Email:</strong> ${p.email || ''}</div>
            <div><strong>Dirección:</strong> ${p.address || 'No registrada'}</div>
            <div class="mt-2 text-sm text-slate-600">Productos/Servicios: ${Array.isArray(p.products) ? p.products.join(', ') : p.products || ''}</div>
            <div class="mt-3">${makeWhatsBtn(p.phone, 'Hola, quisiera pedir información sobre sus productos/servicios.')}</div>
            <div class="mt-4 flex gap-2"><button id="btn-close-provider" class="btn btn-secondary">Cerrar</button><button id="btn-edit-provider" class="bg-custom-blue text-white px-3 py-1 rounded">Editar</button></div>
        `;
        // wire provider buttons using closure over `p`
        setTimeout(() => {
            const closeP = document.getElementById('btn-close-provider');
            const editP = document.getElementById('btn-edit-provider');
            if (closeP) closeP.addEventListener('click', (e) => { e.preventDefault(); closeModal('edit-modal'); });
            if (editP) editP.addEventListener('click', (e) => { e.preventDefault(); closeModal('edit-modal'); if (window.openEditModal) { window.openEditModal('provider', Object.assign({}, p)); focusModalFirstInput(); } });
        }, 20);
        return;
    }

    if (type === 'order' || type === 'pedido') {
        const o = item || {};
        // enrich order display: customer info, assigned delivery, items table and totals
        const items = Array.isArray(o.items) ? o.items : [];
        const itemsRows = items.map(it => `
            <tr class="border-b">
                <td class="p-2">${it.name}</td>
                <td class="p-2 text-center">${it.qty}</td>
                <td class="p-2 text-right">${it.price != null ? formatCurrency(it.price) : ''}</td>
                <td class="p-2 text-right">${(it.price != null) ? formatCurrency((it.price || 0) * (it.qty || 1)) : ''}</td>
            </tr>
        `).join('');

        wrapper.innerHTML = `
            <h3 class="text-xl font-bold mb-2">Detalle de Pedido ${o.id || ''}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <div><strong>Cliente:</strong> ${o.customer || o.customerName || ''}</div>
                    <div><strong>Correo:</strong> ${o.customerEmail || ''}</div>
                        <div><strong>Fecha:</strong> ${o.date || ''}</div>
                        <div><strong>Estado:</strong> <span class="px-2 py-1 rounded ${o.status === 'Entregado' ? 'bg-green-100 text-green-800' : o.status === 'Cancelado' ? 'bg-red-100 text-red-800' : o.status === 'En Camino' ? 'bg-indigo-100 text-indigo-800' : 'bg-yellow-100 text-yellow-800'}">${o.status || ''}</span></div>
                </div>
                <div>
                        <div><strong>Repartidor asignado:</strong> ${o.deliveryMan || 'En espera de repartidor'}</div>
                    <div class="mt-2">${o.deliveryPhone ? `<a class="btn btn-sm" target="_blank" href="${makeWhatsBtn(o.deliveryPhone, 'Hola, sobre el pedido ' + (o.id || ''))}">${icons.whats} WhatsApp Repartidor</a>` : ''}</div>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-2 font-semibold">Producto</th>
                            <th class="p-2 font-semibold text-center">Cantidad</th>
                            <th class="p-2 font-semibold text-right">Precio</th>
                            <th class="p-2 font-semibold text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsRows}
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <div class="w-full md:w-1/3 bg-slate-50 p-3 rounded">
                    <div class="flex justify-between"><span>Subtotal</span><strong>${o.subtotal != null ? formatCurrency(o.subtotal) : ''}</strong></div>
                    <div class="flex justify-between"><span>Envío</span><strong>${o.shipping != null ? formatCurrency(o.shipping) : ''}</strong></div>
                    <hr class="my-2" />
                    <div class="flex justify-between text-lg"><span>Total</span><strong>${o.total != null ? formatCurrency(o.total) : ''}</strong></div>
                </div>
            </div>

            <div class="mt-4 flex gap-2"><button onclick="closeModal('edit-modal')" class="btn btn-secondary">Cerrar</button>${(getCurrentUserRole && getCurrentUserRole() === 'admin') ? `<button onclick="event.preventDefault(); closeModal('edit-modal'); if(window.openEditModal) { window.openEditModal('order', Object.assign({}, o)); window.focusModalFirstInput && window.focusModalFirstInput(); }" class="bg-custom-blue text-white px-3 py-1 rounded">Editar</button>` : ''}</div>
        `;
        return;
    }

    // fallback
    wrapper.innerHTML = `<div>Detalle no disponible</div>`;
}

// expose for templates and main entry
window.openRevalidateModal = window.openRevalidateModal || openRevalidateModal;
// expose helper
window.focusModalFirstInput = window.focusModalFirstInput || focusModalFirstInput;
