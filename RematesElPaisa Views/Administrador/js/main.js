// Funcionalidad básica para el dashboard de administración

document.addEventListener('DOMContentLoaded', function() {
    // Activar elementos del menú según la página actual
    const currentPage = window.location.pathname.split('/').pop();
    const menuLinks = document.querySelectorAll('.main-nav a');
    
    menuLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
    
    // Funcionalidad para los botones de eliminar
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const itemType = this.closest('tr') ? 'elemento' : 'producto';
            if (confirm(`¿Estás seguro de que deseas eliminar este ${itemType}?`)) {
                // Aquí iría la lógica para eliminar el elemento
                console.log('Elemento eliminado');
                // Simulamos la eliminación con una animación
                const item = this.closest('tr') || this.closest('.product-card');
                item.style.opacity = '0';
                item.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    item.remove();
                }, 300);
            }
        });
    });
    
    // Funcionalidad para la paginación
    const paginationButtons = document.querySelectorAll('.pagination button');
    paginationButtons.forEach(button => {
        button.addEventListener('click', function() {
            paginationButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            // Aquí iría la lógica para cargar la página correspondiente
            console.log('Cargando página: ', this.textContent);
        });
    });
    
    // Funcionalidad para las tarjetas de productos
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Evita que se active al hacer clic en los botones
            if (!e.target.closest('.product-actions')) {
                console.log('Ver detalles del producto');
                // Aquí iría la lógica para ver detalles
            }
        });
    });
    
    // Funcionalidad para la búsqueda
    const searchButtons = document.querySelectorAll('.search-box button');
    searchButtons.forEach(button => {
        button.addEventListener('click', function() {
            const searchInput = this.previousElementSibling;
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                console.log('Buscando: ', searchTerm);
                // Aquí iría la lógica de búsqueda
            }
        });
    });
    
    // Simular carga de datos
    console.log('Dashboard de Remates El Paísa cargado correctamente');
});