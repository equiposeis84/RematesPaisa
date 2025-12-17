<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Remates El Paísa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .checkout-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .checkout-card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .checkout-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="{{ route('catalogo') }}">
                <i class="fas fa-store"></i> Remates El Paísa
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('catalogo') }}">
                    <i class="fas fa-arrow-left"></i> Volver al Catálogo
                </a>
            </div>
        </div>
    </nav>

    <div class="checkout-container">
        <div class="text-center mb-4">
            <h1><i class="fas fa-shopping-bag text-success"></i> Finalizar Compra</h1>
            <p class="text-muted">Revise su pedido antes de confirmar</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Columna izquierda: Productos y información -->
            <div class="col-lg-8">
                <!-- Resumen del Carrito -->
                <div class="checkout-card">
                    <div class="checkout-header">
                        <h4 class="mb-0"><i class="fas fa-shopping-cart"></i> Resumen del Carrito</h4>
                    </div>
                    <div class="card-body">
                        @if(!empty($carrito))
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Precio Unitario</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($carrito as $id => $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-box text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $item['nombre'] }}</strong><br>
                                                        <small class="text-muted">{{ $item['categoria'] ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                ${{ number_format($item['precio'], 2) }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-primary" style="font-size: 1em; padding: 8px 12px;">
                                                    {{ $item['cantidad'] }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <strong>${{ number_format($item['precio'] * $item['cantidad'], 2) }}</strong>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Tu carrito está vacío</h4>
                                <p class="text-muted mb-4">No hay productos para procesar.</p>
                                <a href="{{ route('catalogo') }}" class="btn btn-primary">
                                    <i class="fas fa-store"></i> Ir al Catálogo
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Información del Cliente -->
                <div class="checkout-card">
                    <div class="checkout-header">
                        <h4 class="mb-0"><i class="fas fa-user-circle"></i> Información del Cliente</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong> {{ $usuario->nombre }}</p>
                                <p><strong>Email:</strong> {{ $usuario->email }}</p>
                                <p><strong>Tipo de usuario:</strong> 
                                    <span class="badge bg-info">
                                        @if($usuario->idRol == 2)
                                            Cliente
                                        @elseif($usuario->idRol == 3)
                                            Repartidor
                                        @endif
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Documento:</strong> {{ $usuario->documento }}</p>
                                <p><strong>Teléfono:</strong> {{ $usuario->telefono ?? 'No registrado' }}</p>
                                <p><strong>Dirección:</strong> {{ $usuario->direccion ?? 'No registrada' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Total y confirmación -->
            <div class="col-lg-4">
                <div class="checkout-card sticky-top" style="top: 20px;">
                    <div class="checkout-header">
                        <h4 class="mb-0"><i class="fas fa-receipt"></i> Resumen del Pedido</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-end">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td>IVA (19%):</td>
                                <td class="text-end">${{ number_format($iva, 2) }}</td>
                            </tr>
                            <tr class="table-active">
                                <th class="checkout-total">Total a pagar:</th>
                                <th class="text-end checkout-total text-success">${{ number_format($total, 2) }}</th>
                            </tr>
                        </table>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <small>
                                El pedido será procesado inmediatamente. 
                                Recibirás una confirmación por email.
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button id="btnRealizarPedido" class="btn btn-success btn-lg py-3">
                                <i class="fas fa-check-circle"></i> Confirmar y Realizar Pedido
                            </button>
                            
                            <a href="{{ route('catalogo') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Seguir Comprando
                            </a>
                            
                            <a href="#" onclick="history.back(); return false;" class="btn btn-outline-primary">
                                <i class="fas fa-edit"></i> Modificar Carrito
                            </a>
                        </div>

                        <div id="loading" class="mt-3 text-center" style="display: none;">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Procesando...</span>
                            </div>
                            <p class="mt-2">Procesando tu pedido, por favor espera...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnRealizarPedido = document.getElementById('btnRealizarPedido');
        const loading = document.getElementById('loading');
        
        if (btnRealizarPedido) {
            btnRealizarPedido.addEventListener('click', function() {
                console.log('=== INICIANDO CHECKOUT ===');
                
                // Mostrar loading
                btnRealizarPedido.disabled = true;
                loading.style.display = 'block';
                
                // Enviar solicitud AJAX
              fetch('{{ route("pedidos.procesar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    console.log('Respuesta status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        alert('¡Pedido creado exitosamente! Número de pedido: #' + data.pedido_id);
                        
                        // Redirigir a la página de mis pedidos
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '{{ route("usuario.mis-pedidos") }}';
                        }
                    } else {
                        // Mostrar error
                        alert('Error: ' + (data.error || 'No se pudo procesar el pedido'));
                        btnRealizarPedido.disabled = false;
                        loading.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    alert('Error de conexión. Por favor, intenta nuevamente.');
                    btnRealizarPedido.disabled = false;
                    loading.style.display = 'none';
                });
            });
        }
    });
    </script>
</body>
</html>