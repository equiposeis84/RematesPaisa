<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/MainContent.css">
    <link rel="stylesheet" href="/css/SidebarStyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">

    <aside class="sidebar">
            <div class="logo"><h1>Remates El Paísa</h1></div>
            <div class="este">
                <nav class="main-nav">
                    <ul>
                        <li class="sidebar-fixed"><a href="<?php echo e(route('index')); ?>"> <span class="nav-icon"> <i class="fa-solid fa-house"></i> </span>Catálogo </a></li>
                        <li class="sidebar-fixed"><a href="<?php echo e(route('usuario.carrito')); ?>"> <span class="nav-icon"> <i class="fa-solid fa-cart-shopping"></i> </span>Carrito </a></li>
                        <li class="sidebar-fixed"><a href="<?php echo e(route('usuario.pedidos')); ?>"> <span class="nav-icon"> <i class="fa-solid fa-bag-shopping"></i> </span>Pedidos </a></li>
                    </ul>
                </nav>
            </div>
            
            <div class="space">
                <div class="space-item"></div>
            </div>

    <div class="user-section">
        
    <div class="user-info">
        <nav class="secondary-nav">
            <li><p><strong> Conectado como:   Usuario</strong></p></li>
            <li>
                <a href="<?php echo e(route('usuario.ayuda.contacto')); ?>" data-view="">Ayuda y Contacto</a>
            </li>
            <li>
                    <span class="nav-icon">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </span>
                    <span class="nav-text">Iniciar Sesión</span>
                </a>
            </li>
        </nav>
    </div>
    </div>
</aside>

        <!-- CONTENIDO PRINCIPAL-->
        <main class="main-content">
            <section class="content-cards">
                <?php echo $__env->yieldContent('content'); ?>
            </section>
        
    </div>
    <i class="fa-light fa-arrow-right-to-bracket"></i>

<header class="content-header">
    <h2>Catálogo de Productos</h2>
    <div class="filters">
        <div class="search-box">
            <input type="text" placeholder="Buscar productos...">
            <button class="btn">Buscar</button>
        </div>
        <div>
            <select>
                <option>Todas las categorías</option>
                <option>Limpieza</option>
                <option>Ropa</option>
                <option>Cocina</option>
                <option>Accesorios</option>
            </select>
        </div>
    </div>
</header>

<div class="product-grid">
    <!-- Producto 1 -->
    <div class="product-card">
        <div class="product-image">Imagen del producto</div>
        <div class="product-info">
            <div class="product-title">Limpiador Multiusos Floral</div>
            <div class="product-category">Limpieza • Botella de 1L</div>
            <div class="product-price">$12.500</div>
            <div class="product-stock">150 en stock</div>
            <p>Aroma fresco y duradero que perfuma todo tu hogar.</p>
            <div class="product-actions">
                <button class="btn btn-sm">Agregar al carrito</button>
            </div>
        </div>
    </div>
    
    <!-- Más productos... -->
</div>

<div class="pagination">
    <button>&laquo; Anterior</button>
    <button class="active">1</button>
    <button>2</button>
    <button>3</button>
    <button>Siguiente &raquo;</button>
</div>
</main>
</body><?php /**PATH C:\xampp\htdocs\RematesPaisaV2\resources\views/index.blade.php ENDPATH**/ ?>