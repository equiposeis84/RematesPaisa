/*
Archivo: js/modules/api.js
Descripción: Adaptador para persistencia que sustituye llamadas al backend por operaciones locales.
Explicación: Exporta funciones asíncronas `saveProducts`, `saveCustomers`, `saveOrders`, `saveUsers` que delegan en `constants.js` para persistencia en localStorage.
Importante: Mantener la interfaz asíncrona (promesas) para compatibilidad futura con un backend real.
*/

// Lightweight API adapter: tries to call backend endpoints, falls back to localStorage helpers.
/**
 * api.js
 *
 * Adaptador ligero de persistencia para entornos sin backend PHP.
 * Reemplaza llamadas a endpoints PHP por llamadas directas a las
 * funciones de persistencia en `constants.js`. Mantiene la misma
 * interfaz asíncrona (promesas) para que el resto del código no
 * necesite cambios.
 */
import { saveProductsToStorage, saveCustomersToStorage, saveOrdersToStorage, saveUsersToStorage } from './constants.js';

/**
 * Guarda una lista de productos.
 * @param {Array} products - Array de objetos producto.
 * @returns {Promise<boolean>} true si se guardó correctamente.
 */
export async function saveProducts(products) {
    try {
        // En el modo "solo frontend" guardamos en localStorage
        saveProductsToStorage(products);
        return true;
    } catch (e) {
        console.warn('saveProducts fallo:', e);
        return false;
    }
}

/**
 * Guarda una lista de clientes.
 * @param {Array} customers - Array de objetos cliente.
 * @returns {Promise<boolean>} true si se guardó correctamente.
 */
export async function saveCustomers(customers) {
    try {
        saveCustomersToStorage(customers);
        return true;
    } catch (e) {
        console.warn('saveCustomers fallo:', e);
        return false;
    }
}

/**
 * Guarda una lista de pedidos.
 * @param {Array} orders - Array de objetos pedido.
 * @returns {Promise<boolean>} true si se guardó correctamente.
 */
export async function saveOrders(orders) {
    try {
        saveOrdersToStorage(orders);
        return true;
    } catch (e) {
        console.warn('saveOrders fallo:', e);
        return false;
    }
}

/**
 * Guarda la colección de usuarios.
 * @param {Object} users - Objeto con usuarios indexados por email.
 * @returns {Promise<boolean>} true si se guardó correctamente.
 */
export async function saveUsers(users) {
    try {
        saveUsersToStorage(users);
        return true;
    } catch (e) {
        console.warn('saveUsers fallo:', e);
        return false;
    }
}

export default { saveProducts, saveCustomers, saveOrders, saveUsers };
