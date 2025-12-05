<?php $__env->startSection('title', 'Proveedores'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Modulo Proveedores</h3>
                
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
                <form name="proveedores" action="<?php echo e(url('/proveedores')); ?>" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botón para abrir modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProveedor" id="btnNuevoProveedor">
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
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="<?php echo e(url('/proveedores')); ?>" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla proveedores -->
                <?php if($datos->count() > 0): ?>
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
                        <?php $__currentLoopData = $datos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item->NITProveedores); ?></td> 
                                <td><?php echo e($item->nombreProveedor); ?></td>  
                                <td><?php echo e($item->telefonoProveedor); ?></td>
                                <td><?php echo e($item->correoProveedor); ?></td>  
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarProveedor"
                                            data-id="<?php echo e($item->NITProveedores); ?>"
                                            data-nombre="<?php echo e($item->nombreProveedor); ?>"
                                            data-telefono="<?php echo e($item->telefonoProveedor); ?>"
                                            data-correo="<?php echo e($item->correoProveedor); ?>">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                 <button type="button" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarProveedor"
                                            data-id="<?php echo e($item->NITProveedores); ?>"
                                            data-nombre="<?php echo e($item->nombreProveedor); ?>">
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
                        No se encontraron proveedores con "<?php echo e(request('search')); ?>"
                    <?php else: ?>
                        No hay proveedores registrados.
                    <?php endif; ?>
                </div>
                <?php endif; ?>
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
                <form action="<?php echo e(route('proveedores.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <!-- CAMPO PARA NIT PROVEEDOR (AUTO GENERADO) -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="NITProveedores" class="form-label">NIT Proveedor *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="NITProveedores" name="NITProveedores" 
                                               value="<?php echo e(old('NITProveedores')); ?>" required readonly
                                               placeholder="Se generará automáticamente">
                                        <button type="button" class="btn btn-outline-secondary" id="btnGenerarNIT">
                                            <i class="fas fa-sync-alt"></i> Regenerar
                                        </button>
                                    </div>
                                    <div class="form-text">NIT generado automáticamente. Puedes usar el botón para regenerar si es necesario.</div>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO NIT PROVEEDOR -->
                    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombreProveedor" class="form-label">Nombre del Proveedor *</label>
                                    <input type="text" class="form-control" id="nombreProveedor" name="nombreProveedor" 
                                           value="<?php echo e(old('nombreProveedor')); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefonoProveedor" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="telefonoProveedor" name="telefonoProveedor" 
                                           value="<?php echo e(old('telefonoProveedor')); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="correoProveedor" class="form-label">Correo Electrónico *</label>
                                    <input type="email" class="form-control" id="correoProveedor" name="correoProveedor" 
                                           value="<?php echo e(old('correoProveedor')); ?>" required>
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
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
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
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

            // Función para obtener el siguiente NIT
            function obtenerSiguienteNIT() {
                fetch('<?php echo e(route("proveedores.getSiguienteNIT")); ?>')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('NITProveedores').value = data.siguienteNIT;
                    })
                    .catch(error => {
                        console.error('Error al obtener el NIT:', error);
                        // Si hay error, usar un cálculo simple
                        const totalProveedores = <?php echo e($datos->total()); ?>;
                        document.getElementById('NITProveedores').value = totalProveedores + 1;
                    });
            }

            // Configurar modal de nuevo proveedor
            const modalProveedor = document.getElementById('modalProveedor');
            if (modalProveedor) {
                // Cuando se abre el modal, obtener el siguiente NIT
                modalProveedor.addEventListener('show.bs.modal', function () {
                    obtenerSiguienteNIT();
                    
                    // Limpiar otros campos
                    document.getElementById('nombreProveedor').value = '';
                    document.getElementById('telefonoProveedor').value = '';
                    document.getElementById('correoProveedor').value = '';
                });
            }

            // Botón para regenerar NIT
            const btnGenerarNIT = document.getElementById('btnGenerarNIT');
            if (btnGenerarNIT) {
                btnGenerarNIT.addEventListener('click', function() {
                    obtenerSiguienteNIT();
                });
            }

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('welcome', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RematesPaisaV2\resources\views/proveedores.blade.php ENDPATH**/ ?>