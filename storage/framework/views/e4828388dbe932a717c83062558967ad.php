<?php $__env->startSection('title', 'Usuarios'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Modulo Usuarios</h3>
                
                <!-- Mostrar mensajes de éxito -->
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
                
                <!-- Mostrar errores de validación -->
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <hr>

                <!-- Formulario de búsqueda -->
                <form name="clientes" action="<?php echo e(url('/clientes')); ?>" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botón para abrir modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCliente">
                            <i class="fa-solid fa-plus"></i> Nuevo
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por nombre, empresa, documento o email" 
                                       name="search"
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="<?php echo e(url('/clientes')); ?>" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla clientes -->
                <?php if($datos->count() > 0): ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Usuario</th>
                            <th>Empresa</th>
                            <th>Tipo Doc.</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $datos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item->idCliente); ?></td> 
                                <td><?php echo e($item->NombreEmpresa); ?></td>
                                <td><?php echo e($item->tipoDocumentoCliente); ?></td>  
                                <td><?php echo e($item->nombreCliente); ?></td>
                                <td><?php echo e($item->apellidoCliente); ?></td>  
                                <td><?php echo e($item->direccionCliente); ?></td>
                                <td><?php echo e($item->telefonoCliente); ?></td>
                                <td><?php echo e($item->emailCliente); ?></td>
                                <td>
                                    <?php if($item->idRol == 1): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php elseif($item->idRol == 2): ?>
                                        <span class="badge bg-success">Cliente</span>
                                    <?php elseif($item->idRol == 3): ?>
                                        <span class="badge bg-warning">Repartidor</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e($item->idRol); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarCliente"
                                            data-id="<?php echo e($item->idCliente); ?>"
                                            data-empresa="<?php echo e($item->NombreEmpresa); ?>"
                                            data-tipodoc="<?php echo e($item->tipoDocumentoCliente); ?>"
                                            data-nombre="<?php echo e($item->nombreCliente); ?>"
                                            data-apellido="<?php echo e($item->apellidoCliente); ?>"
                                            data-direccion="<?php echo e($item->direccionCliente); ?>"
                                            data-telefono="<?php echo e($item->telefonoCliente); ?>"
                                            data-email="<?php echo e($item->emailCliente); ?>">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDeleteCliente"
                                            data-id="<?php echo e($item->idCliente); ?>"
                                            data-empresa="<?php echo e($item->NombreEmpresa); ?>">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                <!-- Paginación -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <!-- Botón anterior -->
                        <li class="page-item <?php echo e($datos->onFirstPage() ? 'disabled' : ''); ?>">
                            <a class="page-link" 
                               href="<?php echo e($datos->previousPageUrl()); ?><?php echo e(request('search') ? '&search=' . request('search') : ''); ?>">
                                Atrás
                            </a>
                        </li>

                        <!-- Números de página -->
                        <?php for($i = 1; $i <= $datos->lastPage(); $i++): ?>
                            <li class="page-item <?php echo e($datos->currentPage() == $i ? 'active' : ''); ?>">
                                <a class="page-link" 
                                   href="<?php echo e($datos->url($i)); ?><?php echo e(request('search') ? '&search=' . request('search') : ''); ?>">
                                    <?php echo e($i); ?>

                                </a>
                            </li>
                        <?php endfor; ?>
                            
                        <!-- Botón Siguiente -->
                        <li class="page-item <?php echo e(!$datos->hasMorePages() ? 'disabled' : ''); ?>">
                            <a class="page-link" 
                               href="<?php echo e($datos->nextPageUrl()); ?><?php echo e(request('search') ? '&search=' . request('search') : ''); ?>">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Información de registros -->
                <div class="text-muted mt-2">
                    Mostrando <?php echo e($datos->firstItem()); ?> a <?php echo e($datos->lastItem()); ?> de <?php echo e($datos->total()); ?> registros
                </div>

                <?php else: ?>
                <div class="alert alert-info text-center mt-3">
                    <i class="fas fa-info-circle"></i> 
                    <?php if(request('search')): ?>
                        No se encontraron usuarios con "<?php echo e(request('search')); ?>"
                    <?php else: ?>
                        No hay usuarios registrados.
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div> <!-- Fin del container -->

    <!-- Modal para Nuevo Cliente -->
    <div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalClienteLabel">Nuevo Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('clientes.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <!-- CAMPOS PRINCIPALES -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="NombreEmpresa" class="form-label">Nombre Empresa *</label>
                                    <input type="text" class="form-control" id="NombreEmpresa" name="NombreEmpresa" 
                                           value="<?php echo e(old('NombreEmpresa')); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idCliente" class="form-label">ID Usuario *</label>
                                    <input type="text" class="form-control" id="idCliente" name="idCliente" 
                                           value="<?php echo e(old('idCliente')); ?>" required 
                                           placeholder="Ej: CL001">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipoDocumentoCliente" class="form-label">Tipo Documento *</label>
                                    <select class="form-select" id="tipoDocumentoCliente" name="tipoDocumentoCliente" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="CC">Cédula (CC)</option>
                                        <option value="CE">Cédula Extranjería (CE)</option>
                                        <option value="PAS">Pasaporte (PAS)</option>
                                        <option value="RUC">RUC</option>
                                        <option value="DNI">DNI</option>
                                        <option value="NIT">NIT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefonoCliente" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="telefonoCliente" name="telefonoCliente" 
                                           value="<?php echo e(old('telefonoCliente')); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombreCliente" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="nombreCliente" name="nombreCliente" 
                                           value="<?php echo e(old('nombreCliente')); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellidoCliente" class="form-label">Apellido *</label>
                                    <input type="text" class="form-control" id="apellidoCliente" name="apellidoCliente" 
                                           value="<?php echo e(old('apellidoCliente')); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="emailCliente" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="emailCliente" name="emailCliente" 
                                           value="<?php echo e(old('emailCliente')); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="direccionCliente" class="form-label">Dirección *</label>
                                    <input type="text" class="form-control" id="direccionCliente" name="direccionCliente" 
                                           value="<?php echo e(old('direccionCliente')); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="passwordUsuario" class="form-label">Contraseña *</label>
                                    <input type="password" class="form-control" id="passwordUsuario" name="passwordUsuario" 
                                           required minlength="6">
                                    <div class="form-text">Mínimo 6 caracteres</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Cliente -->
    <div class="modal fade" id="modalDeleteCliente" tabindex="-1" aria-labelledby="modalDeleteClienteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalDeleteClienteLabel">Eliminar Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEliminarCliente" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar al usuario <strong><span id="nombreClienteEliminar"></span></strong>?</p>
                        <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Cliente -->
    <div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-labelledby="modalEditarClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditarClienteLabel">Editar Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarCliente" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <!-- CAMPOS PRINCIPALES -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_NombreEmpresa" class="form-label">Nombre Empresa *</label>
                                    <input type="text" class="form-control" id="edit_NombreEmpresa" name="NombreEmpresa" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_idCliente" class="form-label">ID Usuario *</label>
                                    <input type="text" class="form-control" id="edit_idCliente" name="idCliente" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tipoDocumentoCliente" class="form-label">Tipo Documento *</label>
                                    <select class="form-select" id="edit_tipoDocumentoCliente" name="tipoDocumentoCliente" required>
                                        <option value="CC">Cédula (CC)</option>
                                        <option value="CE">Cédula Extranjería (CE)</option>
                                        <option value="PAS">Pasaporte (PAS)</option>
                                        <option value="RUC">RUC</option>
                                        <option value="DNI">DNI</option>
                                        <option value="NIT">NIT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_telefonoCliente" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="edit_telefonoCliente" name="telefonoCliente" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nombreCliente" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="edit_nombreCliente" name="nombreCliente" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_apellidoCliente" class="form-label">Apellido *</label>
                                    <input type="text" class="form-control" id="edit_apellidoCliente" name="apellidoCliente" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_emailCliente" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="edit_emailCliente" name="emailCliente" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_direccionCliente" class="form-label">Dirección *</label>
                                    <input type="text" class="form-control" id="edit_direccionCliente" name="direccionCliente" required>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Configurar modal de edición
            const modalEditar = document.getElementById('modalEditarCliente');
            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const empresa = button.getAttribute('data-empresa');
                    const tipoDoc = button.getAttribute('data-tipodoc');
                    const nombre = button.getAttribute('data-nombre');
                    const apellido = button.getAttribute('data-apellido');
                    const direccion = button.getAttribute('data-direccion');
                    const telefono = button.getAttribute('data-telefono');
                    const email = button.getAttribute('data-email');

                    document.getElementById('formEditarCliente').action = `/clientes/${id}`;
                    document.getElementById('edit_idCliente').value = id;
                    document.getElementById('edit_NombreEmpresa').value = empresa;
                    document.getElementById('edit_tipoDocumentoCliente').value = tipoDoc;
                    document.getElementById('edit_nombreCliente').value = nombre;
                    document.getElementById('edit_apellidoCliente').value = apellido;
                    document.getElementById('edit_direccionCliente').value = direccion;
                    document.getElementById('edit_telefonoCliente').value = telefono;
                    document.getElementById('edit_emailCliente').value = email;
                });
            }

            // Limpiar formulario de nuevo cliente cuando se cierra el modal
            const modalNuevo = document.getElementById('modalCliente');
            if (modalNuevo) {
                modalNuevo.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('NombreEmpresa').value = '';
                    document.getElementById('idCliente').value = '';
                    document.getElementById('tipoDocumentoCliente').value = '';
                    document.getElementById('nombreCliente').value = '';
                    document.getElementById('apellidoCliente').value = '';
                    document.getElementById('direccionCliente').value = '';
                    document.getElementById('telefonoCliente').value = '';
                    document.getElementById('emailCliente').value = '';
                    document.getElementById('passwordUsuario').value = '';
                });
            }

            // Configurar modal de eliminación
            const modalDelete = document.getElementById('modalDeleteCliente');
            if (modalDelete) {
                modalDelete.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const empresa = button.getAttribute('data-empresa');
                    
                    // Actualizar el nombre en el modal
                    const nombreSpan = document.getElementById('nombreClienteEliminar');
                    if (nombreSpan && empresa) {
                        nombreSpan.textContent = empresa;
                    }
                    
                    // Actualizar el formulario de eliminación
                    document.getElementById('formEliminarCliente').action = `/clientes/${id}`;
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('VistasAdmin.welcome', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RematesPaisaV2\resources\views/VistasAdmin/clientes.blade.php ENDPATH**/ ?>