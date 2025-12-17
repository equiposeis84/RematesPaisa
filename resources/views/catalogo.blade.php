<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remates El Paísa - Catálogo</title>
    <link rel="stylesheet" href="/css/MainContent.css">
    <link rel="stylesheet" href="/css/SidebarStyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo"><h1>Remates El Paísa</h1></div>
            <div class="este">
                <nav class="main-nav">
                    <ul>
                        <li class="sidebar-fixed">
                            <a href="{{ route('catalogo') }}"> 
                                <span class="nav-icon"> <i class="fa-solid fa-store"></i> </span>Catálogo 
                            </a>
                        </li>
                        
                        <!-- ENLACE PARA ABRIR EL MODAL DEL CARRITO -->
                        <li class="sidebar-fixed">
                            <a href="#" onclick="openCartModal(); return false;"> 
                                <span class="nav-icon"> <i class="fa-solid fa-cart-shopping"></i> </span>Mi Carrito 
                            </a>
                        </li>
                        
                        <li class="sidebar-fixed">
                            @if(session()->has('user_id') && session('user_type') != 1)
                              <a href="{{ route('usuario.mis-pedidos') }}"> 
    <span class="nav-icon"> <i class="fa-solid fa-clipboard-list"></i> </span>Mis Pedidos 
</a>
                            @else
                                <a href="{{ route('login') }}" class="text-warning"> 
                                    <span class="nav-icon"> <i class="fa-solid fa-clipboard-list"></i> </span>Mis Pedidos 
                                    <small class="d-block text-muted">(Inicia sesión)</small>
                                </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
            
            <div class="space">
                <div class="space-item"></div>
            </div>

            <div class="user-section">
                <div class="user-info">
                    @if(session()->has('user_id'))
                        <p><strong>{{ session('user_name') }}</strong></p>
                        <p style="color: #777; font-size: 12px;">{{ session('user_email') }}</p>
                        <small class="badge bg-info">
                            @if(session('user_type') == 1)
                                Administrador
                            @elseif(session('user_type') == 2)
                                Cliente
                            @elseif(session('user_type') == 3)
                                Repartidor
                            @endif
                        </small>
                    @else
                        <p><strong>Invitado</strong></p>
                        <p style="color: #777; font-size: 12px;">Explora nuestro catálogo</p>
                    @endif
                </div>

                <nav class="secondary-nav">
                    <ul>
                        <li>
                            <a href="{{ route('usuario.ayuda.contacto') }}">
                                <span class="nav-icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                                <span class="nav-text">Ayuda y contacto</span>
                            </a>
                        </li>

                        <li>
                            @if(session()->has('user_id'))
                                <!-- FORMULARIO DE LOGOUT -->
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                        <span class="nav-icon">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                        </span>
                                        <span class="nav-text">Cerrar Sesión</span>
                                    </a>
                                </form>
                            @else
                                <a href="{{ route('login') }}">
                                    <span class="nav-icon">
                                        <i class="fa-solid fa-right-to-bracket"></i>
                                    </span>
                                    <span class="nav-text">Iniciar Sesión</span>
                                </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content">
            <section class="content-cards">
                <!-- CONTENIDO DEL CATÁLOGO -->
                <div class="container-fluid py-4">
                    <!-- Encabezado -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h1 class="display-6 mb-0">Catálogo de Productos</h1>
                            <p class="text-muted">Encuentra los mejores productos al mejor precio</p>
                            <hr>
                        </div>
                    </div>

                    <!-- Filtros y Productos -->
                    <div class="row mb-4">
                        <!-- Columna de Filtros -->
                        <div class="col-md-3">
                            <!-- Filtros -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('catalogo') }}">
                                        <div class="mb-3">
                                            <label class="form-label">Buscar producto:</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                <input type="text" class="form-control" name="search" 
                                                       placeholder="Nombre o categoría..." value="{{ $search ?? '' }}">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Categoría:</label>
                                            <select class="form-select" name="categoria">
                                                <option value="">Todas las categorías</option>
                                                @foreach($categorias ?? [] as $cat)
                                                    <option value="{{ $cat }}" {{ ($categoria ?? '') == $cat ? 'selected' : '' }}>
                                                        {{ $cat }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-filter"></i> Aplicar Filtros
                                            </button>
                                            <a href="{{ route('catalogo') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-undo"></i> Limpiar Filtros
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Resumen del Carrito -->
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Mi Carrito</h5>
                                </div>
                                <div class="card-body" id="cart-sidebar">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2 mb-0">Cargando carrito...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna de Productos -->
                        <div class="col-md-9">
                            @if(isset($producto) && ($activeTab ?? '') == 'detalle')
                                <!-- Vista de detalle del producto -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-body text-center">
                                                <i class="fas fa-box fa-5x text-primary mb-3"></i>
                                                <h2>{{ $producto->nombreProducto }}</h2>
                                                <p class="text-muted">{{ $producto->categoriaProducto }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <h4 class="card-title">Detalles del Producto</h4>
                                                <table class="table">
                                                    <tr>
                                                        <th>Precio:</th>
                                                        <td class="h4 text-danger">${{ number_format($producto->precioUnitario, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Stock disponible:</th>
                                                        <td>
                                                            @if($producto->stock > 0)
                                                                <span class="badge bg-success">{{ $producto->stock }} unidades</span>
                                                            @else
                                                                <span class="badge bg-danger">Agotado</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Proveedor:</th>
                                                        <td>{{ $producto->nombreProveedor ?? 'No especificado' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Entradas:</th>
                                                        <td>{{ $producto->entradaProducto }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Salidas:</th>
                                                        <td>{{ $producto->salidaProducto }}</td>
                                                    </tr>
                                                </table>
                                                
                                                @if($producto->stock > 0)
                                                    <div class="input-group mb-3">
                                                        <button class="btn btn-outline-secondary" type="button" 
                                                                onclick="decrementQuantity({{ $producto->idProductos }})">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" 
                                                               class="form-control text-center" 
                                                               id="quantity-{{ $producto->idProductos }}" 
                                                               value="1" 
                                                               min="1" 
                                                               max="{{ $producto->stock }}">
                                                        <button class="btn btn-outline-secondary" type="button" 
                                                                onclick="incrementQuantity({{ $producto->idProductos }})">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <button class="btn btn-primary w-100 mb-2" 
                                                            onclick="addToCart({{ $producto->idProductos }})">
                                                        <i class="fas fa-cart-plus"></i> Añadir al Carrito
                                                    </button>
                                                @else
                                                    <button class="btn btn-secondary w-100" disabled>
                                                        <i class="fas fa-times-circle"></i> Producto Agotado
                                                    </button>
                                                @endif
                                                
                                                <a href="{{ route('catalogo') }}" class="btn btn-outline-secondary w-100">
                                                    <i class="fas fa-arrow-left"></i> Volver al Catálogo
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Productos relacionados -->
                                @if($relacionados && $relacionados->count() > 0)
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h4>Productos relacionados</h4>
                                            <div class="row">
                                                @foreach($relacionados as $relacionado)
                                                    <div class="col-md-3 mb-3">
                                                        <div class="card h-100">
                                                            <div class="card-body">
                                                                <h6 class="card-title">{{ Str::limit($relacionado->nombreProducto, 30) }}</h6>
                                                                <p class="card-text text-danger">${{ number_format($relacionado->precioUnitario, 2) }}</p>
                                                                <a href="{{ route('catalogo.show', $relacionado->idProductos) }}" 
                                                                   class="btn btn-sm btn-outline-info w-100">
                                                                    Ver
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                            @else
                                <!-- Vista normal del catálogo -->
                                <!-- Información y controles -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <p class="mb-0">
                                        @if(isset($productos) && $productos->total())
                                            <strong>{{ $productos->total() }}</strong> productos encontrados
                                            @if($search ?? '')
                                                para "<strong>{{ $search }}</strong>"
                                            @endif
                                        @else
                                            <strong>0</strong> productos encontrados
                                        @endif
                                    </p>
                                </div>

                                <!-- Grid de Productos -->
                                <div class="row" id="products-grid">
                                    @if(isset($productos) && $productos->count() > 0)
                                        @foreach($productos as $producto)
                                            @php
                                                $stock = $producto->entradaProducto - $producto->salidaProducto;
                                            @endphp
                                            <div class="col-lg-4 col-md-6 mb-4">
                                                <div class="card h-100 shadow-sm">
                                                    <div class="position-relative">
                                                        <div class="bg-light text-center py-4">
                                                            <i class="fas fa-box fa-3x text-primary"></i>
                                                        </div>
                                                        <span class="position-absolute top-0 start-0 badge bg-dark m-2">
                                                            {{ $producto->categoriaProducto }}
                                                        </span>
                                                        @if($stock > 0)
                                                            <span class="position-absolute top-0 end-0 badge bg-success m-2">
                                                                {{ $stock }} disponibles
                                                            </span>
                                                        @else
                                                            <span class="position-absolute top-0 end-0 badge bg-danger m-2">
                                                                Agotado
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="card-body d-flex flex-column">
                                                        <h5 class="card-title" title="{{ $producto->nombreProducto }}">
                                                            {{ Str::limit($producto->nombreProducto, 40) }}
                                                        </h5>
                                                        
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <span class="h4 text-danger mb-0">
                                                                ${{ number_format($producto->precioUnitario, 2) }}
                                                            </span>
                                                            @if($stock > 0)
                                                                <span class="badge bg-success">Disponible</span>
                                                            @else
                                                                <span class="badge bg-danger">Agotado</span>
                                                            @endif
                                                        </div>
                                                        
                                                        @if($stock > 0)
                                                            <div class="input-group mb-3">
                                                                <button class="btn btn-outline-secondary" type="button" 
                                                                        onclick="decrementQuantity({{ $producto->idProductos }})">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number" 
                                                                       class="form-control text-center" 
                                                                       id="quantity-{{ $producto->idProductos }}" 
                                                                       value="1" 
                                                                       min="1" 
                                                                       max="{{ $stock }}">
                                                                <button class="btn btn-outline-secondary" type="button" 
                                                                        onclick="incrementQuantity({{ $producto->idProductos }})">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                            
                                                            <button class="btn btn-primary w-100 mb-2" 
                                                                    onclick="addToCart({{ $producto->idProductos }})">
                                                                <i class="fas fa-cart-plus"></i> Añadir al Carrito
                                                            </button>
                                                        @else
                                                            <button class="btn btn-secondary w-100" disabled>
                                                                <i class="fas fa-times-circle"></i> Producto Agotado
                                                            </button>
                                                        @endif
                                                        
                                                        <a href="{{ route('catalogo.show', $producto->idProductos) }}" 
                                                           class="btn btn-outline-info w-100 mt-auto">
                                                            <i class="fas fa-eye"></i> Ver Detalles
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12">
                                            <div class="alert alert-info text-center py-5">
                                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                                <h4>No se encontraron productos</h4>
                                                <p>No hay productos disponibles con los filtros seleccionados.</p>
                                                <a href="{{ route('catalogo') }}" class="btn btn-primary">
                                                    <i class="fas fa-undo"></i> Ver todos los productos
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Paginación -->
                                @if(isset($productos) && $productos->hasPages())
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <nav aria-label="Page navigation">
                                                <ul class="pagination justify-content-center">
                                                    <!-- Botón anterior -->
                                                    <li class="page-item {{ $productos->onFirstPage() ? 'disabled' : '' }}">
                                                        <a class="page-link" 
                                                           href="{{ $productos->previousPageUrl() }}{{ $search ? '&search=' . $search : '' }}{{ $categoria ? '&categoria=' . $categoria : '' }}">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </a>
                                                    </li>
                                                    
                                                    <!-- Números de página -->
                                                    @for ($i = 1; $i <= $productos->lastPage(); $i++)
                                                        <li class="page-item {{ $productos->currentPage() == $i ? 'active' : '' }}">
                                                            <a class="page-link" 
                                                               href="{{ $productos->url($i) }}{{ $search ? '&search=' . $search : '' }}{{ $categoria ? '&categoria=' . $categoria : '' }}">
                                                                {{ $i }}
                                                            </a>
                                                        </li>
                                                    @endfor
                                                    
                                                    <!-- Botón siguiente -->
                                                    <li class="page-item {{ !$productos->hasMorePages() ? 'disabled' : '' }}">
                                                        <a class="page-link" 
                                                           href="{{ $productos->nextPageUrl() }}{{ $search ? '&search=' . $search : '' }}{{ $categoria ? '&categoria=' . $categoria : '' }}">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                            
                                            <div class="text-center text-muted mt-2">
                                                Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} 
                                                de {{ $productos->total() }} productos
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <!-- FIN DEL CONTENIDO DEL CATÁLOGO -->
            </section>
        </main>
    </div>

    <!-- Modal de confirmación al añadir producto -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Producto añadido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="cartModalBody">
                    El producto se ha añadido correctamente al carrito.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir comprando</button>
                    <button type="button" class="btn btn-primary" onclick="openCartModal()">
                        <i class="fas fa-shopping-cart"></i> Ver Carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Configuración global
    const config = {
        csrfToken: '{{ csrf_token() }}',
        addToCartUrl: '/catalogo/add',
        getCartUrl: '/catalogo/cart/get',
        updateCartUrl: '/catalogo/cart/update',
        removeCartUrl: '/catalogo/cart/remove',
        clearCartUrl: '/catalogo/cart/clear'
    };

    // Cargar carrito al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Catálogo cargado - iniciando carrito');
        loadCartSidebar();
    });

    // Incrementar cantidad
    function incrementQuantity(productId) {
        const input = document.getElementById(`quantity-${productId}`);
        const max = parseInt(input.max);
        if (input.value < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    // Decrementar cantidad
    function decrementQuantity(productId) {
        const input = document.getElementById(`quantity-${productId}`);
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    // Añadir al carrito
    function addToCart(productId) {
        const quantity = document.getElementById(`quantity-${productId}`).value;
        console.log(`Añadiendo producto ${productId}, cantidad: ${quantity}`);
        
        fetch(config.addToCartUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                idProducto: productId,
                cantidad: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.success) {
                // Actualizar sidebar
                updateCartSidebarUI(data);
                
                // Mostrar modal de éxito
                showSuccessModal(data);
                
                // Actualizar botón del modal si está abierto
                if (document.getElementById('cartModalFull').classList.contains('show')) {
                    loadCartModal();
                }
            } else {
                alert(data.message || 'Error al añadir al carrito');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            alert('Error de conexión. Intenta nuevamente.');
        });
    }

    // Cargar sidebar del carrito
    function loadCartSidebar() {
        fetch(config.getCartUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Carrito cargado:', data);
            if (data.success) {
                updateCartSidebarUI(data);
            }
        })
        .catch(error => console.error('Error cargando carrito:', error));
    }

    // Cargar modal completo del carrito
    function loadCartModal() {
        const modalBody = document.getElementById('cartModalFullBody');
        if (!modalBody) {
            console.error('No se encontró el modal del carrito');
            return;
        }
        
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3">Cargando tu carrito...</p>
            </div>
        `;
        
        fetch(config.getCartUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCartModal(data);
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error al cargar el carrito
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Error de conexión
                </div>
            `;
        });
    }

    // Renderizar contenido del modal del carrito
    function renderCartModal(data) {
        const modalBody = document.getElementById('cartModalFullBody');
        const checkoutBtn = document.getElementById('checkoutBtn');
        
        if (!modalBody) return;
        
        if (data.carrito && Object.keys(data.carrito).length > 0) {
            let html = `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Subtotal</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            let total = 0;
            let itemCount = 0;
            
            Object.values(data.carrito).forEach(item => {
                if (item.id) {
                    const subtotal = item.precio * item.cantidad;
                    total += subtotal;
                    itemCount += item.cantidad;
                    
                    html += `
                        <tr id="modal-item-${item.id}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-box text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>${item.nombre}</strong><br>
                                        <small class="text-muted">${item.categoria || 'General'}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="text-danger">$${parseFloat(item.precio).toFixed(2)}</span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="input-group input-group-sm mx-auto" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartItem(${item.id}, -1, 'modal')">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           class="form-control text-center" 
                                           id="modal-quantity-${item.id}"
                                           value="${item.cantidad}" 
                                           min="1" 
                                           max="${item.stock || 99}"
                                           onchange="updateCartItemInput(${item.id}, this.value)">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartItem(${item.id}, 1, 'modal')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <strong>$${parseFloat(subtotal).toFixed(2)}</strong>
                            </td>
                            <td class="text-center align-middle">
                                <button class="btn btn-outline-danger btn-sm" onclick="removeCartItem(${item.id}, 'modal')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Resumen del pedido</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td>Productos:</td>
                                        <td class="text-end">${itemCount} items</td>
                                    </tr>
                                    <tr>
                                        <td>Subtotal:</td>
                                        <td class="text-end">$${total.toFixed(2)}</td>
                                    </tr>
                                    <tr>
                                        <td>Envío:</td>
                                        <td class="text-end text-success">Gratis</td>
                                    </tr>
                                    <tr class="table-active">
                                        <th>Total:</th>
                                        <th class="text-end text-success">$${total.toFixed(2)}</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-danger" onclick="clearCartModal()">
                                        <i class="fas fa-trash-alt"></i> Vaciar Carrito
                                    </button>
                                    <a href="{{ route('catalogo') }}" class="btn btn-outline-primary" data-bs-dismiss="modal">
                                        <i class="fas fa-store"></i> Seguir Comprando
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            modalBody.innerHTML = html;
            if (checkoutBtn) checkoutBtn.disabled = false;
        } else {
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Tu carrito está vacío</h4>
                    <p class="text-muted mb-4">No has agregado ningún producto al carrito.</p>
                    <button class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="fas fa-store"></i> Explorar Catálogo
                    </button>
                </div>
            `;
            if (checkoutBtn) checkoutBtn.disabled = true;
        }
    }

    // Actualizar sidebar del carrito
    function updateCartSidebarUI(data) {
        const cartSidebar = document.getElementById('cart-sidebar');
        if (!cartSidebar) return;
        
        if (data.carrito && Object.keys(data.carrito).length > 0) {
            let cartHtml = '<ul class="list-group list-group-flush">';
            let total = 0;
            let itemCount = 0;
            
            Object.values(data.carrito).forEach(item => {
                if (item.id) {
                    cartHtml += `
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="d-block">${item.nombre}</small>
                                    <small class="text-muted">${item.cantidad} x $${parseFloat(item.precio).toFixed(2)}</small>
                                </div>
                                <span class="badge bg-primary">$${parseFloat(item.subtotal).toFixed(2)}</span>
                            </div>
                        </li>
                    `;
                    total += parseFloat(item.subtotal);
                    itemCount += parseInt(item.cantidad);
                }
            });

            // CORRECCIÓN: CIERRA CORRECTAMENTE EL HTML
            cartHtml += `
                <li class="list-group-item bg-light">
                    <div class="d-flex justify-content-between">
                        <strong>Total (${itemCount} items):</strong>
                        <strong class="text-success">$${total.toFixed(2)}</strong>
                    </div>
                </li>
                <li class="list-group-item">
                    <button class="btn btn-success w-100" onclick="openCartModal()">
                        <i class="fas fa-shopping-cart"></i> Ver Carrito Completo
                    </button>
                </li>
            </ul>`;
            
            cartSidebar.innerHTML = cartHtml;
        } else {
            cartSidebar.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tu carrito está vacío</p>
                    <p class="text-muted small">Añade productos desde el catálogo</p>
                </div>
            `;
        }
    }

    // Mostrar modal de éxito
    function showSuccessModal(data) {
        const modalElement = document.getElementById('cartModal');
        if (!modalElement) return;
        
        const modalBody = document.getElementById('cartModalBody');
        if (modalBody) {
            modalBody.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h5>¡Producto añadido!</h5>
                    <p>${data.message}</p>
                    <p class="mb-0"><strong>${data.producto.nombre}</strong> x${data.producto.cantidad}</p>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm" onclick="openCartModal()">
                            <i class="fas fa-shopping-cart"></i> Ver Carrito
                        </button>
                    </div>
                </div>
            `;
        }
        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }

    // Abrir modal del carrito
    function openCartModal() {
        console.log('Abriendo modal del carrito');
        
        // Cerrar modal de éxito si está abierto
        const successModal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
        if (successModal) successModal.hide();
        
        // Cargar y mostrar modal del carrito
        loadCartModal();
        const modalElement = document.getElementById('cartModalFull');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            console.error('No se encontró el modal del carrito con id cartModalFull');
            alert('Error: No se puede abrir el carrito. Recarga la página.');
        }
    }

    // Funciones para manejar el carrito en el modal
    function updateCartItem(productId, change, type = 'modal') {
        const input = document.getElementById(`${type}-quantity-${productId}`);
        if (!input) return;
        
        let newQuantity = parseInt(input.value) + change;
        const max = parseInt(input.max) || 99;
        const min = 1;
        
        if (newQuantity < min) newQuantity = min;
        if (newQuantity > max) newQuantity = max;
        
        input.value = newQuantity;
        sendCartUpdate(productId, newQuantity);
    }

    function updateCartItemInput(productId, value) {
        let newQuantity = parseInt(value);
        const min = 1;
        
        if (isNaN(newQuantity) || newQuantity < min) {
            newQuantity = min;
            const input = document.getElementById(`modal-quantity-${productId}`);
            if (input) input.value = newQuantity;
        }
        
        sendCartUpdate(productId, newQuantity);
    }

    function sendCartUpdate(productId, quantity) {
        fetch(config.updateCartUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                idProducto: productId,
                cantidad: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCartModal(); // Recargar modal
                loadCartSidebar(); // Actualizar sidebar
            } else {
                alert(data.message || 'Error al actualizar');
                loadCartModal(); // Recargar para sincronizar
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    }

    function removeCartItem(productId, type = 'modal') {
        if (!confirm('¿Eliminar este producto del carrito?')) return;
        
        fetch(config.removeCartUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                idProducto: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCartModal();
                loadCartSidebar();
            } else {
                alert(data.message || 'Error al eliminar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    }
            function closeCartModal() {
            console.log('Cerrando modal del carrito...');
            const modalElement = document.getElementById('cartModalFull');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // La redirección la hará el navegador automáticamente
                                        }


        function goToCheckout() {
            // Cerrar modal
            const modalElement = document.getElementById('cartModalFull');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // Redirigir a checkout
            window.location.href = '{{ route("pedidos.checkout") }}';
        }

    function clearCartModal() {
        if (!confirm('¿Vaciar todo el carrito? Esta acción no se puede deshacer.')) return;
        
        fetch(config.clearCartUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCartModal();
                loadCartSidebar();
            } else {
                alert(data.message || 'Error al vaciar carrito');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    }

    function proceedToCheckout() {
    console.log('=== CHECKOUT: Iniciando ===');
    console.log('Usuario logueado?', {{ session()->has('user_id') ? 'true' : 'false' }});
    
    // Verificar si el usuario está logueado
    @if(session()->has('user_id'))
        console.log('CHECKOUT: Usuario logueado, redirigiendo...');
        
        // Cerrar modal del carrito
        try {
            const cartModal = document.getElementById('cartModalFull');
            if (cartModal) {
                const modal = bootstrap.Modal.getInstance(cartModal);
                if (modal) {
                    modal.hide();
                    console.log('CHECKOUT: Modal cerrado');
                }
            }
        } catch (e) {
            console.log('CHECKOUT: Error cerrando modal:', e);
        }
        
        // Redirigir directamente a checkout
        console.log('CHECKOUT: Redirigiendo a /checkout');
        window.location.href = '/checkout';
        
    @else
        console.log('CHECKOUT: Usuario NO logueado');
        if (confirm('Para realizar un pedido necesitas iniciar sesión. ¿Deseas ir a la página de login?')) {
            window.location.href = '{{ route("login") }}';
        }
    @endif
    
    return false; // Prevenir comportamiento por defecto
}
    </script>

    <!-- Modal del Carrito -->
    <div class="modal fade" id="cartModalFull" tabindex="-1" aria-labelledby="cartModalFullLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="cartModalFullLabel">
                        <i class="fas fa-shopping-cart"></i> Mi Carrito de Compras
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="cartModalFullBody">
                    <!-- Contenido dinámico se cargará aquí -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3">Cargando tu carrito...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                    <button class="btn btn-success" id="checkoutBtn" onclick="goToCheckout()">
                        <i class="fas fa-credit-card"></i> Realizar pedido
                        </button>
                </div>
            </div>
        </div>
    </div>
    <script>
// Depuración temporal
console.log('Ruta pedidos.checkout:', '{{ route("pedidos.checkout") }}');
console.log('Usuario logueado:', {{ session()->has('user_id') ? 'true' : 'false' }});

// Verifica si el botón existe
document.addEventListener('DOMContentLoaded', function() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        console.log('Botón checkout encontrado');
        checkoutBtn.addEventListener('click', function(e) {
            console.log('Click en botón checkout');
            console.log('Href:', this.getAttribute('href'));
        });
    }
});
</script>

</body>
</html>