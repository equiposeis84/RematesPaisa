@extends('layouts.usuario')

@section('title', 'Catálogo de Productos - Remates El Paísa')

@section('content')
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
@endsection