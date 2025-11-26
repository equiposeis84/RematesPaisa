<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Inventrio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Módulo Productos</h3>
                
                <!-- Mostrar mensajes -->
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
                
                <hr>

                <!-- Formulario de búsqueda -->
                <form action="{{ url('/productos') }}" method="GET">
                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                            <i class="fa-solid fa-plus"></i> Nuevo Producto
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por nombre o categoría" 
                                       name="search"
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="{{ url('/productos') }}" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla productos -->
                @if(isset($productos) && $productos->count() > 0)
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Precio Unitario</th>
                            <th>Proveedor ID</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr>
                                <td>{{ $producto->idProductos }}</td> 
                                <td>{{ $producto->nombreProducto }}</td>
                                <td>{{ $producto->entradaProducto }}</td>
                                <td>{{ $producto->salidaProducto }}</td>
                                <td>
                                    @php
                                        $stock = $producto->entradaProducto - $producto->salidaProducto;
                                    @endphp
                                    <span class="badge {{ $stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $stock }}
                                    </span>
                                </td>
                                <td>{{ $producto->categoriaProducto }}</td>
                                <td>${{ number_format($producto->precioUnitario, 2) }}</td>
                                <td>{{ $producto->idProveedores ?? 'N/A' }}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarProducto"
                                            data-id="{{ $producto->idProductos }}"
                                            data-nombre="{{ $producto->nombreProducto }}"
                                            data-entrada="{{ $producto->entradaProducto }}"
                                            data-salida="{{ $producto->salidaProducto }}"
                                            data-categoria="{{ $producto->categoriaProducto }}"
                                            data-precio="{{ $producto->precioUnitario }}"
                                            data-proveedor="{{ $producto->idProveedores }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                    <form action="{{ route('productos.destroy', $producto->idProductos) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li class="page-item {{ $productos->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $productos->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                Atrás
                            </a>
                        </li>

                        @for ($i = 1; $i <= $productos->lastPage(); $i++)
                            <li class="page-item {{ $productos->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" 
                                   href="{{ $productos->url($i) }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                            
                        <li class="page-item {{ !$productos->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $productos->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="text-muted mt-2">
                    Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }} registros
                </div>

                @else
                <div class="alert alert-info text-center mt-3">
                    <i class="fas fa-info-circle"></i> 
                    @if(request('search'))
                        No se encontraron productos con "{{ request('search') }}"
                    @else
                        No hay productos registrados.
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalProductoLabel">Nuevo Producto</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('productos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombreProductos" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombreProductos" name="nombreProducto" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="entradaProductos" class="form-label">Cantidad Entrada *</label>
                                    <input type="number" class="form-control" id="entradaProductos" name="entradaProducto" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="salidaProductos" class="form-label">Cantidad Salida</label>
                                    <input type="number" class="form-control" id="salidaProductos" name="salidaProducto">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoriaProductos" class="form-label">Categoría *</label>
                                    <select class="form-select" id="categoriaProductos" name="categoriaProducto" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Electrónicos">Electrónicos</option>
                                        <option value="Ropa">Ropa</option>
                                        <option value="Hogar">Hogar</option>
                                        <option value="Deportes">Deportes</option>
                                        <option value="Alimentos">Alimentos</option>
                                        <option value="Bebidas">Bebidas</option>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Otros">Otros</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="predoUnitario" class="form-label">Precio Unitario *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="predoUnitario" name="precioUnitario" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idProveedores" class="form-label">ID Proveedor</label>
                                    <input type="number" class="form-control" id="idProveedores" name="idProveedores" min="0">
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

    <!-- Modal Editar Producto -->
    <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditarProductoLabel">Editar Producto</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarProducto" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_nombreProductos" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="edit_nombreProductos" name="nombreProducto" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_entradaProductos" class="form-label">Cantidad Entrada *</label>
                                    <input type="number" class="form-control" id="edit_entradaProductos" name="entradaProducto" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_salidaProductos" class="form-label">Cantidad Salida</label>
                                    <input type="number" class="form-control" id="edit_salidaProductos" name="salidaProducto">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_categoriaProductos" class="form-label">Categoría *</label>
                                    <select class="form-select" id="edit_categoriaProductos" name="categoriaProducto" required>
                                        <option value="Electrónicos">Electrónicos</option>
                                        <option value="Ropa">Ropa</option>
                                        <option value="Hogar">Hogar</option>
                                        <option value="Deportes">Deportes</option>
                                        <option value="Alimentos">Alimentos</option>
                                        <option value="Bebidas">Bebidas</option>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Otros">Otros</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_predoUnitario" class="form-label">Precio Unitario *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit_predoUnitario" name="precioUnitario" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_idProveedores" class="form-label">ID Proveedor</label>
                                    <input type="number" class="form-control" id="edit_idProveedores" name="idProveedores" min="0">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto cerrar alertas
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

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
                    const precio = button.getAttribute('data-precio');
                    const proveedor = button.getAttribute('data-proveedor');

                    document.getElementById('formEditarProducto').action = `/productos/${id}`;
                    document.getElementById('edit_nombreProductos').value = nombre;
                    document.getElementById('edit_entradaProductos').value = entrada;
                    document.getElementById('edit_salidaProductos').value = salida;
                    document.getElementById('edit_categoriaProductos').value = categoria;
                    document.getElementById('edit_predoUnitario').value = precio;
                    document.getElementById('edit_idProveedores').value = proveedor || '';
                });
            }

            // Limpiar formulario nuevo
            const modalNuevo = document.getElementById('modalProducto');
            if (modalNuevo) {
                modalNuevo.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('nombreProductos').value = '';
                    document.getElementById('entradaProductos').value = '';
                    document.getElementById('salidaProductos').value = '';
                    document.getElementById('categoriaProductos').value = '';
                    document.getElementById('predoUnitario').value = '';
                    document.getElementById('idProveedores').value = '';
                });
            }
        });
    </script>
</body>
</html>