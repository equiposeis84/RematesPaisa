@extends('VistasAdmin.welcome')
@section('title', 'Gestión de Usuarios y Roles')
@section('content')
<div class="container-sm d-flex justify-content-center mt-5">
    <div class="card">
        <div class="card-body" style="width: 1200px;">
            <h3>Módulo Gestión de Usuarios y Roles</h3>
            
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
                <strong>Gestión completa:</strong> Usuarios, roles y permisos en un solo lugar.
            </div>
            
            <hr>

            <!-- Pestañas de navegación -->
            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $tab == 'usuarios' ? 'active' : '' }}" 
                            id="usuarios-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#usuarios" 
                            type="button" 
                            role="tab"
                            onclick="window.location.href='{{ route('roles.index', ['tab' => 'usuarios']) }}'">
                        <i class="fas fa-users"></i> Usuarios
                        <span class="badge bg-primary">{{ $stats['totalUsuarios'] ?? 0 }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $tab == 'roles' ? 'active' : '' }}" 
                            id="roles-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#roles" 
                            type="button" 
                            role="tab"
                            onclick="window.location.href='{{ route('roles.index', ['tab' => 'roles']) }}'">
                        <i class="fas fa-user-tag"></i> Roles
                        <span class="badge bg-info">{{ $stats['totalRoles'] ?? 0 }}</span>
                    </button>
                </li>
            </ul>

            <!-- Contenido de pestañas -->
            <div class="tab-content" id="myTabContent">
                
                <!-- =========== PESTAÑA USUARIOS =========== -->
                <div class="tab-pane fade {{ $tab == 'usuarios' ? 'show active' : '' }}" 
                     id="usuarios" 
                     role="tabpanel" 
                     aria-labelledby="usuarios-tab">
                    
                    <!-- Botones superiores -->
                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                            <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAgregarAdmin">
                            <i class="fa-solid fa-user-shield"></i> Agregar Administrador
                        </button>
                    </div>
                    
                    <!-- Formulario de búsqueda y filtros -->
                    <form method="GET" action="{{ route('roles.index') }}">
                        <input type="hidden" name="tab" value="usuarios">
                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" 
                                           placeholder="Buscar por nombre, email, teléfono, dirección..." 
                                           name="search"
                                           value="{{ $search ?? '' }}">
                                    <button type="submit" class="btn btn-info">Buscar</button>
                                    @if($search)
                                    <a href="{{ route('roles.index', ['tab' => 'usuarios']) }}" class="btn btn-warning">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de Filtro -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="btn-group" role="group" aria-label="Filtros de usuarios">
                                    <a href="{{ route('roles.index', ['tab' => 'usuarios', 'filter' => 'all']) }}" 
                                       class="btn btn-outline-primary {{ $filter == 'all' ? 'active' : '' }}">
                                        <i class="fas fa-layer-group"></i> Todos
                                    </a>
                                    <a href="{{ route('roles.index', ['tab' => 'usuarios', 'filter' => 'admins']) }}" 
                                       class="btn btn-outline-danger {{ $filter == 'admins' ? 'active' : '' }}">
                                        <i class="fas fa-user-shield"></i> Admins
                                    </a>
                                    <a href="{{ route('roles.index', ['tab' => 'usuarios', 'filter' => 'clientes']) }}" 
                                       class="btn btn-outline-success {{ $filter == 'clientes' ? 'active' : '' }}">
                                        <i class="fas fa-users"></i> Clientes
                                    </a>
                                    <a href="{{ route('roles.index', ['tab' => 'usuarios', 'filter' => 'repartidores']) }}" 
                                       class="btn btn-outline-warning {{ $filter == 'repartidores' ? 'active' : '' }}">
                                        <i class="fas fa-motorcycle"></i> Repartidores
                                    </a>
                                    <a href="{{ route('roles.index', ['tab' => 'usuarios', 'filter' => 'custom']) }}" 
                                       class="btn btn-outline-info {{ $filter == 'custom' ? 'active' : '' }}">
                                        <i class="fas fa-cogs"></i> Personalizados
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Tabla de usuarios -->
                    @if($usuarios->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Dirección</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">#{{ $usuario->idUsuario }}</span>
                                            @if($usuario->idUsuario == 1)
                                            <span class="badge bg-danger">Principal</span>
                                            @endif
                                        </td> 
                                        <td>
                                            <strong>{{ $usuario->nombre }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $usuario->tipoDocumento ?? 'CC' }}: 
                                                {{ $usuario->documento ?? 'No registrado' }}
                                            </small>
                                        </td>  
                                        <td>{{ $usuario->email }}</td>
                                        <td>
                                            @if($usuario->telefono)
                                                {{ $usuario->telefono }}
                                            @else
                                                <span class="text-muted">No registrado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($usuario->direccion)
                                                <span title="{{ $usuario->direccion }}">
                                                    {{ Str::limit($usuario->direccion, 30) }}
                                                </span>
                                            @else
                                                <span class="text-muted">No registrada</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $rolColors = [
                                                    1 => 'danger',    // Admin
                                                    2 => 'success',   // Cliente
                                                    3 => 'warning',   // Repartidor
                                                ];
                                                $color = $rolColors[$usuario->idRol] ?? 'info';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ $usuario->rol->nombreRol ?? 'Sin rol' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($usuario->activo)
                                            <span class="badge bg-success">Activo</span>
                                            @else
                                            <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Editar -->
                                                <button type="button" class="btn btn-warning btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEditarUsuario"
                                                        data-id="{{ $usuario->idUsuario }}"
                                                        data-nombre="{{ $usuario->nombre }}"
                                                        data-email="{{ $usuario->email }}"
                                                        data-idrol="{{ $usuario->idRol }}"
                                                        data-documento="{{ $usuario->documento }}"
                                                        data-tipodocumento="{{ $usuario->tipoDocumento }}"
                                                        data-direccion="{{ $usuario->direccion }}"
                                                        data-telefono="{{ $usuario->telefono }}"
                                                        data-activo="{{ $usuario->activo }}">
                                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                                </button>
                                                
                                                <!-- Cambiar estado -->
                                                <form action="{{ route('roles.usuarios.toggleEstado', $usuario->idUsuario) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-secondary btn-sm" 
                                                            title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}">
                                                        <i class="fas fa-power-off"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Eliminar -->
                                                @if($usuario->idUsuario != 1)
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEliminarUsuario"
                                                        data-id="{{ $usuario->idUsuario }}"
                                                        data-nombre="{{ $usuario->nombre }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end">
                            <li class="page-item {{ $usuarios->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" 
                                   href="{{ $usuarios->previousPageUrl() }}{{ $search ? '&search=' . $search : '' }}{{ $filter ? '&filter=' . $filter : '' }}&tab=usuarios">
                                    Atrás
                                </a>
                            </li>

                            @for ($i = 1; $i <= $usuarios->lastPage(); $i++)
                                <li class="page-item {{ $usuarios->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" 
                                       href="{{ $usuarios->url($i) }}{{ $search ? '&search=' . $search : '' }}{{ $filter ? '&filter=' . $filter : '' }}&tab=usuarios">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor
                                
                            <li class="page-item {{ !$usuarios->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" 
                                   href="{{ $usuarios->nextPageUrl() }}{{ $search ? '&search=' . $search : '' }}{{ $filter ? '&filter=' . $filter : '' }}&tab=usuarios">
                                    Siguiente
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <div class="text-muted mt-2">
                        Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuarios
                    </div>

                    @else
                    <div class="alert alert-info text-center mt-3">
                        <i class="fas fa-info-circle"></i> 
                        @if($search)
                            No se encontraron usuarios con "{{ $search }}"
                        @elseif($filter && $filter != 'all')
                            No hay usuarios del tipo seleccionado.
                        @else
                            No hay usuarios registrados.
                        @endif
                    </div>
                    @endif
                    
                </div> <!-- Fin pestaña usuarios -->
                
                <!-- =========== PESTAÑA ROLES =========== -->
                <div class="tab-pane fade {{ $tab == 'roles' ? 'show active' : '' }}" 
                     id="roles" 
                     role="tabpanel" 
                     aria-labelledby="roles-tab">
                    
                    <!-- Botones superiores -->
                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRol">
                            <i class="fa-solid fa-plus"></i> Nuevo Rol
                        </button>
                    </div>
                    
                    <!-- Formulario de búsqueda -->
                    <form method="GET" action="{{ route('roles.index') }}" class="mb-4">
                        <input type="hidden" name="tab" value="roles">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" 
                                           placeholder="Buscar por nombre o ID de rol" 
                                           name="search"
                                           value="{{ $search ?? '' }}">
                                    <button type="submit" class="btn btn-info">Buscar</button>
                                    @if($search)
                                    <a href="{{ route('roles.index', ['tab' => 'roles']) }}" class="btn btn-warning">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Tabla de roles -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Rol</th>
                                    <th>Nombre del Rol</th>
                                    <th>Usuarios Asignados</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $rol)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $rol->idRol }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $rol->nombreRol }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $rol->usuarios_count }} usuario(s)
                                            </span>
                                        </td>
                                        <td>
                                            @if($rol->idRol <= 3)
                                                <span class="badge bg-danger">Sistema</span>
                                            @else
                                                <span class="badge bg-success">Personalizado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rol->idRol > 3) <!-- Solo editar roles personalizados -->
                                            <div class="btn-group" role="group">
                                                <!-- Editar rol -->
                                                <button type="button" class="btn btn-warning btn-sm btn-editar-rol" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEditarRol"
                                                        data-id="{{ $rol->idRol }}"
                                                        data-nombre="{{ $rol->nombreRol }}">
                                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                                </button>
                                                
                                                <!-- Eliminar rol -->
                                                <form action="{{ route('roles.destroy', $rol->idRol) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('¿Eliminar el rol {{ $rol->nombreRol }}? Esta acción no se puede deshacer.')">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @else
                                            <span class="text-muted">Rol del sistema</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($roles->isEmpty())
                    <div class="alert alert-info text-center mt-3">
                        <i class="fas fa-info-circle"></i> 
                        @if($search)
                            No se encontraron roles con "{{ $search }}"
                        @else
                            No hay roles registrados.
                        @endif
                    </div>
                    @endif
                    
                </div> <!-- Fin pestaña roles -->
                
            </div> <!-- Fin tab-content -->
            
        </div> <!-- Fin card-body -->
    </div> <!-- Fin card -->
</div> <!-- Fin container -->

<!-- =========== MODALES =========== -->

<!-- Modal para Nuevo Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalUsuarioLabel">
                    <i class="fas fa-user-plus"></i> Crear Nuevo Usuario
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('roles.usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required 
                                       placeholder="Ej: Juan Pérez" maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="Ej: usuario@ejemplo.com">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="password" name="password" required 
                                       placeholder="Mínimo 6 caracteres" minlength="6">
                            </div>
                            <div class="mb-3">
                                <label for="idRol" class="form-label">Rol *</label>
                                <select class="form-select" id="idRol" name="idRol" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach($todosRoles as $rol)
                                        <option value="{{ $rol->idRol }}">{{ $rol->nombreRol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipoDocumento" class="form-label">Tipo documento</label>
                                <select class="form-select" id="tipoDocumento" name="tipoDocumento">
                                    <option value="CC" selected>Cédula</option>
                                    <option value="TI">Tarjeta Identidad</option>
                                    <option value="CE">Cédula Extranjería</option>
                                    <option value="PAS">Pasaporte</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="text" class="form-control" id="documento" name="documento" 
                                       placeholder="Número de documento">
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" 
                                       placeholder="Ej: 3001234567">
                            </div>
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="2" 
                                          placeholder="Dirección completa"></textarea>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activo" id="activo" checked>
                                <label class="form-check-label" for="activo">
                                    Usuario activo
                                </label>
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
                <h1 class="modal-title fs-5" id="modalAgregarAdminLabel">
                    <i class="fas fa-user-shield"></i> Agregar Administrador
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('roles.usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <small><i class="fas fa-exclamation-triangle"></i> 
                        Los administradores tienen acceso completo al sistema.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_nombre" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="admin_nombre" name="nombre" required 
                               placeholder="Ej: Administrador Principal" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="admin_email" name="email" required 
                               placeholder="Ej: admin@empresa.com">
                    </div>
                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Contraseña *</label>
                        <input type="password" class="form-control" id="admin_password" name="password" required 
                               placeholder="Mínimo 6 caracteres" minlength="6">
                    </div>
                    
                    <!-- Rol fijo como Administrador (ID 1) -->
                    <input type="hidden" name="idRol" value="1">
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" id="admin_activo" checked>
                        <label class="form-check-label" for="admin_activo">
                            Usuario activo
                        </label>
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

<!-- Modal para Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalEditarUsuarioLabel">
                    <i class="fas fa-user-edit"></i> Editar Usuario
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarUsuario" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nombre" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="edit_nombre" name="nombre" required 
                                       maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="edit_password" name="password" 
                                       placeholder="Dejar vacío para mantener la actual" minlength="6">
                                <div class="form-text">Solo complete si desea cambiar la contraseña</div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_idRol" class="form-label">Rol *</label>
                                <select class="form-select" id="edit_idRol" name="idRol" required>
                                    @foreach($todosRoles as $rol)
                                        <option value="{{ $rol->idRol }}">{{ $rol->nombreRol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tipoDocumento" class="form-label">Tipo documento</label>
                                <select class="form-select" id="edit_tipoDocumento" name="tipoDocumento">
                                    <option value="CC">Cédula</option>
                                    <option value="TI">Tarjeta Identidad</option>
                                    <option value="CE">Cédula Extranjería</option>
                                    <option value="PAS">Pasaporte</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_documento" class="form-label">Documento</label>
                                <input type="text" class="form-control" id="edit_documento" name="documento">
                            </div>
                            <div class="mb-3">
                                <label for="edit_telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="edit_telefono" name="telefono">
                            </div>
                            <div class="mb-3">
                                <label for="edit_direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" id="edit_direccion" name="direccion" rows="2"></textarea>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activo" id="edit_activo">
                                <label class="form-check-label" for="edit_activo">
                                    Usuario activo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Usuario -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalEliminarUsuarioLabel">
                    <i class="fas fa-trash"></i> Eliminar Usuario
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEliminarUsuario" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar al usuario <strong id="nombreUsuarioEliminar"></strong>?</p>
                    <div class="alert alert-warning">
                        <small><i class="fas fa-exclamation-triangle"></i> 
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Nuevo Rol -->
<div class="modal fade" id="modalRol" tabindex="-1" aria-labelledby="modalRolLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalRolLabel">
                    <i class="fas fa-plus-circle"></i> Crear Nuevo Rol
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <small><i class="fas fa-exclamation-triangle"></i> 
                        Los IDs 1-3 están reservados para roles del sistema.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="idRol" class="form-label">ID Rol *</label>
                        <input type="number" class="form-control" id="idRol" name="idRol" required 
                               placeholder="Ej: 4, 5, 6..." min="4">
                        <div class="form-text">Usar números mayores a 3</div>
                    </div>
                    <div class="mb-3">
                        <label for="nombreRol" class="form-label">Nombre del Rol *</label>
                        <input type="text" class="form-control" id="nombreRol" name="nombreRol" required 
                               placeholder="Ej: Supervisor, Vendedor, etc." maxlength="45">
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
                <h1 class="modal-title fs-5" id="modalEditarRolLabel">
                    <i class="fas fa-edit"></i> Editar Rol
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarRol" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editar_nombreRol" class="form-label">Nombre del Rol *</label>
                        <input type="text" class="form-control" id="editar_nombreRol" name="nombreRol" required 
                               maxlength="45">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar Rol</button>
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

    // =========== MODAL EDITAR USUARIO ===========
    const modalEditarUsuario = document.getElementById('modalEditarUsuario');
    if (modalEditarUsuario) {
        modalEditarUsuario.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');
            const email = button.getAttribute('data-email');
            const idRol = button.getAttribute('data-idrol');
            const documento = button.getAttribute('data-documento');
            const tipoDocumento = button.getAttribute('data-tipodocumento');
            const direccion = button.getAttribute('data-direccion');
            const telefono = button.getAttribute('data-telefono');
            const activo = button.getAttribute('data-activo');

            // Configurar formulario
            document.getElementById('formEditarUsuario').action = '/roles/usuarios/' + id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_idRol').value = idRol;
            document.getElementById('edit_documento').value = documento || '';
            document.getElementById('edit_tipoDocumento').value = tipoDocumento || 'CC';
            document.getElementById('edit_direccion').value = direccion || '';
            document.getElementById('edit_telefono').value = telefono || '';
            document.getElementById('edit_activo').checked = activo === '1';
        });
    }

    // =========== MODAL ELIMINAR USUARIO ===========
    const modalEliminarUsuario = document.getElementById('modalEliminarUsuario');
    if (modalEliminarUsuario) {
        modalEliminarUsuario.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');

            document.getElementById('formEliminarUsuario').action = '/roles/usuarios/' + id;
            document.getElementById('nombreUsuarioEliminar').textContent = nombre;
        });
    }

    // =========== MODAL EDITAR ROL ===========
    const modalEditarRol = document.getElementById('modalEditarRol');
    if (modalEditarRol) {
        modalEditarRol.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');

            document.getElementById('formEditarRol').action = '/roles/' + id;
            document.getElementById('editar_nombreRol').value = nombre;
        });
    }

    // Limpiar formularios cuando se cierran los modales
    document.getElementById('modalUsuario')?.addEventListener('hidden.bs.modal', function () {
        this.querySelector('form').reset();
    });
    
    document.getElementById('modalAgregarAdmin')?.addEventListener('hidden.bs.modal', function () {
        this.querySelector('form').reset();
    });
    
    document.getElementById('modalRol')?.addEventListener('hidden.bs.modal', function () {
        this.querySelector('form').reset();
    });
});
</script>
@endsection