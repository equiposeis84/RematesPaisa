// Funcionalidad básica para el dashboard de administración

document.addEventListener('DOMContentLoaded', function() {
    // Activar elementos del menú según la página actual
    const currentPath = window.location.pathname || '';
    const currentFile = (currentPath.split('/').pop() || window.location.href.split('/').pop() || 'inicioA.html').toLowerCase();
    const menuLinks = document.querySelectorAll('.main-nav a');

    menuLinks.forEach(link => {
        const href = link.getAttribute('href') || '';
        const hrefFile = href.split('/').pop() || '';
        if (hrefFile.toLowerCase() === currentFile || href.toLowerCase().endsWith(currentFile)) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // Restaurar estado del submenu (persistencia simple)
    (function restoreSubmenuState() {
        try {
            const parentLi = document.querySelector('.has-submenu');
            if (!parentLi) return;
            const toggle = parentLi.querySelector('.submenu-toggle');
            const submenu = parentLi.querySelector('.submenu');
            if (!toggle || !submenu) return;
            const wanted = localStorage.getItem('adm_gestion_open');
            const currentFileName = (window.location.pathname || '').split('/').pop() || window.location.href.split('/').pop() || '';
            const submenuLinks = Array.from(submenu.querySelectorAll('a')).map(a => (a.getAttribute('href')||'').split('/').pop());
            const belongs = submenuLinks.some(h => h && currentFileName && h.toLowerCase() === currentFileName.toLowerCase());
            if (wanted === 'true' || belongs) {
                toggle.setAttribute('aria-expanded', 'true');
                submenu.removeAttribute('hidden');
                parentLi.setAttribute('aria-expanded', 'true');
                parentLi.classList.add('open');
            } else {
                toggle.setAttribute('aria-expanded', 'false');
                submenu.setAttribute('hidden', '');
                parentLi.removeAttribute('aria-expanded');
                parentLi.classList.remove('open');
            }
        } catch (err) {
            // ignore storage errors
        }
    })();

    // Delegated click handler (single listener to avoid conflicts)
    document.body.addEventListener('click', function(e) {
        // If the user clicked a normal anchor (with href) that doesn't declare a data-action,
        // allow the browser to handle navigation naturally. This avoids JS accidentally
        // preventing link navigation (sidebar links, CTAs, etc.).
        const possibleAnchor = e.target.closest && e.target.closest('a[href]');
        if (possibleAnchor && !possibleAnchor.hasAttribute('data-action')) {
            return; // let browser navigate
        }

        const toggle = e.target.closest && e.target.closest('.submenu-toggle');
        if (toggle) {
            e.preventDefault();
            const parentLi = toggle.closest('.has-submenu');
            if (!parentLi) return;
            const submenu = parentLi.querySelector('.submenu');
            if (!submenu) return;
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            if (expanded) {
                toggle.setAttribute('aria-expanded', 'false');
                submenu.setAttribute('hidden', '');
                parentLi.removeAttribute('aria-expanded');
                parentLi.classList.remove('open');
                try { localStorage.setItem('adm_gestion_open', 'false'); } catch(e) {}
            } else {
                toggle.setAttribute('aria-expanded', 'true');
                submenu.removeAttribute('hidden');
                parentLi.setAttribute('aria-expanded', 'true');
                parentLi.classList.add('open');
                try { localStorage.setItem('adm_gestion_open', 'true'); } catch(e) {}
            }
            return;
        }

        // btn-danger (eliminar)
        const del = e.target.closest && e.target.closest('.btn-danger');
        if (del) {
            e.preventDefault();
            const row = del.closest('tr');
            const card = del.closest('.product-card');
            const item = row || card;
            const itemType = row ? 'elemento' : (card ? 'producto' : 'registro');
            if (confirm(`¿Estás seguro de que deseas eliminar este ${itemType}?`)) {
                if (item) {
                    item.style.opacity = '0';
                    item.style.transition = 'opacity 0.25s ease';
                    setTimeout(() => item.remove(), 250);
                }
            }
            return;
        }

        // data-action shortcuts
        const actionEl = e.target.closest && e.target.closest('[data-action]');
        if (actionEl) {
            e.preventDefault();
            const action = actionEl.getAttribute('data-action');
            if (action === 'open-catalog') {
                window.location.href = 'html/Catalogo.html';
            } else if (action === 'open-orders') {
                window.location.href = 'html/Pedidos.html';
            } else if (action === 'open-inventory') {
                window.location.href = 'html/inventario.html';
            } else {
                console.log('Acción no implementada:', action);
            }
            return;
        }

        // Paginación
        const pageBtn = e.target.closest && e.target.closest('.pagination button');
        if (pageBtn) {
            e.preventDefault();
            const all = document.querySelectorAll('.pagination button');
            all.forEach(b => b.classList.remove('active'));
            pageBtn.classList.add('active');
            console.log('Cargando página: ', pageBtn.textContent.trim());
            return;
        }

        // Product card click (avoid opening when clicking controls)
        const card = e.target.closest && e.target.closest('.product-card');
        if (card && !e.target.closest('.product-actions')) {
            console.log('Ver detalles del producto (simulado)');
            return;
        }

        // Anchors styled as .btn: let natural navigation happen
        const aBtn = e.target.closest && e.target.closest('a.btn');
        if (aBtn) return;
    });

    // Search boxes (attach handlers directly to inputs/buttons)
    const searchBoxes = document.querySelectorAll('.search-box');
    searchBoxes.forEach(box => {
        const btn = box.querySelector('button');
        const input = box.querySelector('input');
        if (!btn || !input) return;
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const term = input.value.trim();
            if (term) {
                console.log('Buscando: ', term);
            } else {
                input.focus();
            }
        });
    });

    console.log('Dashboard de Remates El Paísa cargado correctamente');
});