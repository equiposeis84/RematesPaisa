/**
 * constants.js
 *
 * Descripción:
 * Este módulo contiene constantes, datos de ejemplo (mocks) y funciones
 * de persistencia local para la aplicación "Remates El Paisa".
 *
 * Contenido principal:
 * - Variables de estado de la aplicación (página actual, rol, sesión)
 * - Datos de ejemplo: usuarios, clientes, repartidores, productos y pedidos
 * - Funciones para guardar/cargar datos desde localStorage
 * - Iconos SVG y definiciones de menús de navegación
 *
 * Notas:
 * - Todo está documentado en español para facilitar mantenimiento.
 * - En producción estos mocks deben reemplazarse por llamadas al backend.
 */

/*
Archivo: js/modules/constants.js
Descripción: Contiene constantes globales, mocks y funciones de persistencia (localStorage).
Explicación: Define claves de localStorage, datos de ejemplo (mockProducts, mockOrders) y funciones `save*` / `load*` para usar en ausencia de backend.
Funciones importantes:
 - saveProductsToStorage(products): guarda productos en localStorage.
 - loadStateFromStorage(): carga el estado completo desde localStorage.
Notas: No almacenar información sensible en localStorage en producción.
*/

// --- ESTADO DE LA APLICACIÓN ---
let _currentPage = 'home';
let _currentUserRole = 'guest';
let _activeSubMenu = null;
let _cart = [];
let _currentChart = null;
let _currentEditItem = null;
let _currentEditType = null;
let _nextPage = null;
let _currentUserEmail = null; // email del usuario actualmente conectado

/**
 * Obtiene la página actual de la aplicación.
 * @returns {string}
 */
export function getCurrentPage() { return _currentPage; }
/**
 * Establece la página actual de la aplicación.
 * @param {string} v - Nombre de la página.
 */
export function setCurrentPage(v) { _currentPage = v; }

/**
 * Obtiene el rol del usuario actual.
 * @returns {string}
 */
export function getCurrentUserRole() { return _currentUserRole; }
export function getActiveSubMenu() { return _activeSubMenu; }
export function setActiveSubMenu(v) { _activeSubMenu = v; }
export function getCart() { return _cart; }
export function setCart(v) { _cart = v; }
export function getCurrentChart() { return _currentChart; }
export function setCurrentChart(v) { _currentChart = v; }
export function getCurrentEditItem() { return _currentEditItem; }
export function setCurrentEditItem(v) { _currentEditItem = v; }
export function getCurrentEditType() { return _currentEditType; }
export function setCurrentEditType(v) { _currentEditType = v; }
export function getNextPage() { return _nextPage; }
export function setNextPage(v) { _nextPage = v; }
export function getCurrentUserEmail() { return _currentUserEmail; }
/**
 * Establece el rol del usuario actual y persiste la sesión.
 * @param {string} v - rol ('guest'|'admin'|'customer'|'delivery')
 */
export function setCurrentUserRole(v) { _currentUserRole = v; try { persistSession(); } catch (e) { /* noop */ } }

/**
 * Establece el email del usuario actual y persiste la sesión.
 * @param {string} v - email del usuario conectado
 */
export function setCurrentUserEmail(v) { _currentUserEmail = v; try { persistSession(); } catch (e) { /* noop */ } }

// Cola de validaciones de pedidos y peticiones administrativas (simuladas)
export const pendingValidations = [];
export const pendingAdminRequests = [];

// --- DATOS DE EJEMPLO ---
export const mockUsers = {
    'admin@remates.com': { role: 'admin', name: 'Admin General', password: '123' },
    'repartidor@remates.com': { role: 'delivery', name: 'Juan Pérez', password: '123' },
    'cliente@remates.com': { role: 'customer', name: 'Ana García', password: '123' },
};

// ensure each user has a stable id for integration (e.g., U001)
(() => {
    try {
        const emails = Object.keys(mockUsers);
        emails.forEach((email, idx) => {
            const u = mockUsers[email];
            if (!u.id) u.id = 'U' + String(idx + 1).padStart(3, '0');
        });
    } catch (e) { /* noop */ }
})();

export const mockCustomers = [
    { id: 'C001', name: 'Ana García', email: 'cliente@remates.com', phone: '3001234567', address: 'Calle 26 #15-30, Bogotá', registered: '2023-01-15', orders: 12,
        paymentMethods: [
            { id: 'pm_1', brand: 'visa', last4: '4242', exp: '12/26', holder: 'ANA G.', masked: '**** **** **** 4242' }
        ],
        // ejemplo de último mensaje recibido por WhatsApp
        lastWhatsAppMessage: { message: '¿Pueden confirmar mi pedido ORD-001?', at: '2025-08-28T12:34:00' }
    },
    { id: 'C002', name: 'Carlos Sanchez', email: 'carlos.s@example.com', phone: '3109876543', address: 'Carrera 7 #45-20, Bogotá', registered: '2023-03-22', orders: 8 }
];

export const mockDeliveryMen = [
    { id: 'D001', name: 'Juan Pérez', email: 'repartidor@remates.com', phone: '3007654321', vehicle: 'Moto', licensePlate: 'ABC123', status: 'Activo' },
    { id: 'D002', name: 'Pedro Ramirez', email: 'pedro.r@remates.com', phone: '3109876543', vehicle: 'Bicicleta', licensePlate: null, status: 'Activo' },
    { id: 'D003', name: 'Sofia Castro', email: 'sofia.c@remates.com', phone: '3158765432', vehicle: 'Moto', licensePlate: 'XYZ789', status: 'Inactivo' }
];

// Ensure at least one demo repartidor exists for environments where localStorage is empty or data was cleared
(function ensureDemoDelivery() {
    try {
        if (!Array.isArray(mockDeliveryMen) || mockDeliveryMen.length === 0) {
            mockDeliveryMen.splice(0, mockDeliveryMen.length, { id: 'D001', name: 'Juan Pérez', email: 'repartidor@remates.com', phone: '3007654321', vehicle: 'Moto', licensePlate: 'ABC123', status: 'Activo' });
        } else {
            const exists = mockDeliveryMen.find(d => d.name === 'Juan Pérez' || d.email === 'repartidor@remates.com');
            if (!exists) mockDeliveryMen.unshift({ id: 'D' + String((mockDeliveryMen.length ? Math.max(...mockDeliveryMen.map(x => Number(x.id.replace(/\D/g, '') || 0))) + 1 : 1)).padStart(3, '0'), name: 'Juan Pérez', email: 'repartidor@remates.com', phone: '3007654321', vehicle: 'Moto', licensePlate: 'ABC123', status: 'Activo' });
        }
    } catch (err) { /* noop */ }
})();

export const mockProducts = [
    {
        id: 1,
        name: 'Limpiador Multiusos Floral',
        price: 12500,
        stock: 150,
    image: 'img/products/limpiador.jpg',
    category: 'Limpieza',
    description: 'Botella de 1L. Aroma fresco y duradero que perfuma todo tu hogar.'
    },
    {
        id: 2,
        name: 'Detergente Líquido para Ropa',
        price: 28000,
        stock: 85,
    image: 'img/products/detergente.jpg',
    category: 'Ropa',
    description: 'Rinde hasta 50 lavadas. Su fórmula avanzada cuida los colores y elimina manchas.'
    },
    {
        id: 3,
        name: 'Lavaloza Líquido Limón',
        price: 8900,
        stock: 210,
    image: 'img/products/lavaloza.jpg',
    category: 'Cocina',
    description: 'Arranca la grasa más difícil con su poder corta grasa. Botella de 750ml.'
    },
    {
        id: 4,
        name: 'Paño de Microfibra (Paquete x3)',
        price: 10500,
        stock: 60,
    image: 'img/products/microfibra.jpg',
    category: 'Accesorios',
    description: 'Paquete de 3 paños ultra absorbentes para limpieza general.'
    },
    {
        id: 5,
        name: 'Bolsas de Basura (Rollo x30)',
        price: 6500,
        stock: 120,
    image: 'img/products/bolsas.jpg',
    category: 'Desechos',
    description: 'Rollo de 30 bolsas resistentes para basura doméstica.'
    },
    {
        id: 6,
        name: 'Ambientador en Aerosol Lavanda',
        price: 9900,
        stock: 75,
    image: 'img/products/ambientador.jpg',
    category: 'Aromatizantes',
    description: 'Aroma lavanda, elimina olores y refresca el ambiente.'
    }
];

export const mockOrders = [
    {
        id: 'ORD-001',
        customer: 'Ana García',
        customerEmail: 'cliente@remates.com',
        deliveryMan: 'Juan Pérez',
        date: '2025-08-28',
        items: [ { sku: 1, name: 'Limpiador Multiusos Floral', qty: 2, price: 12500 } ],
        subtotal: 25000,
        shipping: 4500,
        total: 29500,
        status: 'Procesando',
        test: true
    },
    {
        id: 'ORD-002',
        customer: 'Carlos Sanchez',
        customerEmail: 'carlos.s@example.com',
        deliveryMan: null,
        date: '2025-08-27',
        items: [ { sku: 3, name: 'Lavaloza Líquido Limón', qty: 1, price: 8900 } ],
        subtotal: 8900,
        shipping: 3500,
        total: 12400,
        status: 'Listo'
    }
    ,{
        id: 'ORD-003',
        customer: 'Luisa Fernandez',
        customerEmail: 'luisa.f@example.com',
        deliveryMan: 'Pedro Ramirez',
        date: '2025-08-26',
        items: [ { sku: 2, name: 'Detergente Líquido para Ropa', qty: 1, price: 28000 }, { sku: 5, name: 'Bolsas de Basura (Rollo x30)', qty: 2, price: 6500 } ],
        subtotal: 41000,
        shipping: 5000,
        total: 46000,
    status: 'Entregado'
    },{
        id: 'ORD-004',
        customer: 'Miguel Rodriguez',
        customerEmail: 'miguel.r@example.com',
        deliveryMan: 'Juan Pérez',
        date: '2025-08-25',
        items: [ { sku: 4, name: 'Paño de Microfibra (Paquete x3)', qty: 1, price: 10500 } ],
        subtotal: 10500,
        shipping: 3500,
        total: 14000,
    status: 'Procesando'
    },{
        id: 'ORD-005',
        customer: 'Ana García',
        customerEmail: 'cliente@remates.com',
        deliveryMan: null,
        date: '2025-08-24',
        items: [ { sku: 6, name: 'Ambientador en Aerosol Lavanda', qty: 3, price: 9900 } ],
        subtotal: 29700,
        shipping: 4500,
        total: 34200,
        status: 'Cancelado'
    }
];

// --- PROVEEDORES (mock) ---
export const mockProviders = [
    { id: 'P001', name: 'Distribuciones López', phone: '3101234567', email: 'ventas@distribucioneslopez.com', products: ['Detergente Líquido', 'Bolsas de Basura'], contact: 'Carlos López' },
    { id: 'P002', name: 'Suministros Casa', phone: '3159876543', email: 'contacto@suministroscasa.com', products: ['Ambientadores', 'Microfibras'], contact: 'María Ortiz' }
];

// Ensure stable IDs for providers, products, customers, and delivery men
(() => {
    try {
        mockProviders.forEach((p, i) => { if (!p.id) p.id = 'P' + String(i + 1).padStart(3, '0'); });
        mockProducts.forEach((pr, i) => { if (!pr.id) pr.id = i + 1; if (!pr.sku) pr.sku = 'SKU' + String(pr.id).padStart(4, '0'); });
        mockCustomers.forEach((c, i) => { if (!c.id) c.id = 'C' + String(i + 1).padStart(3, '0'); });
        mockDeliveryMen.forEach((d, i) => { if (!d.id) d.id = 'D' + String(i + 1).padStart(3, '0'); });
    } catch (e) { /* noop */ }
})();

export const deliveryTasks = [
    { id: 'ORD-006', deliveryDate: '2025-08-29', customerName: 'Carlos Sanchez', address: 'Carrera 7 #45-20, Bogotá', phone: '3109876543', items: ['Lavaloza 750ml'], estimatedTime: '10:30 - 11:00', total: 12400, status: 'Pendiente' },
    { id: 'ORD-007', deliveryDate: '2025-08-29', customerName: 'Ana García', address: 'Calle 26 #15-30, Bogotá', phone: '3001234567', items: ['Limpiador Multiusos Floral x2'], estimatedTime: '12:00 - 13:00', total: 29500, status: 'Pendiente' }
];

export const deliveryHistory = [
    { id: 'ORD-003', customerName: 'Luisa Fernandez', total: 46000, status: 'Entregado', completedAt: '2025-08-26 15:20' }
];

// --- PERSISTENCIA LOCAL (localStorage) ---
const STORAGE_KEYS = {
    products: 'remates_products_v1',
    customers: 'remates_customers_v1'
};

// add orders key
STORAGE_KEYS.orders = 'remates_orders_v1';
// add users key
STORAGE_KEYS.users = 'remates_users_v1';
// add delivery men key
STORAGE_KEYS.deliveryMen = 'remates_delivery_men_v1';
// providers key
STORAGE_KEYS.providers = 'remates_providers_v1';
// session key (current user/email/role)
STORAGE_KEYS.session = 'remates_session_v1';

/**
 * Guarda la lista de productos en localStorage (mocks -> persistencia local).
 */
export function saveProductsToStorage() {
    try {
        localStorage.setItem(STORAGE_KEYS.products, JSON.stringify(mockProducts));
    } catch (e) {
        console.warn('No se pudo guardar products en localStorage', e);
    }
}

/**
 * Persiste la sesión actual (email y role) en localStorage.
 */
export function persistSession() {
    try {
        const payload = { email: _currentUserEmail, role: _currentUserRole };
        localStorage.setItem(STORAGE_KEYS.session, JSON.stringify(payload));
    } catch (e) { /* noop */ }
}

/**
 * Guarda la lista de clientes en localStorage.
 */
export function saveCustomersToStorage() {
    try {
        localStorage.setItem(STORAGE_KEYS.customers, JSON.stringify(mockCustomers));
    } catch (e) {
        console.warn('No se pudo guardar customers en localStorage', e);
    }
}

/**
 * Guarda la lista de pedidos en localStorage.
 * @param {Array} orders - Array de pedidos a guardar (opcional si se usa mockOrders global).
 */
export function saveOrdersToStorage(orders) {
    try {
        if (!orders) return;
        localStorage.setItem(STORAGE_KEYS.orders, JSON.stringify(orders));
    } catch (e) {
        console.warn('No se pudo guardar orders en localStorage', e);
    }
}

/**
 * Guarda los usuarios (objeto) en localStorage.
 */
export function saveUsersToStorage() {
    try {
        localStorage.setItem(STORAGE_KEYS.users, JSON.stringify(mockUsers));
    } catch (e) {
        console.warn('No se pudo guardar users en localStorage', e);
    }
}

/**
 * Guarda repartidores en localStorage.
 */
export function saveDeliveryMenToStorage() {
    try {
        localStorage.setItem(STORAGE_KEYS.deliveryMen, JSON.stringify(mockDeliveryMen));
    } catch (e) {
        console.warn('No se pudo guardar deliveryMen en localStorage', e);
    }
}

/**
 * Guarda proveedores en localStorage.
 */
export function saveProvidersToStorage() {
    try {
        localStorage.setItem(STORAGE_KEYS.providers, JSON.stringify(mockProviders));
    } catch (e) {
        console.warn('No se pudo guardar providers en localStorage', e);
    }
}

/**
 * Carga el estado (productos, clientes, pedidos, usuarios, repartidores) desde localStorage
 * y actualiza los mocks en memoria.
 */
export function loadStateFromStorage() {
    try {
        const prod = localStorage.getItem(STORAGE_KEYS.products);
        if (prod) {
            const parsed = JSON.parse(prod);
            if (Array.isArray(parsed)) {
                // replace contents of mockProducts while keeping reference
                mockProducts.splice(0, mockProducts.length, ...parsed);
            }
        }
        const cust = localStorage.getItem(STORAGE_KEYS.customers);
        if (cust) {
            const parsedC = JSON.parse(cust);
            if (Array.isArray(parsedC)) {
                mockCustomers.splice(0, mockCustomers.length, ...parsedC);
            }
        }
        const ord = localStorage.getItem(STORAGE_KEYS.orders);
        if (ord) {
            const parsedO = JSON.parse(ord);
            if (Array.isArray(parsedO)) {
                mockOrders.splice(0, mockOrders.length, ...parsedO);
            }
        }
        const us = localStorage.getItem(STORAGE_KEYS.users);
        if (us) {
            const parsedU = JSON.parse(us);
            if (parsedU && typeof parsedU === 'object') {
                Object.keys(mockUsers).forEach(k => delete mockUsers[k]);
                Object.assign(mockUsers, parsedU);
            }
        }
        const dm = localStorage.getItem(STORAGE_KEYS.deliveryMen);
        if (dm) {
            const parsedD = JSON.parse(dm);
            if (Array.isArray(parsedD)) {
                mockDeliveryMen.splice(0, mockDeliveryMen.length, ...parsedD);
            }
        }
        // restore session (role + email)
        const ses = localStorage.getItem(STORAGE_KEYS.session);
        if (ses) {
            try {
                const parsedS = JSON.parse(ses);
                if (parsedS) {
                    if (parsedS.role) _currentUserRole = parsedS.role;
                    if (parsedS.email) _currentUserEmail = parsedS.email;
                }
            } catch (e) { /* noop */ }
        }
    } catch (e) {
        console.warn('No se pudo cargar estado desde localStorage', e);
    }
}

// intentar cargar estado al importar este módulo
try { loadStateFromStorage(); } catch (e) { /* noop */ }

// --- ICONOS SVG (puedes agregar más si los usas en el menú) ---
export const icons = {
    home: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>',
    catalog: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h7v7H3V3zM14 3h7v7h-7V3zM14 14h7v7h-7v-7zM3 14h7v7H3v-7z"/></svg>',
    orders: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 2h6a2 2 0 012 2v0a2 2 0 01-2 2h-6A2 2 0 017 4V4a2 2 0 012-2zM7 8h10M7 12h10M7 16h6"/></svg>',
    inventory: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>',
    cart: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A1 1 0 007 17h10a1 1 0 00.95-.68L21 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"/></svg>',
    help: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 14v.01M16 10h.01M12 10v.01M12 18a6 6 0 100-12 6 6 0 000 12z"/></svg>',
    login: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3m6-6l-6 6 6 6"/></svg>'
};

// Iconos adicionales usados por el menú
icons.users = '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11c1.657 0 3-1.343 3-3S17.657 5 16 5s-3 1.343-3 3 1.343 3 3 3zM6 11c1.657 0 3-1.343 3-3S7.657 5 6 5 3 6.343 3 8s1.343 3 3 3zM3 20a6 6 0 0118 0"/></svg>';
icons.reports = '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3v18M4 7h16M4 12h10M4 17h7"/></svg>';
icons.delivery = '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h13l3 5v5H6a3 3 0 01-3-3V7zM16 17h2a2 2 0 100-4h-2v4zM7 20a1 1 0 100-2 1 1 0 000 2zM17 20a1 1 0 100-2 1 1 0 000 2z"/></svg>';

// Small inline icons for actions (edit, toggle, trash)
icons.edit = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4h6l3 3v6M3 21v-3a4 4 0 014-4h3"/></svg>';
icons.trash = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5-4h4m-6 0h6"/></svg>';
icons.toggle = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="10" rx="5" ry="5"/><circle cx="8" cy="12" r="3"/></svg>';
// small WhatsApp / message indicator (green dot with chat bubble)
icons.whats = '<svg class="w-4 h-4 inline-block mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 12a9 9 0 10-3.7 7l.7.2.2-.7A9 9 0 0021 12z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2" fill="currentColor"/></svg>';

// Gear icon used across the app (same as profile settings gear)
export const gearIcon = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09c.67 0 1.24-.4 1.51-1a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06c.5.5 1.2.65 1.82.33.31-.17.6-.39.86-.66.26-.27.49-.58.66-.86.32-.62.17-1.32-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06c.5.5 1.2.65 1.82.33.28-.15.55-.33.78-.56a2 2 0 012.83 2.83c-.23.23-.41.5-.56.78-.32.62-.17 1.32.33 1.82l.06.06a2 2 0 012.83 2.83l-.06.06a1.65 1.65 0 00-1.51 1H21a2 2 0 010 4h-.09c-.67 0-1.24.4-1.51 1z"/></svg>';

export const navMenus = {
    admin: [
        { page: 'home', label: 'Inicio', icon: 'home' },
        { page: 'catalog', label: 'Catálogo', icon: 'catalog' },
        // Gestión: submenu con las herramientas administrativas
        { page: 'gestion', label: 'Gestión', icon: 'orders', children: [
            { page: 'inventory', label: 'Inventario', icon: 'inventory' },
            { page: 'orders', label: 'Pedidos', icon: 'orders' },
            { page: 'users', label: 'Usuarios', icon: 'users' },
            { page: 'reports', label: 'Reportes', icon: 'reports' },
            { page: 'clients', label: 'Clientes', icon: 'users' },
            { page: 'repartidores', label: 'Repartidores', icon: 'delivery' },
            { page: 'providers', label: 'Proveedores', icon: 'users' }
        ] },
        { page: 'help', label: 'Ayuda y Contacto', icon: 'help', bottom: true },
        { page: 'login', label: 'Cerrar Sesión', icon: 'login', bottom: true }
    ],
    delivery: [
        { page: 'home', label: 'Inicio', icon: 'home' },
        { page: 'delivery', label: 'Mis Entregas', icon: 'orders' },
        { page: 'help', label: 'Ayuda y Contacto', icon: 'help', bottom: true },
        { page: 'login', label: 'Cerrar Sesión', icon: 'login', bottom: true }
    ],
    customer: [
        { page: 'home', label: 'Inicio', icon: 'home' },
        { page: 'catalog', label: 'Catálogo', icon: 'catalog' },
    { page: 'profile', label: 'Perfil', icon: 'orders' },
        { page: 'orders', label: 'Mis Pedidos', icon: 'orders' },
        { page: 'cart', label: 'Carrito', icon: 'cart' },
        { page: 'help', label: 'Ayuda y Contacto', icon: 'help', bottom: true },
        { page: 'login', label: 'Cerrar Sesión', icon: 'login', bottom: true }
    ],
    guest: [
        { page: 'home', label: 'Inicio', icon: 'home' },
        { page: 'catalog', label: 'Catálogo', icon: 'catalog' },
        { page: 'orders', label: 'Mis Pedidos', icon: 'orders' },
        { page: 'cart', label: 'Carrito', icon: 'cart' },
        { page: 'help', label: 'Ayuda y Contacto', icon: 'help', bottom: true },
        { page: 'login', label: 'Iniciar Sesión', icon: 'login', bottom: true }
    ]
};

// Número de WhatsApp para soporte (usar formato internacional sin + ni espacios, p.ej. '573001234567')
export const whatsappNumber = '573001234567';

// Genera URL de WhatsApp (web o móvil) con mensaje opcional
export function getWhatsAppUrl(number, message) {
    const base = `https://wa.me/${number}`;
    if (!message) return base;
    return `${base}?text=${encodeURIComponent(message)}`;
}

// Safe wrappers that try backend via API adapter if present
let apiAdapter = null;
try {
    // dynamic import will fail in some static environments; guard it
    // eslint-disable-next-line no-undef
    apiAdapter = (window && window.api) || null;
} catch (e) { apiAdapter = null; }

export async function saveProductsSafe() {
    if (apiAdapter && apiAdapter.saveProducts) return await apiAdapter.saveProducts(mockProducts);
    saveProductsToStorage(); return true;
}

export async function saveCustomersSafe() {
    if (apiAdapter && apiAdapter.saveCustomers) return await apiAdapter.saveCustomers(mockCustomers);
    saveCustomersToStorage(); return true;
}

export async function saveOrdersSafe() {
    if (apiAdapter && apiAdapter.saveOrders) return await apiAdapter.saveOrders(mockOrders);
    saveOrdersToStorage(mockOrders); return true;
}

export async function saveUsersSafe() {
    if (apiAdapter && apiAdapter.saveUsers) return await apiAdapter.saveUsers(mockUsers);
    saveUsersToStorage(); return true;
}

export async function saveDeliveryMenSafe() {
    if (apiAdapter && apiAdapter.saveDeliveryMen) return await apiAdapter.saveDeliveryMen(mockDeliveryMen);
    saveDeliveryMenToStorage(); return true;
}

export async function saveProvidersSafe() {
    if (apiAdapter && apiAdapter.saveProviders) return await apiAdapter.saveProviders(mockProviders);
    saveProvidersToStorage(); return true;
}

// Normalize image path: if caller passes only a filename, ensure it points to img/products/<file>
export function normalizeImagePath(path) {
    if (!path) return 'img/placeholder.svg';
    // already absolute or starts with img/
    if (path.startsWith('http') || path.startsWith('/') || path.startsWith('img/')) return path;
    // if it's just a filename like 'lavaloza.jpg' or 'products/lavaloza.jpg', normalize
    const filename = path.split('/').pop();
    return `img/products/${filename}`;
}