/*
Archivo: js/modules/catalog.js
Descripción: Render y lógica del catálogo de productos.
Explicación: Funciones para listar productos, aplicar filtros y manejar acciones rápidas (añadir al carrito).
Notas: Separar render (DOM) de la fuente de datos para pruebas y futuras integraciones.
*/

// --- FUNCIONES DEL CATÁLOGO ---
import { mockProducts, getCurrentUserRole, normalizeImagePath } from './constants.js';
import { formatCurrency } from './utils.js';

export function renderCatalogPage() {
    const container = document.getElementById('page-catalog');
    if (!container) return;
    container.innerHTML = `
        <div class="animate-fade-in">
            <h2 class="text-3xl font-bold mb-4">Catálogo de Productos</h2>
            <div id="catalog-grid" class="grid grid-cols-1 md:grid-cols-3 gap-6"></div>
        </div>
    `;
    renderProductCards();
}

function renderProductCards(filteredProducts = mockProducts) {
    const grid = document.getElementById('catalog-grid');
    if (!grid) return;
    const role = getCurrentUserRole();
    grid.innerHTML = filteredProducts.map(product => `
        <div class="bg-white rounded-lg shadow p-4 flex flex-col animate-fade-in">
            <div class="h-40 w-full bg-slate-100 rounded-t-lg mb-4 overflow-hidden flex items-center justify-center">
                <img src="${normalizeImagePath(product.image || 'img/placeholder.svg')}" alt="${product.name || ''}" class="object-cover h-full w-full cursor-pointer" onclick="event.preventDefault(); window.openProductModal && window.openProductModal(${product.id})">
            </div>
            <h3 class="font-semibold text-lg mb-2">${product.name || ''}</h3>
            <p class="mb-2 text-gray-600">${product.description || ''}</p>
            <p class="mb-2 text-blue-600 font-bold text-xl">${formatCurrency(product.price || 0)}</p>
            <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded mb-2">${product.stock || 0} en stock</span>
            ${role === 'admin' ? '<div class="text-sm text-slate-500">Vista administrativa (sin añadir al carrito)</div>' : `<button onclick="event.preventDefault(); window.addToCart && window.addToCart(${product.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition">Añadir al Carrito</button>`}
        </div>
    `).join('');
}