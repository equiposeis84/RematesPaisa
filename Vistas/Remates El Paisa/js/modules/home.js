/*
Archivo: js/modules/home.js
Descripción: Lógica y render para la página de inicio (dashboard o landing).
Explicación: Contiene funciones para mostrar widgets, resúmenes y accesos rápidos.
Importante: Mantener las métricas actualizables desde los mocks/estado central.
*/

import { icons } from './constants.js';

export function renderHomePage() {
    const container = document.getElementById('page-home');
    if (!container) return;
    container.innerHTML = `
        <div class="animate-fade-in">
            <h2 class="text-3xl font-bold text-slate-800">Bienvenidos a la Plataforma de Gestión</h2>
            <p class="mt-2 text-slate-600">Hemos diseñado esta plataforma para optimizar la gestión de tus pedidos y el control de inventario en Remates El Paisa.</p>
            <div class="grid md:grid-cols-2 gap-6 mt-8">
                     <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow">
                          <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4 text-custom-blue">
                              ${icons.catalog}
                          </div>
                    <h3 class="text-xl font-semibold text-slate-800">Acceder al Catálogo Dinámico</h3>
                    <p class="text-slate-500 mt-2">Explora nuestro catálogo actualizado con los mejores productos para el hogar.</p>
                    <button onclick="window.navigateTo && window.navigateTo('catalog')" class="mt-4 bg-custom-blue text-white font-semibold py-2 px-4 rounded-lg hover:bg-custom-blue-dark transition-colors w-full">Ver Catálogo</button>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4 text-custom-blue">
                        ${icons.orders}
                    </div>
                    <h3 class="text-xl font-semibold text-slate-800">Mis pedidos y seguimiento</h3>
                    <p class="text-slate-500 mt-2">Consulta el estado de tus pedidos y revisa tu historial de compras.</p>
                    <button onclick="window.navigateTo && window.navigateTo('orders')" class="mt-4 bg-custom-blue text-white font-semibold py-2 px-4 rounded-lg hover:bg-custom-blue-dark transition-colors w-full">Consultar Pedidos</button>
                </div>
            </div>
        </div>
    `;
}