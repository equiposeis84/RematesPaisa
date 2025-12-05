<?php $__env->startSection('title', 'Productos del Pedido'); ?>
<?php $__env->startSection('content'); ?>
<header class="content-header">
    <h2>Ayuda y Contacto</h2>
</header>

<section class="content-body">
    <p>Aquí puedes encontrar información de contacto y asistencia.</p>
</section>

<div class="content-body">
    <h3>Contáctanos</h3>
    <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos:</p>
    <ul>
        <div class="card icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">...</svg>
            <li>Email: soporte@remateselpaisa.com</li>
        </div>
        <div class="card icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">...</svg>
            <li>Teléfono: +57 310 8892977 / +57 322 3191394</li>
        </div>
    </ul>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('VistasAdmin.welcome', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RematesPaisaV2\resources\views/VistasAdmin/AyudaContacto.blade.php ENDPATH**/ ?>