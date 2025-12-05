<?php $__env->startSection('title', 'Iniciar Sesión - Remates El Paísa'); ?>

<?php $__env->startSection('content'); ?>
<div class="form-container">
    <h1>Iniciar Sesión</h1>
    
    <form method="POST" action="<?php echo e(route('login.post')); ?>">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email">
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password">
        </div>

        <?php if($errors->any()): ?>
            <div class="error">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <button type="submit" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="btn-icon bi bi-people" viewBox="0 0 16 16"></svg>
            Ingresar
        </button>
    </form>

    <div class="form-links">
        <a href="#">¿Olvidaste tu contraseña?</a>
        <br><br>
        <a href="<?php echo e(route('auth.register')); ?>">¿No tienes cuenta? Regístrate</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.usuario', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RematesPaisaV2\resources\views/Usuarios/IniciarSesion.blade.php ENDPATH**/ ?>