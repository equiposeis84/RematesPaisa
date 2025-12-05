<?php $__env->startSection('title', 'Productos'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Modulo Productos</h3>
                
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
                
                <hr>

                <!-- Formulario de búsqueda -->
                <form name="productos" action="<?php echo e(url('/productos')); ?>" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botón para abrir modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto" id="btnNuevoProducto">
                            <i class="fa-solid fa-plus"></i> Nuevo
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por nombre, categoría o ID" 
                                       name="search"
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="<?php echo e(url('/productos')); ?>" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla productos -->
                <?php if($datos->count() > 0): ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Producto</th>
                            <th>Nombre</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>NIT</th>
                            <th>Proveedor</th>
                            <th>Precio Unitario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $datos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item->idProductos); ?></td> 
                                <td><?php echo e($item->nombreProducto); ?></td>  
                                <td><?php echo e($item->entradaProducto); ?></td>
                                <td><?php echo e($item->salidaProducto); ?></td>  
                                <td><?php echo e($item->entradaProducto - $item->salidaProducto); ?></td>
                                <td><?php echo e($item->categoriaProducto); ?></td>
                                <td><?php echo e($item->NITProveedores); ?></td>
                                <td><?php echo e($item->proveedor->nombreProveedor ?? 'No encontrado'); ?></td>
                                <td>$<?php echo e(number_format($item->precioUnitario, 2)); ?></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarProducto"
                                            data-id="<?php echo e($item->idProductos); ?>"
                                            data-nombre="<?php echo e($item->nombreProducto); ?>"
                                            data-entrada="<?php echo e($item->entradaProducto); ?>"
                                            data-salida="<?php echo e($item->salidaProducto); ?>"
                                            data-categoria="<?php echo e($item->categoriaProducto); ?>"
                                            data-proveedor="<?php echo e($item->NITProveedores); ?>"
                                            data-precio="<?php echo e($item->precioUnitario); ?>">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarProducto"
                                            data-id="<?php echo e($item->idProductos); ?>">
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
                        No se encontraron productos con "<?php echo e(request('search')); ?>"
                    <?php else: ?>
                        No hay productos registrados.
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div> <!-- Fin del container -->

    <!-- Modal para Nuevo Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalProductoLabel">Nuevo Producto</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('productos.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <!-- CAMPO PARA ID PRODUCTO (AUTOGENERADO) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idProductos" class="form-label">ID Producto *</label>
                                    <input type="text" class="form-control" id="idProductos" name="idProductos" value="<?php echo e($nextId); ?>" required readonly>
                                    <div class="form-text">ID generado automáticamente</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoriaProducto" class="form-label">Categoría *</label>
                                    <select class="form-select" id="categoriaProducto" name="categoriaProducto" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Electrónicos">Electrónicos</option>
                                        <option value="Ropa">Ropa</option>
                                        <option value="Hogar">Hogar</option>
                                        <option value="Deportes">Deportes</option>
                                        <option value="Juguetes">Juguetes</option>
                                        <option value="Alimentos">Alimentos</option>
                                        <option value="Bebidas">Bebidas</option>
                                        <option value="Limpieza">Limpieza</option>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Otros">Otros</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO ID PRODUCTO -->
                    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombreProducto" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="entradaProducto" class="form-label">Entrada *</label>
                                    <input type="number" class="form-control" id="entradaProducto" name="entradaProducto" required min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="salidaProducto" class="form-label">Salida *</label>
                                    <input type="number" class="form-control" id="salidaProducto" name="salidaProducto" required min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="NITProveedores" class="form-label">NIT Proveedor *</label>
                                    <select class="form-select" id="NITProveedores" name="NITProveedores" required>
                                        <option value="">Seleccionar proveedor...</option>
                                        <?php $__currentLoopData = $proveedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proveedor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($proveedor->NITProveedores); ?>">
                                                <?php echo e($proveedor->NITProveedores); ?> - <?php echo e($proveedor->nombreProveedor); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="precioUnitario" class="form-label">Precio Unitario *</label>
                                    <input type="number" step="0.01" class="form-control" id="precioUnitario" name="precioUnitario" required min="0" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Producto -->
    <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditarProductoLabel">Editar Producto</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarProducto" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body">
                        <!-- CAMPO PARA ID PRODUCTO (solo lectura) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_idProductos" class="form-label">ID Producto *</label>
                                    <input type="text" class="form-control" id="edit_idProductos" name="idProductos" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_categoriaProducto" class="form-label">Categoría *</label>
                                    <select class="form-select" id="edit_categoriaProducto" name="categoriaProducto" required>
                                        <option value="Electrónicos">Electrónicos</option>
                                        <option value="Ropa">Ropa</option>
                                        <option value="Hogar">Hogar</option>
                                        <option value="Deportes">Deportes</option>
                                        <option value="Juguetes">Juguetes</option>
                                        <option value="Alimentos">Alimentos</option>
                                        <option value="Bebidas">Bebidas</option>
                                        <option value="Limpieza">Limpieza</option>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Otros">Otros</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO ID PRODUCTO -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_nombreProducto" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="edit_nombreProducto" name="nombreProducto" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_entradaProducto" class="form-label">Entrada *</label>
                                    <input type="number" class="form-control" id="edit_entradaProducto" name="entradaProducto" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_salidaProducto" class="form-label">Salida *</label>
                                    <input type="number" class="form-control" id="edit_salidaProducto" name="salidaProducto" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_NITProveedores" class="form-label">NIT Proveedor *</label>
                                    <select class="form-select" id="edit_NITProveedores" name="NITProveedores" required>
                                        <option value="">Seleccionar proveedor...</option>
                                        <?php $__currentLoopData = $proveedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proveedor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($proveedor->NITProveedores); ?>">
                                                <?php echo e($proveedor->NITProveedores); ?> - <?php echo e($proveedor->nombreProveedor); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_precioUnitario" class="form-label">Precio Unitario *</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_precioUnitario" name="precioUnitario" required min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Producto -->
    <div class="modal fade" id="modalEliminarProducto" tabindex="-1" aria-labelledby="modalEliminarProductoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEliminarProductoLabel">Eliminar Producto</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEliminarProducto" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este producto?</p>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Auto cerrar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Configurar modal de nuevo producto para autogenerar ID
        const modalNuevo = document.getElementById('modalProducto');
        if (modalNuevo) {
            modalNuevo.addEventListener('hidden.bs.modal', function () {
                document.getElementById('categoriaProducto').value = '';
                document.getElementById('nombreProducto').value = '';
                document.getElementById('entradaProducto').value = '0';
                document.getElementById('salidaProducto').value = '0';
                document.getElementById('NITProveedores').value = '';
                document.getElementById('precioUnitario').value = '0';
            });
        }

        // Configurar modal de edición
        const modalEditar = document.getElementById('modalEditarProducto');
        if (modalEditar) {
            modalEditar.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const entrada = button.getAttribute('data-entrada');
                const salida = button.getAttribute('data-salida');
                const categoria = button.getAttribute('data-categoria');
                const proveedor = button.getAttribute('data-proveedor');
                const precio = button.getAttribute('data-precio');

                document.getElementById('formEditarProducto').action = `/productos/${id}`;
                document.getElementById('edit_idProductos').value = id;
                document.getElementById('edit_nombreProducto').value = nombre;
                document.getElementById('edit_entradaProducto').value = entrada;
                document.getElementById('edit_salidaProducto').value = salida;
                document.getElementById('edit_categoriaProducto').value = categoria;
                document.getElementById('edit_NITProveedores').value = proveedor;
                document.getElementById('edit_precioUnitario').value = precio;
            });
        }

        // Configurar modal de eliminación
        const modalEliminar = document.getElementById('modalEliminarProducto');
        if (modalEliminar) {
            modalEliminar.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                document.getElementById('formEliminarProducto').action = `/productos/${id}`;
            });
        }
    }); 
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('VistasAdmin.welcome', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RematesPaisaV2\resources\views/VistasAdmin/productos.blade.php ENDPATH**/ ?>