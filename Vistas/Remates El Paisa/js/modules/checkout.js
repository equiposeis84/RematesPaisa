/*
Archivo: js/modules/checkout.js
Descripción: Flujo de pago y confirmación de pedidos en cliente.
Explicación: Maneja formularios de envío/facturación, validación y creación de pedidos en localStorage.
Importante: No almacenar datos de tarjetas en texto plano; integrar un proveedor de pagos si lo vas a usar en producción.
*/

// Funciones mínimas para renderizar la página de checkout y completar la compra.
// Esta implementación usa localStorage para guardar pedidos y trabaja con el carrito
// expuesto por `js/modules/cart.js` (si existe). Es una plantilla editable.

export function renderCheckoutPage() {
	const container = document.getElementById('page-checkout');
	if (!container) return;

	container.innerHTML = `
		<h2>Finalizar compra</h2>
		<form id="checkout-form">
			<label>Nombre completo<input name="name" required></label>
			<label>Dirección<input name="address" required></label>
			<label>Teléfono<input name="phone" required></label>
			<button type="submit">Confirmar pedido</button>
		</form>
	`;

	const form = container.querySelector('#checkout-form');
	form.addEventListener('submit', (e) => {
		e.preventDefault();
		const data = Object.fromEntries(new FormData(form).entries());
		completeCheckout(data);
	});
}

/**
 * Completa el proceso de compra: crea un objeto pedido mínimo y lo guarda en localStorage.
 * @param {Object} customerInfo - Datos de envío/facturación recolectados del formulario.
 */
export function completeCheckout(customerInfo = {}) {
	// Intentar leer carrito desde localStorage
	const cartRaw = localStorage.getItem('rp_cart');
	const cart = cartRaw ? JSON.parse(cartRaw) : { items: [] };

	if (!cart.items || cart.items.length === 0) {
		alert('El carrito está vacío.');
		return;
	}

	const ordersRaw = localStorage.getItem('rp_orders');
	const orders = ordersRaw ? JSON.parse(ordersRaw) : [];

	const newOrder = {
		id: `order_${Date.now()}`,
		createdAt: new Date().toISOString(),
		customer: customerInfo,
		items: cart.items,
		status: 'pendiente'
	};

	orders.push(newOrder);
	localStorage.setItem('rp_orders', JSON.stringify(orders));

	// Limpiar carrito
	localStorage.removeItem('rp_cart');

	// Notificar al usuario
	alert('Pedido creado correctamente. ID: ' + newOrder.id);

	// Redirigir a la página principal o de pedidos
	if (window.navigateTo) window.navigateTo('orders');
}
