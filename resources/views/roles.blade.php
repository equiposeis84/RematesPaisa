@extends('welcome')
@section('title', 'Roles')
@section('content')
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Módulo Roles</h3>
                
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
                <form name="roles" action="{{ route('roles.index') }}" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botón para abrir modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRol">
                            <i class="fa-solid fa-plus"></i> Nuevo
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por ID, nombre o descripción" 
                                       name="search"
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="{{ route('roles.index') }}" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla roles -->
                @if($datos->count() > 0)
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Rol</th>
                            <th>Nombre del Rol</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datos as $rol)
                            <tr>
                                <td>{{ $rol->idRol }}</td> 
                                <td>{{ $rol->nombreRol }}</td>  
                                <td>{{ $rol->descripcionRol ?? 'N/A' }}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarRol"
                                            data-id="{{ $rol->idRol }}"
                                            data-nombre="{{ $rol->nombreRol }}"
                                            data-descripcion="{{ $rol->descripcionRol }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarRol"
                                            data-id="{{ $rol->idRol }}">
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
                        No se encontraron roles con "{{ request('search') }}"
                    @else
                        No hay roles registrados.
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div> <!-- Fin del container -->

    <!-- Modal para Nuevo Rol -->
    <div class="modal fade" id="modalRol" tabindex="-1" aria-labelledby="modalRolLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalRolLabel">Nuevo Rol</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- CAMPO PARA ID ROL -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idRol" class="form-label">ID Rol *</label>
                                    <input type="number" class="form-control" id="idRol" name="idRol" required 
                                           placeholder="Ej: 1, 2, 3..." min="1">
                                    @error('idRol')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombreRol" class="form-label">Nombre del Rol *</label>
                                    <input type="text" class="form-control" id="nombreRol" name="nombreRol" required 
                                           placeholder="Ej: Admin, Cliente, Repartidor" maxlength="45">
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO ID ROL -->
                    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="descripcionRol" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcionRol" name="descripcionRol" 
                                              rows="3" placeholder="Descripción del rol..." maxlength="255"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Rol -->
    <div class="modal fade" id="modalEditarRol" tabindex="-1" aria-labelledby="modalEditarRolLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditarRolLabel">Editar Rol</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarRol" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- CAMPO PARA ID ROL (solo lectura) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_idRol" class="form-label">ID Rol *</label>
                                    <input type="number" class="form-control" id="edit_idRol" name="idRol" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nombreRol" class="form-label">Nombre del Rol *</label>
                                    <input type="text" class="form-control" id="edit_nombreRol" name="nombreRol" required 
                                           maxlength="45">
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO ID ROL -->
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_descripcionRol" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="edit_descripcionRol" name="descripcionRol" 
                                              rows="3" maxlength="255"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Rol -->
    <div class="modal fade" id="modalEliminarRol" tabindex="-1" aria-labelledby="modalEliminarRolLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEliminarRolLabel">Eliminar Rol</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEliminarRol" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este rol?</p>
                        <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar Rol</button>
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
            const modalEditar = document.getElementById('modalEditarRol');
            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nombre = button.getAttribute('data-nombre');
                    const descripcion = button.getAttribute('data-descripcion');

                    // Actualizar el formulario
                    document.getElementById('formEditarRol').action = `/roles/${id}`;
                    document.getElementById('edit_idRol').value = id;
                    document.getElementById('edit_nombreRol').value = nombre;
                    document.getElementById('edit_descripcionRol').value = descripcion || '';
                });
            }

            // Configurar modal de eliminación
            const modalEliminar = document.getElementById('modalEliminarRol');
            if (modalEliminar) {
                modalEliminar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    
                    // Actualizar el formulario de eliminación
                    document.getElementById('formEliminarRol').action = `/roles/${id}`;
                });
            }

            // Limpiar formulario de nuevo rol cuando se cierra el modal
            const modalNuevo = document.getElementById('modalRol');
            if (modalNuevo) {
                modalNuevo.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('idRol').value = '';
                    document.getElementById('nombreRol').value = '';
                    document.getElementById('descripcionRol').value = '';
                });
            }
        });
    </script>
@endsection