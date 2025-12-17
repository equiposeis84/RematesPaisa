@extends('layouts.usuario')

@section('title', 'Carrito de Compras - Remates El Paísa')

@section('content')
<header class="content-header">
    <h2><i class="fas fa-shopping-cart"></i> Carrito de Compras</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('catalogo') }}">Catálogo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Carrito</li>
        </ol>
    </nav>
</header>

<section class="content-body">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Lista de productos en el carrito -->
            <div class="col-lg-8 col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes"></i> Productos en tu carrito</h5>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($carrito) && count($carrito) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="60"></th>
                                            <th>Producto</th>
                                            <th class="text-center">Precio Unitario</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Subtotal</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total = 0;
                                            $totalItems = 0;
                                        @endphp
                                        
                                        @foreach($carrito as $id => $item)
                                            @php
                                                $subtotal = $item['precio'] * $item['cantidad'];
                                                $total += $subtotal;
                                                $totalItems += $item['cantidad'];
                                            @endphp
                                            <tr id="cart-item-{{ $id }}">
                                                <td>
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-box text-primary"></i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-1">{{ $item['nombre'] }}</h6>
                                                        <small class="text-muted">Categoría: {{ $item['categoria'] ?? 'General' }}</small><br>
                                                        <small class="text-muted">Stock disponible: {{ $item['stock'] ?? 0 }}</small>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="fw-bold text-danger">${{ number_format($item['precio'], 2) }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="input-group input-group-sm mx-auto" style="width: 120px;">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity({{ $id }}, -1)">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" 
                                                               class="form-control text-center" 
                                                               id="cart-quantity-{{ $id }}"
                                                               value="{{ $item['cantidad'] }}" 
                                                               min="1" 
                                                               max="{{ $item['stock'] ?? 99 }}"
                                                               onchange="updateQuantityInput({{ $id }})">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity({{ $id }}, 1)">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="fw-bold">${{ number_format($subtotal, 2) }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart({{ $id }})" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted">Tu carrito está vacío</h4>
                                <p class="text-muted mb-4">No has agregado ningún producto al carrito.</p>
                                <a href="{{ route('catalogo') }}" class="btn btn-primary">
                                    <i class="fas fa-store"></i> Ir al catálogo
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Acciones del carrito -->
                @if(isset($carrito) && count($carrito) > 0)
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('catalogo') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Seguir comprando
                        </a>
                        <button class="btn btn-outline-danger" onclick="clearCart()">
                            <i class="fas fa-trash-alt"></i> Vaciar carrito
                        </button>
                    </div>
                @endif
            </div>
            
            <!-- Resumen del pedido -->
            <div class="col-lg-4 col-md-5">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt"></i> Resumen del pedido</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($carrito) && count($carrito) > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Productos ({{ $totalItems }}):</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Envío:</span>
                                    <span class="text-success">Gratis</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total a pagar:</strong>
                                    <strong class="text-success fs-5">${{ number_format($total, 2) }}</strong>
                                </div>
                            </div>
                            
                            <!-- Verificar que el usuario esté autenticado -->
                            @auth
                                <form action="{{ route('usuario.carrito.procesar') }}" method="POST" id="checkout-form">
                                    @csrf
                                    
                                    <!-- Mostrar info del usuario actual -->
                                    <div class="alert alert-info mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle fa-2x me-3"></i>
                                            <div>
                                                <strong>{{ auth()->user()->nombre ?? auth()->user()->name }}</strong><br>
                                                <small>{{ auth()->user()->email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="direccion" class="form-label">Dirección de entrega</label>
                                        <textarea class="form-control" id="direccion" name="direccion" rows="2" 
                                                  placeholder="Ingresa tu dirección completa" required>{{ auth()->user()->direccion ?? old('direccion', '') }}</textarea>
                                        <div class="form-text">Asegúrate de que la dirección sea correcta</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono de contacto</label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                                               value="{{ auth()->user()->telefono ?? old('telefono', '') }}" 
                                               placeholder="Ej: 3001234567" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="observaciones" class="form-label">Observaciones (opcional)</label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" 
                                                  placeholder="Instrucciones especiales para la entrega">{{ old('observaciones') }}</textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Método de pago</label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="metodo_pago" id="efectivo" value="efectivo" checked>
                                            <label class="form-check-label" for="efectivo">
                                                <i class="fas fa-money-bill-wave text-success"></i> Efectivo contra entrega
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metodo_pago" id="transferencia" value="transferencia">
                                            <label class="form-check-label" for="transferencia">
                                                <i class="fas fa-university text-primary"></i> Transferencia bancaria
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check-circle"></i> Confirmar pedido
                                        </button>
                                        <a href="#" class="btn btn-outline-secondary" onclick="alert('Función en desarrollo')">
                                            <i class="fas fa-calendar-alt"></i> Programar entrega
                                        </a>
                                    </div>
                                </form>
                            @else
                                <!-- Si por alguna razón no está autenticado (aunque debería estarlo) -->
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>¡Necesitas iniciar sesión!</strong>
                                    <p class="mb-0 mt-2">Para proceder con la compra, debes iniciar sesión primero.</p>
                                    <div class="d-grid gap-2 mt-3">
                                        <a href="{{ route('login') }}" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                                        </a>
                                    </div>
                                </div>
                            @endauth
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Agrega productos al carrito para ver el resumen</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Información de ayuda -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-question-circle"></i> Información importante</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-shipping-fast text-info me-2"></i>
                                <small>Envíos en 24-48 horas</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-shield-alt text-info me-2"></i>
                                <small>Pago seguro</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-undo text-info me-2"></i>
                                <small>Devoluciones en 7 días</small>
                            </li>
                            <li>
                                <i class="fas fa-headset text-info me-2"></i>
                                <small>Soporte: 01-800-123-4567</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                ¿Estás seguro de que deseas realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmModalButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de éxito -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle"></i> ¡Éxito!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="successModalBody">
                Operación realizada correctamente.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Configuración global
    const config = {
        csrfToken: '{{ csrf_token() }}',
        updateCartUrl: '{{ route("catalogo.cart.update") }}',
        removeCartUrl: '{{ route("catalogo.cart.remove", ":id") }}',
        clearCartUrl: '{{ route("catalogo.cart.clear") }}'
    };

    // Actualizar cantidad con botones +/- 
    function updateQuantity(productId, change) {
        const input = document.getElementById(`cart-quantity-${productId}`);
        if (!input) return;
        
        let newQuantity = parseInt(input.value) + change;
        const max = parseInt(input.max) || 99;
        const min = 1;
        
        if (newQuantity < min) newQuantity = min;
        if (newQuantity > max) newQuantity = max;
        
        input.value = newQuantity;
        updateCartItem(productId, newQuantity);
    }

    // Actualizar cantidad cuando se cambia manualmente el input
    function updateQuantityInput(productId) {
        const input = document.getElementById(`cart-quantity-${productId}`);
        if (!input) return;
        
        let newQuantity = parseInt(input.value);
        const max = parseInt(input.max) || 99;
        const min = 1;
        
        if (newQuantity < min) newQuantity = min;
        if (newQuantity > max) newQuantity = max;
        
        input.value = newQuantity;
        updateCartItem(productId, newQuantity);
    }

    // Actualizar item en el carrito vía AJAX
    function updateCartItem(productId, quantity) {
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
                // Recargar la página para actualizar totales
                location.reload();
            } else {
                alert(data.message || 'Error al actualizar la cantidad');
                location.reload(); // Recargar para sincronizar
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al actualizar');
        });
    }

    // Eliminar producto del carrito
    function removeFromCart(productId) {
        if (!confirm('¿Estás seguro de que deseas eliminar este producto del carrito?')) {
            return;
        }
        
        const url = config.removeCartUrl.replace(':id', productId);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Eliminar fila de la tabla
                const row = document.getElementById(`cart-item-${productId}`);
                if (row) {
                    row.remove();
                }
                
                // Recargar página si no quedan productos
                if (document.querySelectorAll('[id^="cart-item-"]').length === 0) {
                    location.reload();
                } else {
                    location.reload(); // Recargar para actualizar totales
                }
            } else {
                alert(data.message || 'Error al eliminar producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al eliminar');
        });
    }

    // Vaciar carrito completo
    function clearCart() {
        if (!confirm('¿Estás seguro de que deseas vaciar todo el carrito? Esta acción no se puede deshacer.')) {
            return;
        }
        
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
                alert('Carrito vaciado correctamente');
                location.reload();
            } else {
                alert(data.message || 'Error al vaciar carrito');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al vaciar carrito');
        });
    }

    // Validar formulario de checkout
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutForm = document.getElementById('checkout-form');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validar dirección
                const direccion = document.getElementById('direccion').value.trim();
                if (!direccion) {
                    alert('Por favor, ingresa una dirección de entrega');
                    return;
                }
                
                // Validar teléfono
                const telefono = document.getElementById('telefono').value.trim();
                if (!telefono) {
                    alert('Por favor, ingresa un número de teléfono de contacto');
                    return;
                }
                
                // Confirmar pedido
                if (confirm('¿Confirmas que deseas realizar este pedido?')) {
                    this.submit();
                }
            });
        }
        
        // Verificar que todas las cantidades sean válidas
        document.querySelectorAll('[id^="cart-quantity-"]').forEach(input => {
            input.addEventListener('blur', function() {
                const productId = this.id.replace('cart-quantity-', '');
                const value = parseInt(this.value);
                
                if (isNaN(value) || value < 1) {
                    this.value = 1;
                    updateCartItem(productId, 1);
                }
            });
        });
    });
</script>
@endsection