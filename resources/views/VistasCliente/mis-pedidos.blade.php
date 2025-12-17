<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Remates El Paísa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .status-badge {
            font-size: 0.8em;
            padding: 5px 10px;
        }
        .order-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 1.5rem;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .order-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 10px 10px 0 0;
        }
        .product-img {
            width: 50px;
            height: 50px;
            background-color: #e9ecef;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
 @php
    use Illuminate\Support\Facades\DB;
    @endphp
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

    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-clipboard-list"></i> Mis Pedidos</h1>
                    <p class="lead mb-0">Revisa el historial y estado de tus compras</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="card bg-white text-dark">
                        <div class="card-body py-2">
                            <small class="text-muted">Cliente:</small>
                            <h6 class="mb-0">{{ $usuario->nombre }}</h6>
                            <small class="text-muted">{{ $usuario->email }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($pedidos->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-history"></i> Historial de Pedidos
                                <span class="badge bg-secondary">{{ $pedidos->total() }} pedidos</span>
                            </h5>
                            
                            @foreach($pedidos as $pedido)
                                <div class="card order-card">
                                    <div class="card-header order-header">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-hashtag text-primary"></i> Pedido #{{ $pedido->idPedidos }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="far fa-calendar"></i> {{ $pedido->fechaPedido }} 
                                                    <i class="far fa-clock ms-2"></i> {{ $pedido->horaPedido }}
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @php
                                                    $estadoColor = match($pedido->estadoPedido) {
                                                        'Completado' => 'success',
                                                        'En proceso' => 'info',
                                                        'Pendiente' => 'warning',
                                                        'Cancelado' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $estadoColor }} status-badge">
                                                    <i class="fas fa-circle"></i> {{ $pedido->estadoPedido }}
                                                </span>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <strong class="text-success">${{ number_format($pedido->totalPedido, 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Información del pedido -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <small class="text-muted">Subtotal:</small>
                                                <div>${{ number_format($pedido->valorPedido, 2) }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">IVA (19%):</small>
                                                <div>${{ number_format($pedido->ivaPedido, 2) }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Total:</small>
                                                <div class="fw-bold text-success">${{ number_format($pedido->totalPedido, 2) }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Repartidor:</small>
                                                <div>
                                                    @if($pedido->repartidorPedido)
                                                        <span class="badge bg-info">Asignado</span>
                                                    @else
                                                        <span class="badge bg-secondary">Por asignar</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Productos del pedido -->
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#productos-{{ $pedido->idPedidos }}" aria-expanded="false">
                                            <i class="fas fa-boxes"></i> Ver productos 
                                            @php
                                                // Contar detalles del pedido
                                                $detalleCount = DB::table('detalleproductos')
                                                    ->where('idPedido', $pedido->idPedidos)
                                                    ->count();
                                            @endphp
                                            ({{ $detalleCount }})
                                        </button>
                                        
                                        <div class="collapse mt-2" id="productos-{{ $pedido->idPedidos }}">
                                            <div class="card card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Producto</th>
                                                                <th class="text-center">Cantidad</th>
                                                                <th class="text-center">Precio Unitario</th>
                                                                <th class="text-center">Subtotal</th>
                                                                <th class="text-center">IVA</th>
                                                                <th class="text-center">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                // Obtener detalles del pedido
                                                                $detalles = DB::table('detalleproductos')
                                                                    ->where('idPedido', $pedido->idPedidos)
                                                                    ->get();
                                                                
                                                                // Obtener nombres de productos
                                                                $productosIds = $detalles->pluck('idProductos')->toArray();
                                                                $productos = DB::table('productos')
                                                                    ->whereIn('idProductos', $productosIds)
                                                                    ->pluck('nombreProducto', 'idProductos');
                                                            @endphp
                                                            
                                                            @if($detalles->count() > 0)
                                                                @foreach($detalles as $detalle)
                                                                <tr>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="product-img me-2">
                                                                                <i class="fas fa-box text-primary"></i>
                                                                            </div>
                                                                            <div>
                                                                                <small class="d-block">
                                                                                    {{ $productos[$detalle->idProductos] ?? 'Producto #' . $detalle->idProductos }}
                                                                                </small>
                                                                                <small class="text-muted">
                                                                                    ID: {{ $detalle->idProductos }}
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        <span class="badge bg-primary">{{ $detalle->cantidadDetalleProducto }}</span>
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        ${{ number_format($detalle->valorUnitarioDetalleProducto, 2) }}
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        ${{ number_format($detalle->totalPagarDetalleProducto, 2) }}
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        ${{ number_format($detalle->ivaDetalleProducto, 2) }}
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        <strong>${{ number_format($detalle->totalDetalleProducto, 2) }}</strong>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                                <tr class="table-active">
                                                                    <td colspan="3" class="text-end"><strong>Totales del pedido:</strong></td>
                                                                    <td class="text-center">
                                                                        <strong>${{ number_format($pedido->valorPedido, 2) }}</strong>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <strong>${{ number_format($pedido->ivaPedido, 2) }}</strong>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <strong class="text-success">${{ number_format($pedido->totalPedido, 2) }}</strong>
                                                                    </td>
                                                                </tr>
                                                            @else
                                                                <tr>
                                                                    <td colspan="6" class="text-center text-muted">
                                                                        <i class="fas fa-info-circle"></i> No hay detalles disponibles para este pedido
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-muted">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <small>
                                                    <i class="fas fa-info-circle"></i> 
                                                    @if($pedido->estadoPedido == 'Pendiente')
                                                        Tu pedido está en espera de ser procesado.
                                                    @elseif($pedido->estadoPedido == 'En proceso')
                                                        Tu pedido está siendo preparado.
                                                    @elseif($pedido->estadoPedido == 'Completado')
                                                        Pedido entregado exitosamente.
                                                    @else
                                                        Estado: {{ $pedido->estadoPedido }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                @if($pedido->estadoPedido == 'Pendiente')
                                                    <button class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-times"></i> Cancelar
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-outline-info" onclick="printPedido({{ $pedido->idPedidos }})">
                                                    <i class="fas fa-print"></i> Imprimir
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Paginación -->
                            @if($pedidos->hasPages())
                                <div class="mt-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            {{ $pedidos->links() }}
                                        </ul>
                                    </nav>
                                    <div class="text-center text-muted mt-2">
                                        Mostrando {{ $pedidos->firstItem() }} a {{ $pedidos->lastItem() }} de {{ $pedidos->total() }} pedidos
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h3 class="text-muted">No tienes pedidos aún</h3>
                        <p class="text-muted mb-4">Comienza a explorar nuestro catálogo y realiza tu primera compra.</p>
                        <a href="{{ route('catalogo') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-store"></i> Ir al Catálogo
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Estadísticas -->
        @if($pedidos->count() > 0)
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h6 class="card-title">Total Pedidos</h6>
                            <h2 class="mb-0">{{ $pedidos->total() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    @php
                        $completados = $pedidos->where('estadoPedido', 'Completado')->count();
                    @endphp
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h6 class="card-title">Completados</h6>
                            <h2 class="mb-0">{{ $completados }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    @php
                        $pendientes = $pedidos->where('estadoPedido', 'Pendiente')->count();
                    @endphp
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h6 class="card-title">Pendientes</h6>
                            <h2 class="mb-0">{{ $pendientes }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    @php
                        $totalGastado = $pedidos->sum('totalPedido');
                    @endphp
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h6 class="card-title">Total Gastado</h6>
                            <h4 class="mb-0">${{ number_format($totalGastado, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-store"></i> Remates El Paísa</h5>
                    <p class="mb-0">Los mejores productos al mejor precio</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('catalogo') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Catálogo
                    </a>
                    <a href="{{ route('usuario.ayuda.contacto') }}" class="btn btn-outline-light btn-sm ms-2">
                        <i class="fas fa-question-circle"></i> Ayuda
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printPedido(idPedido) {
            window.open('/pedido/' + idPedido + '/comprobante', '_blank');
        }
        
        // Auto-cerrar alertas después de 5 segundos
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>