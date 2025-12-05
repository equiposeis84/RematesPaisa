<?php $__env->startSection('title', 'Productos del Pedido'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-sm d-flex justify-content-center mt-5">
    <div class="card">
        <div class="card-body" style="width: 1200px;">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Productos del Pedido #<?php echo e($pedido->idPedidos); ?></h3>
                <a href="<?php echo e(route('pedidos.index')); ?>" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Pedidos
                </a>
            </div>
            
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

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

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Agregar Producto</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('pedidos.productos.store', $pedido->idPedidos)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label for="idProducto" class="form-label">Seleccionar Producto *</label>
                                    <select class="form-select" id="idProducto" name="idProducto" required>
                                        <option value="">Seleccionar producto...</option>
                                        <?php $__currentLoopData = $productosDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($producto->idProductos); ?>" 
                                                    data-precio="<?php echo e($producto->precioUnitario); ?>"
                                                    data-nombre="<?php echo e($producto->nombreProducto); ?>">
                                                <?php echo e($producto->nombreProducto); ?> - $<?php echo e(number_format($producto->precioUnitario, 2)); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio Unitario *</label>
                                    <input type="number" step="0.01" class="form-control" id="precio" readonly required min="0">
                                </div>
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label">Cantidad *</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" required min="1" value="1">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-plus"></i> Agregar Producto
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Resumen del Pedido</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>ID Pedido:</strong> <?php echo e($pedido->idPedidos); ?></p>
                            <p><strong>Cliente:</strong> <?php echo e($pedido->idCliente); ?></p>
                            <p><strong>Fecha:</strong> <?php echo e($pedido->fechaPedido); ?></p>
                            <p><strong>Estado:</strong> 
                                <span class="badge 
                                    <?php if($pedido->estadoPedido == 'Pendiente'): ?> bg-warning
                                    <?php elseif($pedido->estadoPedido == 'Enviado'): ?> bg-info
                                    <?php elseif($pedido->estadoPedido == 'Entregado'): ?> bg-success
                                    <?php elseif($pedido->estadoPedido == 'Cancelado'): ?> bg-danger
                                    <?php else: ?> bg-secondary <?php endif; ?>">
                                    <?php echo e($pedido->estadoPedido); ?>

                                </span>
                            </p>
                            <hr>
                            <p><strong>Subtotal:</strong> $<?php echo e(number_format($pedido->valorPedido, 2)); ?></p>
                            <p><strong>IVA (19%):</strong> $<?php echo e(number_format($pedido->ivaPedido, 2)); ?></p>
                            <p><strong>Total:</strong> $<?php echo e(number_format($pedido->totalPedido, 2)); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Productos en el Pedido</h5>
                </div>
                <div class="card-body">
                    <?php if($productos->count() > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Producto</th>
                                <th>Nombre</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($producto->idProductos); ?></td>
                                <td><?php echo e($producto->producto->nombreProducto ?? 'Producto no encontrado'); ?></td>
                                <td>$<?php echo e(number_format($producto->valorUnitarioDetalleProducto, 2)); ?></td>
                                <td><?php echo e($producto->cantidadDetalleProducto); ?></td>
                                <td>$<?php echo e(number_format($producto->totalPagarDetalleProducto, 2)); ?></td>
                                <td>$<?php echo e(number_format($producto->ivaDetalleProducto, 2)); ?></td>
                                <td>$<?php echo e(number_format($producto->totalDetalleProducto, 2)); ?></td>
                                <td>
                                    <form action="<?php echo e(route('pedidos.productos.destroy', ['idPedido' => $pedido->idPedidos, 'idProducto' => $producto->idProductos])); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este producto del pedido?')">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay productos en este pedido.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productoSelect = document.getElementById('idProducto');
    const precioInput = document.getElementById('precio');
    
    productoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value !== '') {
            precioInput.value = selectedOption.getAttribute('data-precio');
        } else {
            precioInput.value = '';
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('welcome', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RematesPaisaV2\resources\views/productos-pedido.blade.php ENDPATH**/ ?>