@extends('VistasAdmin.welcome')
@section('title', 'Productos del Pedido')
@section('content')
<div class="container-sm d-flex justify-content-center mt-5">
    <div class="card">
        <div class="card-body" style="width: 1200px;">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Productos del Pedido #{{ $pedido->idPedidos }}</h3>
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Pedidos
                </a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Agregar Producto</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pedidos.productos.store', $pedido->idPedidos) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="idProducto" class="form-label">Seleccionar Producto *</label>
                                    <select class="form-select" id="idProducto" name="idProducto" required>
                                        <option value="">Seleccionar producto...</option>
                                        @foreach($productosDisponibles as $producto)
                                            <option value="{{ $producto->idProductos }}" 
                                                    data-precio="{{ $producto->precioUnitario }}"
                                                    data-nombre="{{ $producto->nombreProducto }}">
                                                {{ $producto->nombreProducto }} - ${{ number_format($producto->precioUnitario, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio Unitario *</label>
                                    <input type="number" step="0.01" class="form-control" id="precio" readonly required min="0">
                                </div>
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label">Cantidad *</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" required min="1" value="1">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-plus"></i> Agregar Producto
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Resumen del Pedido</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>ID Pedido:</strong> {{ $pedido->idPedidos }}</p>
                            <p><strong>Cliente:</strong> {{ $pedido->idCliente }}</p>
                            <p><strong>Fecha:</strong> {{ $pedido->fechaPedido }}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge 
                                    @if($pedido->estadoPedido == 'Pendiente') bg-warning
                                    @elseif($pedido->estadoPedido == 'Enviado') bg-info
                                    @elseif($pedido->estadoPedido == 'Entregado') bg-success
                                    @elseif($pedido->estadoPedido == 'Cancelado') bg-danger
                                    @else bg-secondary @endif">
                                    {{ $pedido->estadoPedido }}
                                </span>
                            </p>
                            <hr>
                            <p><strong>Subtotal:</strong> ${{ number_format($pedido->valorPedido, 2) }}</p>
                            <p><strong>IVA (19%):</strong> ${{ number_format($pedido->ivaPedido, 2) }}</p>
                            <p><strong>Total:</strong> ${{ number_format($pedido->totalPedido, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Productos en el Pedido</h5>
                </div>
                <div class="card-body">
                    @if($productos->count() > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Producto</th>
                                <th>Nombre</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                            <tr>
                                <td>{{ $producto->idProductos }}</td>
                                <td>{{ $producto->producto->nombreProducto ?? 'Producto no encontrado' }}</td>
                                <td>${{ number_format($producto->valorUnitarioDetalleProducto, 2) }}</td>
                                <td>{{ $producto->cantidadDetalleProducto }}</td>
                                <td>${{ number_format($producto->totalPagarDetalleProducto, 2) }}</td>
                                <td>${{ number_format($producto->ivaDetalleProducto, 2) }}</td>
                                <td>${{ number_format($producto->totalDetalleProducto, 2) }}</td>
                                <td>
                                    <form action="{{ route('pedidos.productos.destroy', ['idPedido' => $pedido->idPedidos, 'idProducto' => $producto->idProductos]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este producto del pedido?')">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay productos en este pedido.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productoSelect = document.getElementById('idProducto');
    const precioInput = document.getElementById('precio');
    
    productoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value !== '') {
            precioInput.value = selectedOption.getAttribute('data-precio');
        } else {
            precioInput.value = '';
        }
    });
});
</script>
@endsection