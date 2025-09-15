<!-- .github/copilot-instructions.md: Guidance for AI coding agents working on RematesPaisa -->

Purpose
-------
Provide concise, actionable guidance so an AI coding agent can be immediately productive in this repository.

Top-level architecture (big picture)
------------------------------------
- This is a front-end prototype (static site) split across two folders: `CodigoAI/Remates El Paisa/` (original prototype) and `RematesElPaisa Views/` (multiple UI views: `Administrador`, `Cliente`, `Usuario`, etc.).
- Entry points are simple HTML pages that load `js/main.js` as an ES module (`<script type="module">`). Main UI logic is implemented in `js/modules/*` and small per-view JS under `RematesElPaisa Views/.../js`.
- Data flow: mock data lives in `js/modules/constants.js` (mockProducts, mockOrders, mockUsers, mockDeliveryMen, mockProviders). UI modules import from `constants.js` and operate on those in-memory mocks. Persistence defaults to `localStorage` via helper functions (`save*ToStorage`, `loadStateFromStorage`).
- Backend integration pattern: modules call `save*Safe()` wrappers which attempt to use a global `window.api` adapter (should expose methods like `saveProducts`, `saveUsers`, `saveOrders`, `saveCustomers`, `saveDeliveryMen`, `saveProviders`) and fall back to `localStorage` when the adapter is missing.

Developer workflows & commands
------------------------------
- Serve the site over HTTP (ES modules require it). Common options (documented in `CodigoAI/Remates El Paisa/readme.md`):
  - Use VS Code Live Server extension (recommended).
  - With Node.js: `npx http-server -p 8000` or `npx serve -l 8000` from the project folder.
  - If `package.json` exists, `npm run start` may be configured (inspect the file if present).
- Debugging tips:
  - Open Chrome/Edge devtools and inspect `window` for `api` adapter and application state (mocks and STORAGE_KEYS keys).
  - Use `localStorage` keys listed in `js/modules/constants.js` (e.g., `remates_products_v1`, `remates_users_v1`, `remates_orders_v1`) to reset or inspect persisted data.

Project-specific conventions & patterns
-------------------------------------
- ES module structure: `js/main.js` imports `js/modules/*` and mounts per-page renderers. Files use named exports from `constants.js` extensively.
- Data mutation pattern: modules mutate the exported mock arrays/objects in-place (e.g., `mockProducts.splice()` in `loadStateFromStorage`) to keep references stable across modules — do not replace exported arrays with new arrays if you want other modules to see updates.
- Persistence fallbacks: always prefer calling `save*Safe()` (returns a promise); the function will call `window.api` when present, otherwise call `save*ToStorage()`.
- Image paths: use `normalizeImagePath()` helper in `constants.js` to map filenames to `img/products/<file>`; it also returns `img/placeholder.svg` for missing images.

Integration points and expected adapter API
-----------------------------------------
- To integrate a backend implement `window.api` (global) before `js/main.js` runs. The adapter must expose asynchronous methods (return promises):
  - `saveProducts(products)`, `saveCustomers(customers)`, `saveOrders(orders)`, `saveUsers(users)`, `saveDeliveryMen(deliveryMen)`, `saveProviders(providers)`
- Example adapter placement: add a small inline `<script>` in `index.html` or load `js/modules/api.js` and set `window.api = { saveProducts: async (p)=>fetch(...), ... }`.
- `js/modules/api.js` exists as a local adapter that delegates to `constants.js` (useful as a reference implementation). Check it for expected signatures.

Files & locations you should look at first
-----------------------------------------
- `CodigoAI/Remates El Paisa/readme.md` — high-level project notes and local dev commands.
- `CodigoAI/Remates El Paisa/js/main.js` — app bootstrap, role-based menu mounting, and where `window.api` is set if an adapter exists.
- `CodigoAI/Remates El Paisa/js/modules/constants.js` — central source of truth for data shape, persistence keys, helper utilities (`save*Safe`, `loadStateFromStorage`, `normalizeImagePath`, `icons`).
- `CodigoAI/Remates El Paisa/js/modules/*.js` — feature modules: `users.js`, `inventory.js`, `orders.js`, `providers.js`, `delivery.js`, `modals.js`, `navigation.js`, `catalog.js`.
- `RematesElPaisa Views/` — multiple HTML views for admin/client; each includes `js/main.js` and small per-view scripts (e.g., `Administrador/js/sidebar.js`).

Style & UX notes that matter for code changes
-------------------------------------------
- The project uses small utility CSS and inlined Tailwind-like classes inside `css/style.css` (see `CodigoAI/Remates El Paisa/css/`). Keep markup class names consistent with existing patterns.
- UI modules expect simple, synchronous DOM renderers and often re-render whole sections; keep DOM updates small and preserve function signatures used by `main.js`.

Examples (do this, not that)
---------------------------
- Correct: call `await saveUsersSafe()` after mutating `mockUsers` to persist via adapter or localStorage.
- Wrong: assigning `mockUsers = {}` — this breaks references other modules rely on. Instead mutate the object in-place (e.g., `Object.assign(mockUsers, newObj)` or delete/add keys).

Edge cases & safety
-------------------
- Many modules swallow errors when persistence fails — add defensive checks when changing persistence (e.g., ensure `localStorage` is available before writing).
- When adding new mock data, follow existing ID formats: `U001`, `P001`, `D001`, `C001`, or numeric product `id` with optional `sku: 'SKUxxxx'`.

What to do first (starter tasks for an AI agent)
-----------------------------------------------
1. Run the site locally with Live Server or `npx http-server` and open `index.html` to observe app behavior.
2. Inspect `js/modules/constants.js` to understand data shapes and persistence keys.
3. If integrating a backend: implement `window.api` (async methods listed above) and test `save*Safe()` flows.

When unsure, reference these lines
---------------------------------
- `constants.js` — persistence functions and STORAGE_KEYS (search for `STORAGE_KEYS` and `save*Safe`).
- `main.js` — where renderers are mounted and where role/menu logic lives.

If you edit files
-----------------
- Preserve ES module imports/exports and don't change exported object references for mocks.
- Keep changes minimal and self-contained; update `readme.md` if you add new local dev commands or adapters.

Questions / Feedback
--------------------
If any part of the architecture or adapter signatures is unclear, ask for which page/view or module you plan to change — include the target file path and a brief goal (e.g., "implement server adapter for users saving via fetch in `index.html`").
