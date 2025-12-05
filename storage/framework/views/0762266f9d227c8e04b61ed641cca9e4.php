<?php $__env->startSection('title', 'Gestión de Usuarios'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Módulo Gestión de Usuarios</h3>
                
                <!-- Mensajes de alerta -->
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Nota:</strong> Gestión completa de usuarios del sistema. Puede crear, editar, cambiar roles y eliminar usuarios.
                </div>
                
                <hr>

                <!-- Formulario de búsqueda -->
                <form name="usuarios" action="<?php echo e(route('roles.index')); ?>" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botones principales -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                            <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAgregarAdmin">
                            <i class="fa-solid fa-user-shield"></i> Agregar Administrador
                        </button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRol">
                            <i class="fa-solid fa-plus"></i> Nuevo Rol
                        </button>
                    </div>
                    
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por nombre, email o rol" 
                                       name="search"
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                    
                    <!-- Botones de Filtro por Tipo de Usuario -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group" role="group" aria-label="Filtros de usuarios">
                                <a href="<?php echo e(route('roles.index', ['filter' => 'all'] + request()->except('filter'))); ?>" 
                                   class="btn btn-outline-primary <?php echo e(request('filter', 'all') == 'all' ? 'active' : ''); ?>">
                                    <i class="fas fa-layer-group"></i> Todos los Usuarios
                                </a>
                                <a href="<?php echo e(route('roles.index', ['filter' => 'admins'] + request()->except('filter'))); ?>" 
                                   class="btn btn-outline-danger <?php echo e(request('filter') == 'admins' ? 'active' : ''); ?>">
                                    <i class="fas fa-user-shield"></i> Administradores
                                </a>
                                <a href="<?php echo e(route('roles.index', ['filter' => 'clientes'] + request()->except('filter'))); ?>" 
                                   class="btn btn-outline-success <?php echo e(request('filter') == 'clientes' ? 'active' : ''); ?>">
                                    <i class="fas fa-users"></i> Clientes
                                </a>
                                <a href="<?php echo e(route('roles.index', ['filter' => 'repartidores'] + request()->except('filter'))); ?>" 
                                   class="btn btn-outline-warning <?php echo e(request('filter') == 'repartidores' ? 'active' : ''); ?>">
                                    <i class="fas fa-motorcycle"></i> Repartidores
                                </a>
                                <a href="<?php echo e(route('roles.index', ['filter' => 'custom'] + request()->except('filter'))); ?>" 
                                   class="btn btn-outline-info <?php echo e(request('filter') == 'custom' ? 'active' : ''); ?>">
                                    <i class="fas fa-cogs"></i> Roles Personalizados
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Indicador de filtro activo -->
                <?php if(request('filter') && request('filter') != 'all'): ?>
                    <div class="alert alert-secondary py-2">
                        <small>
                            <i class="fas fa-filter"></i> 
                            <strong>Filtro activo:</strong> 
                            <?php switch(request('filter')):
                                case ('admins'): ?>
                                    Mostrando solo <strong>Administradores</strong>
                                    <?php break; ?>
                                <?php case ('clientes'): ?>
                                    Mostrando solo <strong>Clientes</strong>
                                    <?php break; ?>
                                <?php case ('repartidores'): ?>
                                    Mostrando solo <strong>Repartidores</strong>
                                    <?php break; ?>
                                <?php case ('custom'): ?>
                                    Mostrando solo <strong>Roles Personalizados</strong>
                                    <?php break; ?>
                            <?php endswitch; ?>
                            <a href="<?php echo e(route('roles.index', request()->except('filter'))); ?>" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="fas fa-times"></i> Quitar filtro
                            </a>
                        </small>
                    </div>
                <?php endif; ?>
                
                <!-- Tabla de usuarios -->
                <?php if($usuarios->count() > 0): ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol Actual</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary">#<?php echo e($usuario->idUsuario); ?></span>
                                </td> 
                                <td>
                                    <strong><?php echo e($usuario->nombre); ?></strong>
                                </td>  
                                <td><?php echo e($usuario->email); ?></td>
                                <td>
                                    <?php if($usuario->rol): ?>
                                        <span class="badge 
                                            <?php if($usuario->idRol == 1): ?> bg-danger
                                            <?php elseif($usuario->idRol == 2): ?> bg-success
                                            <?php elseif($usuario->idRol == 3): ?> bg-warning
                                            <?php else: ?> bg-info <?php endif; ?>">
                                            <?php echo e($usuario->rol->nombreRol); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin rol</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-success">Activo</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Botón Cambiar Rol -->
                                        <button type="button" class="btn btn-info btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalCambiarRol"
                                                data-id="<?php echo e($usuario->idUsuario); ?>"
                                                data-nombre="<?php echo e($usuario->nombre); ?>"
                                                data-email="<?php echo e($usuario->email); ?>"
                                                data-rolactual="<?php echo e($usuario->idRol); ?>">
                                            <i class="fa-solid fa-arrows-rotate"></i> Cambiar Rol
                                        </button>
                                        
                                        <!-- Botón Editar -->
                                        <button type="button" class="btn btn-success btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarUsuario"
                                                data-id="<?php echo e($usuario->idUsuario); ?>"
                                                data-nombre="<?php echo e($usuario->nombre); ?>"
                                                data-email="<?php echo e($usuario->email); ?>"
                                                data-idrol="<?php echo e($usuario->idRol); ?>">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </button>
                                        
                                        <!-- Botón Eliminar -->
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEliminarUsuario"
                                                data-id="<?php echo e($usuario->idUsuario); ?>"
                                                data-nombre="<?php echo e($usuario->nombre); ?>">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                <!-- Paginación -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li class="page-item <?php echo e($usuarios->onFirstPage() ? 'disabled' : ''); ?>">
                            <a class="page-link" 
                               href="<?php echo e($usuarios->previousPageUrl()); ?><?php echo e(request('search') ? '&search=' . request('search') : ''); ?><?php echo e(request('filter') ? '&filter=' . request('filter') : ''); ?>">
                                Atrás
                            </a>
                        </li>

                        <?php for($i = 1; $i <= $usuarios->lastPage(); $i++): ?>
                            <li class="page-item <?php echo e($usuarios->currentPage() == $i ? 'active' : ''); ?>">
                                <a class="page-link" 
                                   href="<?php echo e($usuarios->url($i)); ?><?php echo e(request('search') ? '&search=' . request('search') : ''); ?><?php echo e(request('filter') ? '&filter=' . request('filter') : ''); ?>">
                                    <?php echo e($i); ?>

                                </a>
                            </li>
                        <?php endfor; ?>
                            
                        <li class="page-item <?php echo e(!$usuarios->hasMorePages() ? 'disabled' : ''); ?>">
                            <a class="page-link" 
                               href="<?php echo e($usuarios->nextPageUrl()); ?><?php echo e(request('search') ? '&search=' . request('search') : ''); ?><?php echo e(request('filter') ? '&filter=' . request('filter') : ''); ?>">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="text-muted mt-2">
                    Mostrando <?php echo e($usuarios->firstItem()); ?> a <?php echo e($usuarios->lastItem()); ?> de <?php echo e($usuarios->total()); ?> usuarios
                    <?php if(request('filter') && request('filter') != 'all'): ?>
                        (filtrado por <?php echo e(request('filter')); ?>)
                    <?php endif; ?>
                </div>

                <?php else: ?>
                <div class="alert alert-info text-center mt-3">
                    <i class="fas fa-info-circle"></i> 
                    <?php if(request('search')): ?>
                        No se encontraron usuarios con "<?php echo e(request('search')); ?>"
                    <?php elseif(request('filter')): ?>
                        No hay usuarios del tipo seleccionado.
                    <?php else: ?>
                        No hay usuarios registrados.
                    <?php endif; ?>
                </div>
                <?php endif; ?>
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
                <form action="<?php echo e(route('usuarios.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
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
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($rol->idRol); ?>"><?php echo e($rol->nombreRol); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <form action="<?php echo e(route('usuarios.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
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

    <!-- Modal para Cambiar Rol -->
    <div class="modal fade" id="modalCambiarRol" tabindex="-1" aria-labelledby="modalCambiarRolLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalCambiarRolLabel">Cambiar Rol de Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formCambiarRol" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle"></i> 
                            Cambie el rol del usuario seleccionado.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Usuario:</label>
                            <p class="form-control-plaintext">
                                <strong id="nombreUsuarioCambiar"></strong><br>
                                <small class="text-muted" id="emailUsuarioCambiar"></small>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rol Actual:</label>
                            <p class="form-control-plaintext">
                                <span id="rolActualUsuario" class="badge bg-info"></span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label for="nuevo_idRol" class="form-label">Nuevo Rol *</label>
                            <select class="form-select" id="nuevo_idRol" name="idRol" required>
                                <option value="">Seleccione un rol</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($rol->idRol); ?>"><?php echo e($rol->nombreRol); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cambiar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>  

    <!-- Modal para Editar Usuario -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditarUsuarioLabel">Editar Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarUsuario" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_nombre" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="edit_nombre" name="nombre" required 
                                           maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="edit_password" name="password" 
                                           placeholder="Dejar vacío para mantener la actual" minlength="6">
                                    <div class="form-text">Solo complete si desea cambiar la contraseña</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_idRol" class="form-label">Rol *</label>
                                    <select class="form-select" id="edit_idRol" name="idRol" required>
                                        <option value="">Seleccione un rol</option>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($rol->idRol); ?>"><?php echo e($rol->nombreRol); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
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
                    <h1 class="modal-title fs-5" id="modalEliminarUsuarioLabel">Eliminar Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEliminarUsuario" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
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
                    <h1 class="modal-title fs-5" id="modalRolLabel">Crear Nuevo Rol</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('roles.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
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
                                    <?php $__errorArgs = ['idRol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

           // Configurar modal de cambiar rol
        const modalCambiarRol = document.getElementById('modalCambiarRol');
        if (modalCambiarRol) {
        modalCambiarRol.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const email = button.getAttribute('data-email');
        const rolActual = button.getAttribute('data-rolactual');
        
        // Obtener el nombre del rol actual
        const rolActualNombre = document.querySelector(`[data-rolactual="${rolActual}"]`)?.textContent || 'Desconocido';

        // Usar la ruta específica para cambiar rol
        document.getElementById('formCambiarRol').action = `/usuarios/${id}/cambiar-rol`;
        document.getElementById('nombreUsuarioCambiar').textContent = nombre;
        document.getElementById('emailUsuarioCambiar').textContent = email;
        document.getElementById('rolActualUsuario').textContent = rolActualNombre;
        document.getElementById('nuevo_idRol').value = rolActual; // Seleccionar rol actual por defecto
    });
}

            // Configurar modal de edición de usuario
            const modalEditarUsuario = document.getElementById('modalEditarUsuario');
            if (modalEditarUsuario) {
                modalEditarUsuario.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nombre = button.getAttribute('data-nombre');
                    const email = button.getAttribute('data-email');
                    const idRol = button.getAttribute('data-idrol');

                    document.getElementById('formEditarUsuario').action = `/usuarios/${id}`;
                    document.getElementById('edit_nombre').value = nombre;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_idRol').value = idRol;
                });
            }

            // Configurar modal de eliminación de usuario
            const modalEliminarUsuario = document.getElementById('modalEliminarUsuario');
            if (modalEliminarUsuario) {
                modalEliminarUsuario.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nombre = button.getAttribute('data-nombre');

                    document.getElementById('formEliminarUsuario').action = `/usuarios/${id}`;
                    document.getElementById('nombreUsuarioEliminar').textContent = nombre;
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
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('welcome', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RematesPaisaV2\resources\views/roles.blade.php ENDPATH**/ ?>