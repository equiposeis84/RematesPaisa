@extends('welcome')
@section('title', 'Pedidos')
@section('content')
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Modulo Pedidos</h3>
                
                <!-- Mostrar mensajes de éxito -->
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
                
                <hr>

                <!-- Formulario de búsqueda -->
                <form name="pedidos" action="{{ url('/pedidos') }}" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botón para abrir modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPedido">
                            <i class="fa-solid fa-plus"></i> Nuevo
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por ID, cliente, estado o repartidor" 
                                       name="search"
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="{{ url('/pedidos') }}" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla pedidos -->
                @if($datos->count() > 0)
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Pedido</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>ID Cliente</th>
                            <th>Valor</th>
                            <th>IVA</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Repartidor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datos as $item)
                            <tr>
                                <td>{{ $item->idPedidos }}</td> 
                                <td>{{ $item->fechaPedido }}</td>  
                                <td>{{ $item->horaPedido }}</td>
                                <td>{{ $item->idCliente }}</td>  
                                <td>${{ number_format($item->valorPedido, 2) }}</td>
                                <td>${{ number_format($item->ivaPedido, 2) }}</td>
                                <td>${{ number_format($item->totalPedido, 2) }}</td>
                                <td>
                                    <span class="badge 
                                        @if($item->estadoPedido == 'Pendiente') bg-warning
                                        @elseif($item->estadoPedido == 'Enviado') bg-info
                                        @elseif($item->estadoPedido == 'Entregado') bg-success
                                        @elseif($item->estadoPedido == 'Cancelado') bg-danger
                                        @else bg-secondary @endif">
                                        {{ $item->estadoPedido }}
                                    </span>
                                </td>
                                <td>{{ $item->repartidorPedido ?? 'N/A' }}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarPedido"
                                            data-id="{{ $item->idPedidos }}"
                                            data-fecha="{{ $item->fechaPedido }}"
                                            data-hora="{{ $item->horaPedido }}"
                                            data-cliente="{{ $item->idCliente }}"
                                            data-valor="{{ $item->valorPedido }}"
                                            data-iva="{{ $item->ivaPedido }}"
                                            data-total="{{ $item->totalPedido }}"
                                            data-estado="{{ $item->estadoPedido }}"
                                            data-repartidor="{{ $item->repartidorPedido }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <!-- Botón anterior -->
                        <li class="page-item {{ $datos->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $datos->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                Atrás
                            </a>
                        </li>

                        <!-- Números de página -->
                        @for ($i = 1; $i <= $datos->lastPage(); $i++)
                            <li class="page-item {{ $datos->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" 
                                   href="{{ $datos->url($i) }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                            
                        <!-- Botón Siguiente -->
                        <li class="page-item {{ !$datos->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $datos->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Información de registros -->
                <div class="text-muted mt-2">
                    Mostrando {{ $datos->firstItem() }} a {{ $datos->lastItem() }} de {{ $datos->total() }} registros
                </div>

                @else
                <div class="alert alert-info text-center mt-3">
                    <i class="fas fa-info-circle"></i> 
                    @if(request('search'))
                        No se encontraron pedidos con "{{ request('search') }}"
                    @else
                        No hay pedidos registrados.
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div> <!-- Fin del container -->

    <!-- Modal para Nuevo Pedido -->
    <div class="modal fade" id="modalPedido" tabindex="-1" aria-labelledby="modalPedidoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalPedidoLabel">Nuevo Pedido</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pedidos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- CAMPO PARA ID PEDIDO -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idPedidos" class="form-label">ID Pedido *</label>
                                    <input type="text" class="form-control" id="idPedidos" name="idPedidos" required 
                                           placeholder="Ej: 1001">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estadoPedido" class="form-label">Estado *</label>
                                    <select class="form-select" id="estadoPedido" name="estadoPedido" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Enviado">Enviado</option>
                                        <option value="Entregado">Entregado</option>
                                        <option value="Cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO ID PEDIDO -->
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fechaPedido" class="form-label">Fecha *</label>
                                    <input type="date" class="form-control" id="fechaPedido" name="fechaPedido" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="horaPedido" class="form-label">Hora *</label>
                                    <input type="time" class="form-control" id="horaPedido" name="horaPedido" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="idCliente" class="form-label">ID Cliente *</label>
                                    <input type="text" class="form-control" id="idCliente" name="idCliente" required 
                                           placeholder="Ej: CL001">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valorPedido" class="form-label">Valor *</label>
                                    <input type="number" step="0.01" class="form-control" id="valorPedido" name="valorPedido" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ivaPedido" class="form-label">IVA *</label>
                                    <input type="number" step="0.01" class="form-control" id="ivaPedido" name="ivaPedido" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="totalPedido" class="form-label">Total *</label>
                                    <input type="number" step="0.01" class="form-control" id="totalPedido" name="totalPedido" required min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="repartidorPedido" class="form-label">Repartidor</label>
                                    <input type="text" class="form-control" id="repartidorPedido" name="repartidorPedido" 
                                           placeholder="Nombre del repartidor">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Pedido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Pedido -->
    <div class="modal fade" id="modalEditarPedido" tabindex="-1" aria-labelledby="modalEditarPedidoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditarPedidoLabel">Editar Pedido</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarPedido" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- CAMPO PARA ID PEDIDO (solo lectura) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_idPedidos" class="form-label">ID Pedido *</label>
                                    <input type="text" class="form-control" id="edit_idPedidos" name="idPedidos" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_estadoPedido" class="form-label">Estado *</label>
                                    <select class="form-select" id="edit_estadoPedido" name="estadoPedido" required>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Enviado">Enviado</option>
                                        <option value="Entregado">Entregado</option>
                                        <option value="Cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO ID PEDIDO -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_fechaPedido" class="form-label">Fecha *</label>
                                    <input type="date" class="form-control" id="edit_fechaPedido" name="fechaPedido" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_horaPedido" class="form-label">Hora *</label>
                                    <input type="time" class="form-control" id="edit_horaPedido" name="horaPedido" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_idCliente" class="form-label">ID Cliente *</label>
                                    <input type="text" class="form-control" id="edit_idCliente" name="idCliente" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_valorPedido" class="form-label">Valor *</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_valorPedido" name="valorPedido" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_ivaPedido" class="form-label">IVA *</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ivaPedido" name="ivaPedido" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_totalPedido" class="form-label">Total *</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_totalPedido" name="totalPedido" required min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_repartidorPedido" class="form-label">Repartidor</label>
                                    <input type="text" class="form-control" id="edit_repartidorPedido" name="repartidorPedido">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Pedido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto cerrar alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Configurar modal de edición
            const modalEditar = document.getElementById('modalEditarPedido');
            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const fecha = button.getAttribute('data-fecha');
                    const hora = button.getAttribute('data-hora');
                    const cliente = button.getAttribute('data-cliente');
                    const valor = button.getAttribute('data-valor');
                    const iva = button.getAttribute('data-iva');
                    const total = button.getAttribute('data-total');
                    const estado = button.getAttribute('data-estado');
                    const repartidor = button.getAttribute('data-repartidor');

                    // Actualizar el formulario
                    document.getElementById('formEditarPedido').action = `/pedidos/${id}`;
                    document.getElementById('edit_idPedidos').value = id;
                    document.getElementById('edit_fechaPedido').value = fecha;
                    document.getElementById('edit_horaPedido').value = hora;
                    document.getElementById('edit_idCliente').value = cliente;
                    document.getElementById('edit_valorPedido').value = valor;
                    document.getElementById('edit_ivaPedido').value = iva;
                    document.getElementById('edit_totalPedido').value = total;
                    document.getElementById('edit_estadoPedido').value = estado;
                    document.getElementById('edit_repartidorPedido').value = repartidor || '';
                });
            }

            // Limpiar formulario de nuevo pedido cuando se cierra el modal
            const modalNuevo = document.getElementById('modalPedido');
            if (modalNuevo) {
                modalNuevo.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('idPedidos').value = '';
                    document.getElementById('estadoPedido').value = '';
                    document.getElementById('fechaPedido').value = '';
                    document.getElementById('horaPedido').value = '';
                    document.getElementById('idCliente').value = '';
                    document.getElementById('valorPedido').value = '';
                    document.getElementById('ivaPedido').value = '';
                    document.getElementById('totalPedido').value = '';
                    document.getElementById('repartidorPedido').value = '';
                });
            }
        });
    </script>
@endsection