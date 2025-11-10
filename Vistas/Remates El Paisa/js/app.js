/*
Archivo: js/app.js
Descripción: Punto de entrada de la aplicación en modo desarrollo.
Explicación: Inicializa rutas, controladores y carga los módulos principales. Aquí se orquesta la aplicación.
Notas: Mantener la inicialización ligera; delegar la lógica a `js/modules/`.
*/

// --- ESTADO DE LA APLICACIÓN ---
var currentPage = 'home';
var currentUserRole = 'guest';
var activeSubMenu = null;
var cart = [];
var currentChart = null;
var currentEditItem = null;
var currentEditType = null;

// --- DATOS DE EJEMPLO ---
var mockUsers = {
    'admin@remates.com': { role: 'admin', name: 'Admin General', password: '123' },
    'repartidor@remates.com': { role: 'delivery', name: 'Juan Pérez', password: '123' },
    'cliente@remates.com': { role: 'customer', name: 'Ana García', password: '123' },
};

var mockProducts = [
    { id: 1, name: 'Limpiador Multiusos Floral', price: 12500, stock: 150, image: 'img/products/limpiador.jpg', description: 'Botella de 1L. Aroma fresco y duradero que perfuma todo tu hogar.' },
    { id: 2, name: 'Detergente Líquido para Ropa', price: 28000, stock: 85, image: 'img/products/detergente.jpg', description: 'Rinde hasta 50 lavadas. Su fórmula avanzada cuida los colores y elimina manchas.' },
    { id: 3, name: 'Lavaloza Líquido Limón', price: 8900, stock: 210, image: 'img/products/lavaloza.jpg', description: 'Arranca la grasa más difícil con su poder corta grasa. Botella de 750ml.' },
    { id: 4, name: 'Paño de Microfibra (Paquete x3)', price: 10500, stock: 60, image: 'img/products/microfibra.jpg', description: 'Paquete de 3 paños ultra absorbentes para limpieza general.' },
    { id: 5, name: 'Bolsas de Basura (Rollo x30)', price: 6500, stock: 120, image: 'img/products/bolsas.jpg', description: 'Rollo de 30 bolsas resistentes para basura doméstica.' },
    { id: 6, name: 'Ambientador en Aerosol Lavanda', price: 9900, stock: 75, image: 'img/products/ambientador.jpg', description: 'Aroma lavanda, elimina olores y refresca el ambiente.' }
];

var mockOrders = [
    { id: 'ORD-001', customer: 'Ana García', deliveryMan: 'Juan Pérez', status: 'Pendiente' }
];

// --- ICONOS SVG ---
var icons = {
    home: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>`,
    catalog: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" /></svg>`,
    orders: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>`,
    cart: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>`,
    help: `<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 14v.01M16 10h.01M12 10v.01M12 18a6 6 0 100-12 6 6 0 000 12z" /></svg>`,
    login: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>`
};

// --- MENÚS DE NAVEGACIÓN POR ROL ---
var navMenus = {
    admin: [
        { page: 'home', label: 'Inicio', icon: 'home' },
        { page: 'catalog', label: 'Catálogo', icon: 'catalog' },
        { page: 'orders', label: 'Pedidos', icon: 'orders' },
        { page: 'cart', label: 'Carrito', icon: 'cart' },
        { page: 'help', label: 'Ayuda y Contacto', icon: 'help', bottom: true },
        { page: 'login', label: 'Cerrar Sesión', icon: 'login', bottom: true }
    ],
    customer: [
        { page: 'home', label: 'Inicio', icon: 'home' },
        { page: 'catalog', label: 'Catálogo', icon: 'catalog' },
        { page: 'orders', label: 'Mis Pedidos', icon: 'orders' },
        { page: 'cart', label: 'Carrito', icon: 'cart' },
        { page: 'help', label: 'Ayuda y Contacto', icon: 'help', bottom: true },
        { page: 'login', label: 'Cerrar Sesión', icon: 'login', bottom: true }
    ],
    delivery: [
        { page: 'home', label: 'Inicio', icon: 'home' },
        { page: 'orders', label: 'Mis Entregas', icon: 'orders' },
        { page: 'help', label: 'Ayuda y Contacto', icon: 'help', bottom: true },
        { page: 'login', label: 'Cerrar Sesión', icon: 'login', bottom: true }
    ],
    guest: [
        { page: 'home', label: 'Inicio', icon: 'home' },
        { page: 'login', label: 'Iniciar Sesión', icon: 'login', bottom: true }
    ]
};

// --- FUNCIONES DE UTILIDAD ---
function formatCurrency(value) {
    return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0 }).format(value);
}

function showAlert(message, type = 'info') {
    // Si existe el sistema moderno de toasts (modules/utils.js) lo usamos
    if (window.showAlert && typeof window.showAlert === 'function') {
        window.showAlert(message, type);
        return;
    }
    // En entornos antiguos, evitar abrir alert() nativo: registrar en consola
    // showAlert fallback: silent in production to avoid console noise
}

// --- RENDERIZADO DE NAVEGACIÓN ---
function renderNav() {
    var navContainer = document.getElementById('main-nav');
    var sessionContainer = document.getElementById('session-controls');
    if (!navContainer || !sessionContainer) return;
    navContainer.innerHTML = '';
    sessionContainer.innerHTML = '';

    var menu = navMenus[currentUserRole] || navMenus['guest'];
    menu.forEach(function(item) {
        navContainer.insertAdjacentHTML('beforeend', `
            <a href="#" onclick="event.preventDefault(); navigateTo('${item.page}')" class="flex items-center gap-3 py-2.5 px-4 mb-2 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors ${currentPage === item.page ? 'bg-custom-nav-active' : ''}">
                ${icons[item.icon]}
                <span>${item.label}</span>
            </a>
        `);
    });

    var cartItemCount = cart.reduce(function(sum, item) { return sum + (item.quantity || 0); }, 0);
    if (['guest', 'customer'].includes(currentUserRole)) {
        var cartIndicator = cartItemCount > 0 ? `<span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">${cartItemCount}</span>` : '';
        navContainer.insertAdjacentHTML('beforeend', `
            <a href="#" onclick="event.preventDefault(); navigateTo('cart');" class="relative flex items-center gap-3 py-2.5 px-4 mb-2 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors ${currentPage === 'cart' ? 'bg-custom-nav-active' : ''}">
                ${icons.cart}
                <span>Carrito</span>
                ${cartIndicator}
            </a>
        `);
    }

    var sessionHTML = `<a href="#" onclick="event.preventDefault(); navigateTo('help')" class="flex items-center gap-3 py-2.5 px-4 mb-2 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors ${currentPage === 'help' ? 'bg-custom-nav-active' : ''}">${icons.help}<span>Ayuda y Contacto</span></a>`;
    if (currentUserRole === 'guest') {
        sessionHTML += `<a href="#" onclick="event.preventDefault(); navigateTo('login');" class="flex items-center gap-3 py-2.5 px-4 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors">${icons.login}<span>Iniciar Sesión</span></a>`;
    } else {
        sessionHTML += `<a href="#" onclick="event.preventDefault(); navigateTo('login');" class="flex items-center gap-3 py-2.5 px-4 rounded-lg text-sm font-medium bg-custom-nav-hover transition-colors">${icons.login}<span>Cerrar Sesión</span></a>`;
    }
    sessionContainer.innerHTML = sessionHTML;
}

// --- RENDERIZADO DE PÁGINAS ---
function renderHomePage() {
    var main = document.querySelector('main');
    if (!main) return;
    main.innerHTML = `
        <div class="page active animate-fade-in" id="page-home">
            <h1 class="text-4xl font-bold mb-6">Bienvenido a Remates El Paisa</h1>
            <p class="text-lg mb-4">¡Gestiona tus productos, pedidos y entregas de forma fácil y rápida!</p>
            <img src="img/products/limpiador.jpg" alt="Remates El Paisa" class="rounded-lg shadow w-full max-w-md mx-auto mb-6">
        </div>
    `;
}

function renderCatalogPage() {
    var main = document.querySelector('main');
    if (!main) return;
    main.innerHTML = `
        <div class="page active animate-fade-in" id="page-catalog">
            <h2 class="text-3xl font-bold mb-4">Catálogo de Productos</h2>
            <div id="catalog-grid" class="grid grid-cols-1 md:grid-cols-3 gap-6"></div>
        </div>
    `;
    renderProductCards();
}

function renderProductCards(filteredProducts) {
    filteredProducts = filteredProducts || mockProducts;
    var grid = document.getElementById('catalog-grid');
    if (!grid) return;
    grid.innerHTML = filteredProducts.map(function(product) {
        return `
            <div class="bg-white rounded-lg shadow p-4 flex flex-col animate-fade-in">
                <img src="${product.image}" alt="${product.name}" class="rounded-t-lg mb-4 h-40 object-cover w-full">
                <h3 class="font-semibold text-lg mb-2">${product.name}</h3>
                <p class="mb-2 text-gray-600">${product.description || ''}</p>
                <p class="mb-2 text-blue-600 font-bold text-xl">${formatCurrency(product.price)}</p>
                <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded mb-2">${product.stock} en stock</span>
                <button onclick="addToCart(${product.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition">Añadir al Carrito</button>
            </div>
        `;
    }).join('');
}

function renderCartPage() {
    var main = document.querySelector('main');
    if (!main) return;
    if (!cart || cart.length === 0) {
        main.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <h2 class="text-3xl font-bold mb-4">Tu Carrito está Vacío</h2>
                <p class="mb-4">Añade productos desde el catálogo para empezar a comprar.</p>
                <button onclick="navigateTo('catalog')" class="bg-blue-500 text-white px-6 py-2 rounded">Ir al Catálogo</button>
            </div>
        `;
        return;
    }
    var subtotal = cart.reduce(function(sum, item) { return sum + (item.product.price * item.quantity); }, 0);
    var shipping = 5000;
    var total = subtotal + shipping;
    main.innerHTML = `
        <div class="page active animate-fade-in" id="page-cart">
            <h2 class="text-3xl font-bold mb-4">Carrito de Compras</h2>
            <div class="mb-6">
                ${cart.map(function(item) {
                    return `
                        <div class="flex items-center justify-between mb-4 bg-white rounded-lg shadow p-4">
                            <div class="flex items-center gap-4">
                                <img src="${item.product.image}" alt="${item.product.name}" class="h-16 w-16 rounded-lg object-cover">
                                <div>
                                    <h3 class="font-semibold text-lg">${item.product.name}</h3>
                                    <p class="text-gray-600">${formatCurrency(item.product.price)} x ${item.quantity}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" min="1" value="${item.quantity}" onchange="updateCartQuantity(${item.product.id}, this.value)" class="w-16 px-2 py-1 border rounded">
                                <button onclick="removeFromCart(${item.product.id})" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <div class="flex justify-between mb-2"><span>Subtotal:</span><span>${formatCurrency(subtotal)}</span></div>
                <div class="flex justify-between mb-2"><span>Envío:</span><span>${formatCurrency(shipping)}</span></div>
                <div class="flex justify-between font-bold text-lg"><span>Total:</span><span>${formatCurrency(total)}</span></div>
            </div>
            <button onclick="navigateTo('checkout')" class="bg-blue-500 text-white px-6 py-2 rounded">Finalizar Compra</button>
        </div>
    `;
}

// --- FUNCIONES DEL CARRITO ---
function addToCart(productId) {
    var product = mockProducts.find(function(p) { return p.id === productId; });
    if (!product) return;
    var item = cart.find(function(i) { return i.product.id === productId; });
    if (item) {
        item.quantity += 1;
    } else {
        cart.push({ product: product, quantity: 1 });
    }
    showAlert('Producto agregado al carrito', 'success');
    renderCartPage();
    renderNav();
}

function updateCartQuantity(productId, newQuantity) {
    var item = cart.find(function(i) { return i.product.id === productId; });
    if (item) {
        item.quantity = parseInt(newQuantity, 10);
        if (item.quantity <= 0) {
            cart = cart.filter(function(i) { return i.product.id !== productId; });
        }
        renderCartPage();
        renderNav();
    }
}

function removeFromCart(productId) {
    cart = cart.filter(function(i) { return i.product.id !== productId; });
    renderCartPage();
    renderNav();
}

// --- NAVEGACIÓN ---
function navigateTo(page) {
    currentPage = page;
    renderNav();
    switch (page) {
        case 'home': renderHomePage(); break;
        case 'catalog': renderCatalogPage(); break;
        case 'cart': renderCartPage(); break;
        // Agrega aquí el resto de páginas y funciones según tu proyecto
        default: renderHomePage();
    }
}

// --- INICIALIZACIÓN ---
document.addEventListener('DOMContentLoaded', function() {
    renderNav();
    renderHomePage();
});