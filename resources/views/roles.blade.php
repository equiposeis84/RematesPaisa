@extends('welcome')
@section('title', 'Roles de Usuario')
@section('content')
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Módulo Roles de Usuario</h3>
                
                <!-- Mensajes de alerta -->
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
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Nota:</strong> Los roles definen los permisos y acceso de los usuarios en el sistema. 
                    Cada usuario registrado debe tener asignado un rol.
                </div>
                
                <hr>

                <!-- Formulario de búsqueda -->
                <form name="roles" action="{{ route('roles.index') }}" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botones modificados -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                            <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                        </button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRol">
                            <i class="fa-solid fa-plus"></i> Nuevo Rol
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAgregarAdmin">
                            <i class="fa-solid fa-user-shield"></i> Agregar Administrador
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por ID o nombre del rol" 
                                       name="search"
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="{{ route('roles.index') }}" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                    
                    <!-- NUEVO: Botones de Filtro por Tipo de Rol -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group" role="group" aria-label="Filtros de roles">
                                <a href="{{ route('roles.index', ['filter' => 'all'] + request()->except('filter')) }}" 
                                   class="btn btn-outline-primary {{ request('filter', 'all') == 'all' ? 'active' : '' }}">
                                    <i class="fas fa-layer-group"></i> Todos los Roles
                                </a>
                                <a href="{{ route('roles.index', ['filter' => 'admins'] + request()->except('filter')) }}" 
                                   class="btn btn-outline-danger {{ request('filter') == 'admins' ? 'active' : '' }}">
                                    <i class="fas fa-user-shield"></i> Administradores
                                </a>
                                <a href="{{ route('roles.index', ['filter' => 'clientes'] + request()->except('filter')) }}" 
                                   class="btn btn-outline-success {{ request('filter') == 'clientes' ? 'active' : '' }}">
                                    <i class="fas fa-users"></i> Clientes
                                </a>
                                <a href="{{ route('roles.index', ['filter' => 'repartidores'] + request()->except('filter')) }}" 
                                   class="btn btn-outline-warning {{ request('filter') == 'repartidores' ? 'active' : '' }}">
                                    <i class="fas fa-motorcycle"></i> Repartidores
                                </a>
                                <a href="{{ route('roles.index', ['filter' => 'custom'] + request()->except('filter')) }}" 
                                   class="btn btn-outline-info {{ request('filter') == 'custom' ? 'active' : '' }}">
                                    <i class="fas fa-cogs"></i> Roles Personalizados
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- NUEVO: Indicador de filtro activo -->
                @if(request('filter') && request('filter') != 'all')
                    <div class="alert alert-secondary py-2">
                        <small>
                            <i class="fas fa-filter"></i> 
                            <strong>Filtro activo:</strong> 
                            @switch(request('filter'))
                                @case('admins')
                                    Mostrando solo <strong>Administradores</strong>
                                    @break
                                @case('clientes')
                                    Mostrando solo <strong>Clientes</strong>
                                    @break
                                @case('repartidores')
                                    Mostrando solo <strong>Repartidores</strong>
                                    @break
                                @case('custom')
                                    Mostrando solo <strong>Roles Personalizados</strong>
                                    @break
                            @endswitch
                            <a href="{{ route('roles.index', request()->except('filter')) }}" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="fas fa-times"></i> Quitar filtro
                            </a>
                        </small>
                    </div>
                @endif

                <!-- Tabla de roles -->
                @if($datos->count() > 0)
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Rol</th>
                            <th>Nombre del Rol</th>
                            <th>Usuarios Asignados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datos as $rol)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $rol->idRol }}</span>
                                </td> 
                                <td>
                                    <strong>{{ $rol->nombreRol }}</strong>
                                    @if($rol->idRol == 1)
                                        <span class="badge bg-danger ms-1">Administrador</span>
                                    @elseif($rol->idRol == 2)
                                        <span class="badge bg-success ms-1">Cliente</span>
                                    @elseif($rol->idRol == 3)
                                        <span class="badge bg-warning ms-1">Repartidor</span>
                                    @else
                                        <span class="badge bg-info ms-1">Personalizado</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-users"></i> 
                                        {{ $rol->usuarios_count }} usuarios
                                    </span>
                                </td>
                                <td>
                                    @if($rol->idRol != 1)
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarRol"
                                            data-id="{{ $rol->idRol }}"
                                            data-nombre="{{ $rol->nombreRol }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalAsignarUsuarios"
                                            data-id="{{ $rol->idRol }}"
                                            data-nombre="{{ $rol->nombreRol }}">
                                        <i class="fa-solid fa-user-plus"></i> Asignar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarRol"
                                            data-id="{{ $rol->idRol }}">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                    @else
                                    <span class="text-muted">Rol del sistema</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li class="page-item {{ $datos->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $datos->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('filter') ? '&filter=' . request('filter') : '' }}">
                                Atrás
                            </a>
                        </li>

                        @for ($i = 1; $i <= $datos->lastPage(); $i++)
                            <li class="page-item {{ $datos->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" 
                                   href="{{ $datos->url($i) }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('filter') ? '&filter=' . request('filter') : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                            
                        <li class="page-item {{ !$datos->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $datos->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('filter') ? '&filter=' . request('filter') : '' }}">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="text-muted mt-2">
                    Mostrando {{ $datos->firstItem() }} a {{ $datos->lastItem() }} de {{ $datos->total() }} registros
                    @if(request('filter') && request('filter') != 'all')
                        (filtrado por {{ request('filter') }})
                    @endif
                </div>

                @else
                <!-- Mensaje cuando no hay datos -->
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    No se encontraron roles que coincidan con los criterios de búsqueda.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalUsuarioLabel">Crear Nuevo Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle"></i> 
                            Complete los datos del nuevo usuario y asigne un rol.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                                           placeholder="Ej: Juan Pérez" maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required 
                                           placeholder="Ej: usuario@ejemplo.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña *</label>
                                    <input type="password" class="form-control" id="password" name="password" required 
                                           placeholder="Mínimo 6 caracteres" minlength="6">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="idRol" class="form-label">Rol *</label>
                                    <select class="form-select" id="idRol" name="idRol" required>
                                        <option value="">Seleccione un rol</option>
                                        @foreach($datos as $rol)
                                            <option value="{{ $rol->idRol }}">{{ $rol->nombreRol }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar Administrador -->
    <div class="modal fade" id="modalAgregarAdmin" tabindex="-1" aria-labelledby="modalAgregarAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalAgregarAdminLabel">Agregar Nuevo Administrador</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <small><i class="fas fa-exclamation-triangle"></i> 
                            <strong>Importante:</strong> Los administradores tienen acceso completo al sistema.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="admin_nombre" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="admin_nombre" name="nombre" required 
                                           placeholder="Ej: Administrador Principal" maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="admin_email" name="email" required 
                                           placeholder="Ej: admin@empresa.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Contraseña *</label>
                                    <input type="password" class="form-control" id="admin_password" name="password" required 
                                           placeholder="Mínimo 6 caracteres" minlength="6">
                                </div>
                            </div>
                        </div>

                        <!-- Rol fijo como Administrador (ID 1) -->
                        <input type="hidden" name="idRol" value="1">
                        
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle"></i> 
                            Este usuario será creado con el rol de <strong>Administrador</strong> y tendrá acceso completo al sistema.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Crear Administrador</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo Rol (existente) -->
    <div class="modal fade" id="modalRol" tabindex="-1" aria-labelledby="modalRolLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalRolLabel">Crear Nuevo Rol</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <small><i class="fas fa-exclamation-triangle"></i> 
                            Los roles definen los permisos de los usuarios. Crea roles específicos para diferentes tipos de usuarios.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idRol" class="form-label">ID Rol *</label>
                                    <input type="number" class="form-control" id="idRol" name="idRol" required 
                                           placeholder="Ej: 4, 5, 6..." min="4">
                                    <div class="form-text">Los IDs 1-3 están reservados para roles del sistema</div>
                                    @error('idRol')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombreRol" class="form-label">Nombre del Rol *</label>
                                    <input type="text" class="form-control" id="nombreRol" name="nombreRol" required 
                                           placeholder="Ej: Supervisor, Vendedor, etc." maxlength="45">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Crear Rol</button>
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
                    <h1 class="modal-title fs-5" id="modalEditarRolLabel">Editar Rol de Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarRol" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Asignar Usuarios al Rol -->
    <div class="modal fade" id="modalAsignarUsuarios" tabindex="-1" aria-labelledby="modalAsignarUsuariosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalAsignarUsuariosLabel">Asignar Usuarios al Rol: <span id="nombreRolAsignar"></span></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAsignarUsuarios" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle"></i> 
                            Seleccione los usuarios que desea asignar a este rol.</small>
                        </div>
                        
                        <!-- Buscador de usuarios -->
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscarUsuario" 
                                       placeholder="Buscar usuarios por nombre o email...">
                                <button type="button" class="btn btn-outline-secondary" id="limpiarBusqueda">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                            <div class="form-text">Escribe para filtrar la lista de usuarios</div>
                        </div>
                        
                        <!-- Lista de usuarios -->
                        <div class="mb-3">
                            <label class="form-label">Usuarios Disponibles 
                                <span class="badge bg-primary" id="contadorTotal">0</span>
                            </label>
                            <div id="listaUsuarios" style="max-height: 300px; overflow-y: auto;" class="border rounded p-2">
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p>Cargando usuarios...</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contador y controles -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted" id="contadorUsuarios">0 usuarios seleccionados</small>
                                <small class="text-muted ms-2" id="contadorFiltrados"></small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-success btn-sm" id="seleccionarTodos">
                                    <i class="fas fa-check"></i> Todos
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="deseleccionarTodos">
                                    <i class="fas fa-times"></i> Ninguno
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Asignar Usuarios Seleccionados</button>
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
                        <div class="alert alert-warning">
                            <small><i class="fas fa-exclamation-triangle"></i> 
                            <strong>Advertencia:</strong> Si hay usuarios asignados a este rol, no podrá eliminarlo. 
                            Primero debe reasignar esos usuarios a otro rol.</small>
                        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Auto cerrar alertas después de 5 segundos
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

                    document.getElementById('formEditarRol').action = `/roles/${id}`;
                    document.getElementById('edit_idRol').value = id;
                    document.getElementById('edit_nombreRol').value = nombre;
                });
            }

            // Configurar modal de asignar usuarios
            const modalAsignar = document.getElementById('modalAsignarUsuarios');
            if (modalAsignar) {
                modalAsignar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const idRol = button.getAttribute('data-id');
                    const nombreRol = button.getAttribute('data-nombre');

                    document.getElementById('formAsignarUsuarios').action = `/roles/${idRol}/asignar-usuarios`;
                    document.getElementById('nombreRolAsignar').textContent = nombreRol;
                    document.getElementById('buscarUsuario').value = '';
                    
                    cargarUsuariosParaRol(idRol);
                });
            }

            // Configurar modal de eliminación
            const modalEliminar = document.getElementById('modalEliminarRol');
            if (modalEliminar) {
                modalEliminar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    document.getElementById('formEliminarRol').action = `/roles/${id}`;
                });
            }

            // Limpiar formularios cuando se cierran los modales
            const modalNuevoUsuario = document.getElementById('modalUsuario');
            if (modalNuevoUsuario) {
                modalNuevoUsuario.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('nombre').value = '';
                    document.getElementById('email').value = '';
                    document.getElementById('password').value = '';
                    document.getElementById('idRol').value = '';
                });
            }

            const modalNuevoAdmin = document.getElementById('modalAgregarAdmin');
            if (modalNuevoAdmin) {
                modalNuevoAdmin.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('admin_nombre').value = '';
                    document.getElementById('admin_email').value = '';
                    document.getElementById('admin_password').value = '';
                });
            }

            const modalNuevoRol = document.getElementById('modalRol');
            if (modalNuevoRol) {
                modalNuevoRol.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('idRol').value = '';
                    document.getElementById('nombreRol').value = '';
                });
            }

            // Variables globales para usuarios
            let todosLosUsuarios = [];
            let usuariosFiltrados = [];

            // Función para cargar usuarios
            function cargarUsuariosParaRol(idRol) {
                fetch(`/roles/${idRol}/usuarios`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(usuarios => {
                        todosLosUsuarios = usuarios;
                        usuariosFiltrados = [...usuarios];
                        renderizarUsuarios();
                        actualizarContadores();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('listaUsuarios').innerHTML = 
                            '<div class="alert alert-danger">Error al cargar usuarios. Verifique la conexión.</div>';
                    });
            }

            // Función para renderizar usuarios
            function renderizarUsuarios() {
                const listaUsuarios = document.getElementById('listaUsuarios');
                
                if (usuariosFiltrados.length === 0) {
                    listaUsuarios.innerHTML = '<div class="alert alert-warning">No se encontraron usuarios que coincidan con la búsqueda.</div>';
                    return;
                }

                let html = '';
                usuariosFiltrados.forEach(usuario => {
                    const usuarioHTML = `
                        <div class="form-check mb-2 p-2 border rounded usuario-item" data-id="${usuario.idUsuario}" data-nombre="${usuario.nombre.toLowerCase()}" data-email="${usuario.email.toLowerCase()}">
                            <input class="form-check-input usuario-checkbox" type="checkbox" 
                                   name="usuarios[]" value="${usuario.idUsuario}" id="usuario_${usuario.idUsuario}"
                                   ${usuario.seleccionado ? 'checked' : ''}>
                            <label class="form-check-label w-100" for="usuario_${usuario.idUsuario}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="nombre-usuario">${usuario.nombre}</strong><br>
                                        <small class="text-muted email-usuario">${usuario.email}</small>
                                    </div>
                                    <span class="badge ${usuario.rol_actual === 'Sin rol' ? 'bg-warning' : 'bg-info'}">
                                        ${usuario.rol_actual}
                                    </span>
                                </div>
                            </label>
                        </div>
                    `;
                    html += usuarioHTML;
                });
                
                listaUsuarios.innerHTML = html;
                actualizarContadores();
            }

            // Función para filtrar usuarios
            function filtrarUsuarios(termino) {
                if (!termino) {
                    usuariosFiltrados = [...todosLosUsuarios];
                } else {
                    const terminoLower = termino.toLowerCase();
                    usuariosFiltrados = todosLosUsuarios.filter(usuario => 
                        usuario.nombre.toLowerCase().includes(terminoLower) ||
                        usuario.email.toLowerCase().includes(terminoLower)
                    );
                }
                renderizarUsuarios();
            }

            // Actualizar contadores
            function actualizarContadores() {
                const seleccionados = document.querySelectorAll('.usuario-checkbox:checked').length;
                const totalFiltrados = usuariosFiltrados.length;
                const totalGeneral = todosLosUsuarios.length;
                
                document.getElementById('contadorUsuarios').textContent = 
                    `${seleccionados} usuario(s) seleccionados`;
                document.getElementById('contadorTotal').textContent = totalGeneral;
                
                if (totalFiltrados !== totalGeneral) {
                    document.getElementById('contadorFiltrados').textContent = 
                        `(${totalFiltrados} filtrados)`;
                } else {
                    document.getElementById('contadorFiltrados').textContent = '';
                }
            }

            // Event listeners para el buscador
            document.getElementById('buscarUsuario')?.addEventListener('input', function(e) {
                filtrarUsuarios(e.target.value);
            });

            document.getElementById('limpiarBusqueda')?.addEventListener('click', function() {
                document.getElementById('buscarUsuario').value = '';
                filtrarUsuarios('');
            });

            // Seleccionar/Deseleccionar todos
            document.getElementById('seleccionarTodos')?.addEventListener('click', function() {
                document.querySelectorAll('.usuario-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
                actualizarContadores();
            });

            document.getElementById('deseleccionarTodos')?.addEventListener('click', function() {
                document.querySelectorAll('.usuario-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                actualizarContadores();
            });

            // Event listener para checkboxes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('usuario-checkbox')) {
                    actualizarContadores();
                }
            });
        });
    </script>
@endsection