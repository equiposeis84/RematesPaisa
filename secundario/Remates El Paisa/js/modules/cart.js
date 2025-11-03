/*
Archivo: js/modules/cart.js
Descripción: Lógica del carrito de compras: añadir, eliminar, calcular totales.
Explicación: Exporta funciones para manipular el carrito y persistirlo en localStorage.
Casos importantes: manejar cantidades cero y sincronización con inventario.
*/

// --- FUNCIONES DEL CARRITO ---
import { mockProducts, mockOrders, getCart, setCart, pendingValidations, getCurrentUserEmail, setNextPage } from './constants.js';
import { renderNav } from './navigation.js';
import { formatCurrency, showAlert, animateBadge } from './utils.js';

export function addToCart(productId) {
    const product = mockProducts.find(p => p.id === productId);
    if (!product) return;
    const cart = getCart() || [];
    const item = cart.find(i => i.product.id === productId);
    if (item) {
        item.quantity += 1;
    } else {
        cart.push({ product, quantity: 1 });
    }
    setCart(cart);
    showAlert('Producto agregado al carrito', 'success');
    // actualizar la navegación (badge)
    renderNav();
    // pequeña animación de la badge si existe
    animateBadge();
    renderCartPage();
}

export function updateCartQuantity(productId, newQuantity) {
    const cart = getCart() || [];
    const item = cart.find(i => i.product.id === productId);
    if (item) {
        item.quantity = parseInt(newQuantity, 10) || 1;
        if (item.quantity <= 0) {
            const updated = cart.filter(i => i.product.id !== productId);
            setCart(updated);
        } else {
            setCart(cart);
        }
        renderCartPage();
    }
}

export function removeFromCart(productId) {
    const cart = getCart() || [];
    const updated = cart.filter(i => i.product.id !== productId);
    setCart(updated);
    renderCartPage();
}

export function renderCartPage() {
    const container = document.getElementById('page-cart');
    if (!container) return;
    const cart = getCart() || [];
    if (!cart || cart.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <h2 class="text-3xl font-bold mb-4">Tu Carrito está Vacío</h2>
                <p class="mb-4">Añade productos desde el catálogo para empezar a comprar.</p>
                <button onclick="event.preventDefault(); window.navigateTo && window.navigateTo('catalog')" class="bg-blue-500 text-white px-6 py-2 rounded">Ir al Catálogo</button>
            </div>
        `;
        renderNav();
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
    const shipping = 5000;
    const total = subtotal + shipping;

    container.innerHTML = `
        <div class="page active animate-fade-in">
            <h2 class="text-3xl font-bold mb-4">Carrito de Compras</h2>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 md:col-span-8">
                    <!-- encabezados de columnas -->
                    <div class="hidden md:grid grid-cols-12 gap-4 items-center bg-white p-3 rounded-t-lg border-t border-l border-r">
                        <div class="col-span-6 text-sm font-medium text-gray-600">Producto</div>
                        <div class="col-span-2 text-right text-sm font-medium text-gray-600">Precio</div>
                        <div class="col-span-2 text-center text-sm font-medium text-gray-600">Cantidad</div>
                        <div class="col-span-2 text-right text-sm font-medium text-gray-600">Subtotal</div>
                    </div>

                    <div class="bg-transparent">
                        ${cart.map(item => `
                            <div class="bg-white rounded-b-lg shadow p-4 mb-4 cart-row">
                                <div class="grid grid-cols-12 gap-4 items-center">
                                    <div class="col-span-6 flex items-center gap-4">
                                        <img src="${item.product.image}" alt="${item.product.name}" class="cart-item-img">
                                        <div>
                                            <h3 class="font-semibold text-lg">${item.product.name}</h3>
                                            <p class="text-sm text-gray-500">${item.product.category || ''}</p>
                                        </div>
                                    </div>
                                    <div class="col-span-2 text-right">
                                        <div class="text-gray-700">${formatCurrency(item.product.price)}</div>
                                    </div>
                                    <div class="col-span-2 text-center">
                                        <input aria-label="Cantidad del producto" type="number" min="1" value="${item.quantity}" onchange="event.preventDefault(); window.updateCartQuantity && window.updateCartQuantity(${item.product.id}, this.value)" class="w-20 px-2 py-1 border rounded text-center mx-auto">
                                    </div>
                                    <div class="col-span-2 text-right flex items-center justify-end gap-3">
                                        <div class="font-semibold">${formatCurrency(item.product.price * item.quantity)}</div>
                                        <button title="Eliminar" aria-label="Eliminar producto" onclick="event.preventDefault(); window.removeFromCart && window.removeFromCart(${item.product.id})" class="trash-btn text-white p-2 rounded-full bg-red-500 hover:bg-red-600">
                                            <!-- Icono de basura moderno (relleno) -->
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H3.5a.5.5 0 000 1h13a.5.5 0 000-1H15V3a1 1 0 00-1-1H6zm2.25 5a.75.75 0 00-.75.75v7a.75.75 0 001.5 0v-7a.75.75 0 00-.75-.75zM10 7.75a.75.75 0 00-.75.75v7a.75.75 0 001.5 0v-7A.75.75 0 0010 7.75zm2.5-.75a.75.75 0 00-.75.75v7a.75.75 0 101.5 0v-7a.75.75 0 00-.75-.75z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <div class="sticky top-6 bg-white rounded-lg shadow p-4">
                        <h4 class="font-semibold mb-3">Resumen del pedido</h4>
                        <div class="flex justify-between mb-2"><span>Subtotal</span><span>${formatCurrency(subtotal)}</span></div>
                        <div class="flex justify-between mb-2"><span>Precio envío</span><span>${formatCurrency(shipping)}</span></div>
                        <div class="flex justify-between font-bold text-lg border-t pt-2"><span>Total</span><span>${formatCurrency(total)}</span></div>
                        <button onclick="event.preventDefault(); window.completeCheckout && window.completeCheckout()" class="mt-4 w-full bg-blue-500 text-white px-6 py-2 rounded">Confirmar pedido</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    renderNav();
}

export function completeCheckout() {
    const cart = getCart() || [];
    if (cart.length === 0) return;

    // Require login to complete checkout
    const userEmail = getCurrentUserEmail();
    if (!userEmail) {
        // store target page so after login user returns to orders
        if (setNextPage) setNextPage('orders');
        if (window.navigateTo) window.navigateTo('login');
        showAlert('Debes iniciar sesión para confirmar el pedido.', 'info');
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
    if (subtotal > 200000) {
        showAlert('No es posible finalizar compras al por mayor por este canal.', 'error');
        return;
    }

    // Enviar a validación administrativa en lugar de crear el pedido inmediatamente
    const user = getCurrentUserEmail();
    pendingValidations.unshift({
        id: 'VAL-' + (pendingValidations.length + 1).toString().padStart(3, '0'),
        email: user || 'invitado',
        submittedAt: new Date().toISOString(),
        items: cart.map(item => ({ id: item.product.id, name: item.product.name, quantity: item.quantity, price: item.product.price })),
        subtotal: subtotal,
        shipping: 5000,
        status: 'Pendiente de validación'
    });

    // Vaciar carrito y notificar
    setCart([]);
    renderNav();
    showAlert('Tu pedido ha sido enviado para validación administrativa. Te notificaremos cuando sea aprobado.', 'info');
    if (window.navigateTo) window.navigateTo('orders');
}