# Remates El Paisa — Desarrollo local

Este proyecto usa módulos ES (type="module") y debe servirse por HTTP para que los imports funcionen correctamente.

Opciones para desarrollo local (Windows PowerShell)

- Live Server (recomendado con VS Code)
  - Instala la extensión Live Server en VS Code.
  - Abre la carpeta del proyecto y presiona "Go Live" (abajo a la derecha) o usa la paleta de comandos: "Live Server: Open with Live Server".

- Usar npx (requiere Node.js)
  - Servir con http-server (rápido, sin instalar globalmente):
    ```powershell
    cd 'C:\Users\APRENDIZ\Desktop\VisualStudioSena - copia\Remates El Paisa'
    npx http-server -p 8000
    ```
  - Servir con serve (útil para SPA):
    ```powershell
    cd 'C:\Users\APRENDIZ\Desktop\VisualStudioSena - copia\Remates El Paisa'
    npx serve -l 8000
    ```

- Alternativa: usar los scripts npm incluidos
  - Instala dependencias (opcional):
    ```powershell
    cd 'C:\Users\APRENDIZ\Desktop\VisualStudioSena - copia\Remates El Paisa'
    npm install
    ```
  - Ejecutar server con http-server instalado localmente (script start):
    ```powershell
    npm run start
    ```
  - Ejecutar el script que usa npx directamente (no requiere instalar dependencias):
    ```powershell
    # Remates El Paisa — Prototipo Plataforma de Gestión

    Resumen
    -------
    Prototipo front-end modular para la gestión de catálogo, inventario, pedidos, repartidores, proveedores y usuarios. Los datos de ejemplo (mock) se cargan desde `js/modules/constants.js` y se persisten en `localStorage` por defecto. El proyecto está preparado para integrarse con un backend mediante un adaptador (`window.api`).

    Estructura del proyecto
    -----------------------
    - `index.html` — punto de entrada, contenedores de páginas y modales.
    - `css/` — estilos locales y overrides de Tailwind.
    - `img/` — imágenes del proyecto y `placeholder.svg` para fallbacks.
    - `js/main.js` — lógica de inicialización, navegación y registro de renderers.
    - `js/modules/` — módulos por responsabilidad:
      - `constants.js` — mock data, persistencia (localStorage), y `icons` SVG.
      - `catalog.js` — catálogo público y tarjetas de producto.
      - `inventory.js` — vista administrativa de inventario con CRUD y undo.
      - `orders.js` — pedidos (cliente y admin) y modal con detalles.
      - `delivery.js` — repartidores: panel admin y vista pública.
      - `providers.js` — proveedores y contacto.
      - `users.js` — usuarios y clientes.
      - `modals.js` — modales de edición y detalle.
      - `utils.js` — utilidades: toasts, formateo y animaciones mínimas.
    - `backend/` — demos y adaptadores de servidor (PHP, ejemplos).

    Archivos principales
    --------------------
    - `index.html`: contenedores de páginas, modales y carga del entrypoint `js/main.js`.
    - `js/main.js`: monta el menú según rol y expone los renders como funciones globales.
    - `js/modules/constants.js`: punto central de datos de ejemplo y funciones `save*Safe()`.

    Cómo preparar el entorno (Windows PowerShell)
    -------------------------------------------
    Opción rápida (Live Server en VS Code):

    - Instala la extensión Live Server y abre la carpeta del proyecto.
    - Haz clic en "Go Live" o usa la paleta: "Live Server: Open with Live Server".

    Usando Node.js (sin instalar dependencias globales):

    ```powershell
    cd 'C:\Users\APRENDIZ\Desktop\VisualStudioSena - copia\Remates El Paisa'
    npx http-server -p 8000
    # luego abrir: http://localhost:8000
    ```

    Descripción breve de cada módulo
    --------------------------------
    - `users.js` (Clientes/Usuarios): listados, edición básica y rol picker.
    - `delivery.js` (Repartidores): vista pública simplificada y panel admin con acciones. El módulo inyecta datos ficticios si no hay repartidores en `mockDeliveryMen`.

    ## Panel de Repartidores (admin)

    Se añadió una tabla responsiva y dinámica para la gestión de repartidores dentro de la vista administrativa (`#page-admin-delivery-men`). Características principales:

    - Columnas: ID, Nombre, Vehículo, Estado (badge verde/rojo), Entregas, Teléfono y Acciones (menú con Editar / Cambiar Estado / Eliminar).
    - Diseño minimalista y responsive usando utilidades Tailwind y reglas CSS específicas en `css/style.css`.
    - Está orientado a futuro: la tabla usa `mockDeliveryMen` y `mockOrders` para enriquecer datos; se puede ampliar con filtros, paginación o búsqueda integrando componentes JS o conectando un backend mediante `window.api`.

    Si deseas que implemente paginación, filtros o integraciones con un backend, dime cuál prefieres y lo agrego.
    - `providers.js` (Proveedores): CRUD local y botones de contacto (WhatsApp).
    - `inventory.js` (Inventario): lista compacta con badges de stock coloreadas, acciones con undo.
    - `orders.js` (Pedidos): listados de pedidos y modal enriquecido con cliente, repartidor, items, totales y asignación (admin).
    - `modals.js`: construcción dinámica de formularios y detalles; usa `makeWhatsBtn()` y helpers.
    - `utils.js`: toasts (incluye `showUndoToast`), formateadores y pequeñas utilidades.

    Animaciones y transiciones
    --------------------------
    - El proyecto usa animaciones mínimas (`.animate-fade-in`) para entradas de componentes. Evita transiciones tipo "diapositiva" en cambios de página.
    - Para actualizaciones en tiempo real (toggle, asignaciones), la UI re-renderiza localmente y muestra toasts; las animaciones son cortas para dar sensación de inmediatez.

    Preparado para integración con base de datos
    -------------------------------------------
    - Las funciones `save*Safe()` del módulo `constants.js` intentan llamar un adaptador `window.api` si existe. Para integrar con un backend basta implementar `window.api` con los métodos esperados:
      - `saveProducts(products)`, `saveCustomers(customers)`, `saveOrders(orders)`, `saveUsers(users)`, `saveDeliveryMen(deliveryMen)`, `saveProviders(providers)`.
    - Mantén las firmas (retornar promesas) y la UI utilizará esas funciones sin cambios adicionales.

    Recomendaciones para integración
    --------------------------------
    1. Implementar endpoints REST o GraphQL en el backend.
    2. Proveer un adaptador `window.api` en la carga inicial que haga peticiones y devuelva la estructura esperada.
    3. Planificar migración de IDs: `U001`, `P001`, `D001`, `C001`, `SKUxxxx` están en los mocks y facilitan el mapping con tablas.

    Notas finales
    ------------
    - He actualizado la pestaña de repartidores para que muestre datos ficticios si la lista está vacía, y añadí la vista pública vs admin.
    - Amplié el modal de "Ver detalles" de pedidos para incluir más campos y permitir asignar repartidor (admin).
    - Cambios en `utils.js` mejoran la visibilidad de toasts/Deshacer.

    Si quieres, puedo:
    - Añadir tests unitarios básicos (Vitest/Jest) para los renderers.
    - Preparar el adaptador `window.api` con ejemplos de endpoints (Node/PHP).
    - Pulir estilos y tamaños de iconos para consistencia visual.

    ---
    Para continuar con integración, dime el stack backend (PHP, Node, Python) y preparo los endpoints y el adaptador.
