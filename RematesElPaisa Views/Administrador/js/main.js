// Funcionalidad básica para el dashboard de administración

document.addEventListener('DOMContentLoaded', function() {
    // Activar elementos del menú según la página actual (mejor manejando rutas relativas)
    const currentPath = window.location.pathname || '';
    // Cuando se usa file:// en Windows el pathname incluye segmentos; tomamos la última porción
    const currentFile = (currentPath.split('/').pop() || window.location.href.split('/').pop() || 'inicioA.html').toLowerCase();
    const menuLinks = document.querySelectorAll('.main-nav a');

    menuLinks.forEach(link => {
        const href = link.getAttribute('href') || '';
        const hrefFile = href.split('/').pop() || '';
        // Comparamos en minúsculas para evitar problemas de mayúsculas
        if (hrefFile.toLowerCase() === currentFile || href.toLowerCase().endsWith(currentFile)) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // Delegación de eventos para botones y acciones dinámicas
    document.body.addEventListener('click', function(e) {
        const target = e.target.closest && e.target.closest('.btn, a.btn, button');
        if (!target) return;

        // Manejar botones con clase btn-danger (eliminar)
        if (target.classList.contains('btn-danger')) {
            e.preventDefault();
            const row = target.closest('tr');
            const card = target.closest('.product-card');
            const item = row || card;
            const itemType = row ? 'elemento' : (card ? 'producto' : 'registro');

            // Usar confirm accesible
            if (confirm(`¿Estás seguro de que deseas eliminar este ${itemType}?`)) {
                console.log('Elemento eliminado');
                if (item) {
                    item.style.opacity = '0';
                    item.style.transition = 'opacity 0.25s ease';
                    setTimeout(() => item.remove(), 250);
                }
            }
            return;
        }

        // Manejar botones que son enlaces (anchor styled as .btn)
        if (target.tagName.toLowerCase() === 'a' && target.classList.contains('btn')) {
            // Dejar el comportamiento por defecto para la navegación normal
            return;
        }

        // Manejar botones con data-action para acciones JS
        const action = target.getAttribute('data-action');
        if (action) {
            e.preventDefault();
            if (action === 'open-catalog') {
                window.location.href = 'html/Catalogo.html';
            } else if (action === 'open-orders') {
                window.location.href = 'html/Pedidos.html';
            } else if (action === 'open-inventory') {
                window.location.href = 'html/inventario.html';
            } else {
                console.log('Acción no implementada:', action);
            }
        }
    });

    // Paginación: usar delegación también
    document.body.addEventListener('click', function(e) {
        const pageBtn = e.target.closest && e.target.closest('.pagination button');
        if (!pageBtn) return;
        e.preventDefault();
        const all = document.querySelectorAll('.pagination button');
        all.forEach(b => b.classList.remove('active'));
        pageBtn.classList.add('active');
        console.log('Cargando página: ', pageBtn.textContent.trim());
    });

    // Delegación para tarjetas de producto (evita conflictos con botones dentro)
    document.body.addEventListener('click', function(e) {
        const card = e.target.closest && e.target.closest('.product-card');
        if (!card) return;
        // Si el click fue en un botón de acciones, no abrir detalles
        if (e.target.closest('.product-actions')) return;
        console.log('Ver detalles del producto (simulado)');
    });

    // Manejar caja de búsqueda (soporta botón dentro de .search-box)
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
                // Llamar a función de búsqueda o filtrar la tabla/productos
            } else {
                // Enfocar el input si está vacío
                input.focus();
            }
        });
    });

    console.log('Dashboard de Remates El Paísa cargado correctamente');
});