@extends('welcome')
@section('title', 'Proveedores')
@section('content')
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Modulo Proveedores</h3>
                
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

                <!-- Mostrar errores de validación -->
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
                
                <hr>

                <!-- Formulario de búsqueda -->
                <form name="proveedores" action="{{ url('/proveedores') }}" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botón para abrir modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProveedor">
                            <i class="fa-solid fa-plus"></i> Nuevo
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por NIT, nombre, email o teléfono" 
                                       name="search"
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="{{ url('/proveedores') }}" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla proveedores -->
                @if($datos->count() > 0)
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>NIT Proveedor</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datos as $item)
                            <tr>
                                <td>{{ $item->NITProveedores }}</td> 
                                <td>{{ $item->nombreProveedor }}</td>  
                                <td>{{ $item->telefonoProveedor }}</td>
                                <td>{{ $item->correoProveedor }}</td>  
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarProveedor"
                                            data-id="{{ $item->NITProveedores }}"
                                            data-nombre="{{ $item->nombreProveedor }}"
                                            data-telefono="{{ $item->telefonoProveedor }}"
                                            data-correo="{{ $item->correoProveedor }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                 <button type="button" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarProveedor"
                                            data-id="{{ $item->NITProveedores }}"
                                            data-nombre="{{ $item->nombreProveedor }}">
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
                        No se encontraron proveedores con "{{ request('search') }}"
                    @else
                        No hay proveedores registrados.
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div> <!-- Fin del container -->

    <!-- Modal para Nuevo Proveedor -->
    <div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalProveedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalProveedorLabel">Nuevo Proveedor</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('proveedores.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- CAMPO PARA NIT PROVEEDOR -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="NITProveedores" class="form-label">NIT Proveedor *</label>
                                    <input type="number" class="form-control" id="NITProveedores" name="NITProveedores" 
                                           value="{{ old('NITProveedores') }}" required 
                                           placeholder="Ej: 123456789" min="1">
                                    <div class="form-text">Ingrese solo números (sin puntos, comas o espacios)</div>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO NIT PROVEEDOR -->
                    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombreProveedor" class="form-label">Nombre del Proveedor *</label>
                                    <input type="text" class="form-control" id="nombreProveedor" name="nombreProveedor" 
                                           value="{{ old('nombreProveedor') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefonoProveedor" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="telefonoProveedor" name="telefonoProveedor" 
                                           value="{{ old('telefonoProveedor') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="correoProveedor" class="form-label">Correo Electrónico *</label>
                                    <input type="email" class="form-control" id="correoProveedor" name="correoProveedor" 
                                           value="{{ old('correoProveedor') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Proveedor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Proveedor -->
    <div class="modal fade" id="modalEditarProveedor" tabindex="-1" aria-labelledby="modalEditarProveedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditarProveedorLabel">Editar Proveedor</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarProveedor" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- CAMPO PARA NIT PROVEEDOR (solo lectura) -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_NITProveedores" class="form-label">NIT Proveedor *</label>
                                    <input type="number" class="form-control" id="edit_NITProveedores" name="NITProveedores" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO NIT PROVEEDOR -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_nombreProveedor" class="form-label">Nombre del Proveedor *</label>
                                    <input type="text" class="form-control" id="edit_nombreProveedor" name="nombreProveedor" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_telefonoProveedor" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="edit_telefonoProveedor" name="telefonoProveedor" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_correoProveedor" class="form-label">Correo Electrónico *</label>
                                    <input type="email" class="form-control" id="edit_correoProveedor" name="correoProveedor" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Proveedor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Proveedor -->
    <div class="modal fade" id="modalEliminarProveedor" tabindex="-1" aria-labelledby="modalEliminarProveedorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEliminarProveedorLabel">Eliminar Proveedor</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEliminarProveedor" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar al proveedor <strong><span id="nombreProveedorEliminar"></span></strong>?</p>
                        <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
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
            const modalEditar = document.getElementById('modalEditarProveedor');
            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nombre = button.getAttribute('data-nombre');
                    const telefono = button.getAttribute('data-telefono');
                    const correo = button.getAttribute('data-correo');

                    // Actualizar el formulario
                    document.getElementById('formEditarProveedor').action = `/proveedores/${id}`;
                    document.getElementById('edit_NITProveedores').value = id;
                    document.getElementById('edit_nombreProveedor').value = nombre;
                    document.getElementById('edit_telefonoProveedor').value = telefono;
                    document.getElementById('edit_correoProveedor').value = correo;
                });
            }

            // Limpiar formulario de nuevo proveedor cuando se cierra el modal
            const modalNuevo = document.getElementById('modalProveedor');
            if (modalNuevo) {
                modalNuevo.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('NITProveedores').value = '';
                    document.getElementById('nombreProveedor').value = '';
                    document.getElementById('telefonoProveedor').value = '';
                    document.getElementById('correoProveedor').value = '';
                });
            }

            // Configurar modal de eliminación
            const modalEliminar = document.getElementById('modalEliminarProveedor');
            if (modalEliminar) {
                modalEliminar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nombre = button.getAttribute('data-nombre');
                    
                    // Actualizar el nombre en el modal
                    const nombreSpan = document.getElementById('nombreProveedorEliminar');
                    if (nombreSpan && nombre) {
                        nombreSpan.textContent = nombre;
                    }
                    
                    document.getElementById('formEliminarProveedor').action = `/proveedores/${id}`;
                });
            }
        });
    </script>
@endsection