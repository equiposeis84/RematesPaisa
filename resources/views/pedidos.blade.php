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
                                    <!-- Botón para gestionar productos del pedido -->
                                    <a href="{{ route('pedidos.productos.index', $item->idPedidos) }}" 
                                       class="btn btn-info btn-sm mb-1">
                                        <i class="fa-solid fa-cart-plus"></i> Productos
                                    </a>
                                    
                                    <button type="button" class="btn btn-success btn-sm mb-1" 
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
                                    
                                    <button type="button" class="btn btn-danger btn-sm mb-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarPedido"
                                            data-id="{{ $item->idPedidos }}">
                                        <i class="fa-solid fa-trash"></i> Eliminar
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
                <form action="{{ route('pedidos.store') }}" method="POST" id="formNuevoPedido">
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

                        <!-- Información del Cliente -->
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Información del Cliente</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="idCliente" class="form-label">Seleccionar Cliente *</label>
                                            <select class="form-select" id="idCliente" name="idCliente" required>
                                                <option value="">Seleccionar cliente...</option>
                                                @foreach($clientes = \App\Models\Cliente::all() as $cliente)
                                                    <option value="{{ $cliente->idCliente }}" 
                                                            data-nombre="{{ $cliente->nombreCliente }}"
                                                            data-apellido="{{ $cliente->apellidoCliente }}"
                                                            data-empresa="{{ $cliente->NombreEmpresa }}"
                                                            data-email="{{ $cliente->emailCliente }}"
                                                            data-telefono="{{ $cliente->telefonoCliente }}"
                                                            data-direccion="{{ $cliente->direccionCliente }}">
                                                        {{ $cliente->idCliente }} - {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }} ({{ $cliente->NombreEmpresa }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="infoCliente" class="mt-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nombre:</strong> <span id="clienteNombre"></span></p>
                                            <p><strong>Empresa:</strong> <span id="clienteEmpresa"></span></p>
                                            <p><strong>Email:</strong> <span id="clienteEmail"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Teléfono:</strong> <span id="clienteTelefono"></span></p>
                                            <p><strong>Dirección:</strong> <span id="clienteDireccion"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valorPedido" class="form-label">Valor *</label>
                                    <input type="number" step="0.01" class="form-control" id="valorPedido" name="valorPedido" required min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ivaPedido" class="form-label">IVA (19%)</label>
                                    <input type="number" step="0.01" class="form-control" id="ivaPedido" name="ivaPedido" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="totalPedido" class="form-label">Total</label>
                                    <input type="number" step="0.01" class="form-control" id="totalPedido" name="totalPedido" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="repartidorPedido" class="form-label">Repartidor</label>
                                    <select class="form-select" id="repartidorPedido" name="repartidorPedido">
                                        <option value="">Seleccionar repartidor...</option>
                                        @foreach($repartidores = \App\Models\Usuario::where('idRol', 3)->get() as $repartidor)
                                            <option value="{{ $repartidor->nombre }}">
                                                {{ $repartidor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
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

    <!-- Modal para Eliminar Pedido -->
    <div class="modal fade" id="modalEliminarPedido" tabindex="-1" aria-labelledby="modalEliminarPedidoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEliminarPedidoLabel">Eliminar Pedido</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEliminarPedido" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este pedido?</p>
                        <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar Pedido</button>
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

                        <!-- Información del Cliente en Edición -->
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Información del Cliente</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="edit_idCliente" class="form-label">Seleccionar Cliente *</label>
                                            <select class="form-select" id="edit_idCliente" name="idCliente" required>
                                                @foreach($clientes = \App\Models\Cliente::all() as $cliente)
                                                    <option value="{{ $cliente->idCliente }}" 
                                                            data-nombre="{{ $cliente->nombreCliente }}"
                                                            data-apellido="{{ $cliente->apellidoCliente }}"
                                                            data-empresa="{{ $cliente->NombreEmpresa }}"
                                                            data-email="{{ $cliente->emailCliente }}"
                                                            data-telefono="{{ $cliente->telefonoCliente }}"
                                                            data-direccion="{{ $cliente->direccionCliente }}">
                                                        {{ $cliente->idCliente }} - {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }} ({{ $cliente->NombreEmpresa }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="editInfoCliente" class="mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nombre:</strong> <span id="editClienteNombre"></span></p>
                                            <p><strong>Empresa:</strong> <span id="editClienteEmpresa"></span></p>
                                            <p><strong>Email:</strong> <span id="editClienteEmail"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Teléfono:</strong> <span id="editClienteTelefono"></span></p>
                                            <p><strong>Dirección:</strong> <span id="editClienteDireccion"></span></p>
                                        </div>
                                    </div>
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
                                    <label for="edit_ivaPedido" class="form-label">IVA (19%)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ivaPedido" name="ivaPedido" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_totalPedido" class="form-label">Total</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_totalPedido" name="totalPedido" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_repartidorPedido" class="form-label">Repartidor</label>
                                    <select class="form-select" id="edit_repartidorPedido" name="repartidorPedido">
                                        <option value="">Seleccionar repartidor...</option>
                                        @foreach($repartidores = \App\Models\Usuario::where('idRol', 3)->get() as $repartidor)
                                            <option value="{{ $repartidor->nombre }}">
                                                {{ $repartidor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Auto cerrar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Función para calcular IVA y Total
            function calcularTotales(valorInput, ivaInput, totalInput) {
                const valor = parseFloat(valorInput.value) || 0;
                const iva = valor * 0.19;
                const total = valor + iva;
                
                ivaInput.value = iva.toFixed(2);
                totalInput.value = total.toFixed(2);
            }

            // Calcular IVA y Total en nuevo pedido
            const valorInput = document.getElementById('valorPedido');
            const ivaInput = document.getElementById('ivaPedido');
            const totalInput = document.getElementById('totalPedido');

            if (valorInput) {
                valorInput.addEventListener('input', function() {
                    calcularTotales(valorInput, ivaInput, totalInput);
                });
                // Calcular inicialmente
                calcularTotales(valorInput, ivaInput, totalInput);
            }

            // Calcular IVA y Total en editar pedido
            const editValorInput = document.getElementById('edit_valorPedido');
            const editIvaInput = document.getElementById('edit_ivaPedido');
            const editTotalInput = document.getElementById('edit_totalPedido');

            if (editValorInput) {
                editValorInput.addEventListener('input', function() {
                    calcularTotales(editValorInput, editIvaInput, editTotalInput);
                });
            }

            // Mostrar información del cliente en nuevo pedido
            const clienteSelect = document.getElementById('idCliente');
            const infoCliente = document.getElementById('infoCliente');

            if (clienteSelect) {
                clienteSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value !== '') {
                        document.getElementById('clienteNombre').textContent = 
                            selectedOption.getAttribute('data-nombre') + ' ' + selectedOption.getAttribute('data-apellido');
                        document.getElementById('clienteEmpresa').textContent = selectedOption.getAttribute('data-empresa');
                        document.getElementById('clienteEmail').textContent = selectedOption.getAttribute('data-email');
                        document.getElementById('clienteTelefono').textContent = selectedOption.getAttribute('data-telefono');
                        document.getElementById('clienteDireccion').textContent = selectedOption.getAttribute('data-direccion');
                        infoCliente.style.display = 'block';
                    } else {
                        infoCliente.style.display = 'none';
                    }
                });
            }

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

                    // Mostrar información del cliente seleccionado
                    const editClienteSelect = document.getElementById('edit_idCliente');
                    const selectedOption = Array.from(editClienteSelect.options).find(opt => opt.value === cliente);
                    if (selectedOption) {
                        document.getElementById('editClienteNombre').textContent = 
                            selectedOption.getAttribute('data-nombre') + ' ' + selectedOption.getAttribute('data-apellido');
                        document.getElementById('editClienteEmpresa').textContent = selectedOption.getAttribute('data-empresa');
                        document.getElementById('editClienteEmail').textContent = selectedOption.getAttribute('data-email');
                        document.getElementById('editClienteTelefono').textContent = selectedOption.getAttribute('data-telefono');
                        document.getElementById('editClienteDireccion').textContent = selectedOption.getAttribute('data-direccion');
                    }

                    // Calcular valores iniciales
                    calcularTotales(editValorInput, editIvaInput, editTotalInput);
                });
            }

            // Mostrar información del cliente en edición
            const editClienteSelect = document.getElementById('edit_idCliente');
            if (editClienteSelect) {
                editClienteSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value !== '') {
                        document.getElementById('editClienteNombre').textContent = 
                            selectedOption.getAttribute('data-nombre') + ' ' + selectedOption.getAttribute('data-apellido');
                        document.getElementById('editClienteEmpresa').textContent = selectedOption.getAttribute('data-empresa');
                        document.getElementById('editClienteEmail').textContent = selectedOption.getAttribute('data-email');
                        document.getElementById('editClienteTelefono').textContent = selectedOption.getAttribute('data-telefono');
                        document.getElementById('editClienteDireccion').textContent = selectedOption.getAttribute('data-direccion');
                    }
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
                    document.getElementById('valorPedido').value = '0';
                    document.getElementById('ivaPedido').value = '';
                    document.getElementById('totalPedido').value = '';
                    document.getElementById('repartidorPedido').value = '';
                    document.getElementById('infoCliente').style.display = 'none';
                    
                    // Recalcular valores
                    calcularTotales(valorInput, ivaInput, totalInput);
                });
            }

            // Configurar modal de eliminación
            const modalEliminar = document.getElementById('modalEliminarPedido');
            if (modalEliminar) {
                modalEliminar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    
                    // Actualizar el formulario de eliminación
                    document.getElementById('formEliminarPedido').action = `/pedidos/${id}`;
                });
            }
        });
    </script>
@endsection