# Administrador — Resumen rápido

Breve documentación para la carpeta `Administrador` (vista de administración).

- **Propósito:** UI estática para la administración del sitio (pedidos, usuarios, inventario, repartidores, reportes).
- **Archivos clave:**
  - `index.html` — entrada principal de la vista administrador.
  - `css/style.css` — estilos principales (ajustar aquí para cambios visuales).
  - `html/` — vistas parciales y páginas (Pedidos, Usuarios, Inventario, etc.).
  - `js/view-loader.js` — carga las vistas y controla navegación local.

- **Datos y persistencia:** Esta vista usa datos estáticos y/o mocks; revisa `js` en la carpeta raíz del proyecto para adaptar adaptadores o `localStorage`.
- **Cómo probar localmente:**
  1. Servir el proyecto por HTTP (los módulos ES requieren servidor). Ejemplo rápido:
     - `npx http-server -p 8000` o usar Live Server de VS Code.
  2. Abrir `RematesElPaisa Views/Administrador/index.html` en el navegador.

- **Dónde editar tablas y estilos:** modificar `css/style.css` (clases: `.table-container`, `.orders-table`, `table`, `th`, `td`).
- **Responsividad:** el CSS incluye reglas `@media` para ajustar el sidebar y el contenido; si aumentas el ancho de la tabla, revisa `min-width` y `max-width` en `.table-container`.

Notas cortas:
- Mantén referencias relativas (p. ej. `../img/` o `../js/`) coherentes cuando muevas archivos.
- Evita reemplazar arrays/objetos si integras con módulos JS que mantienen referencias.

Si quieres, puedo:
- Añadir ejemplos de snippets para conectar con un API back-end (fetch + `window.api`).
- Subir imágenes o íconos usados por el sidebar.
