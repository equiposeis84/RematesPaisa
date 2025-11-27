<<<<<<< HEAD
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modulo Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
=======
@extends('welcome')
@section('title', 'Proveedores')
@section('content')
>>>>>>> 1992225baf11169504a8d35174321996067799e9
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Modulo Proveedores</h3>
                
                <!-- Mostrar mensajes de éxito -->
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
<<<<<<< HEAD
                <form name="Proveedores" action="{{ url('/proveedores') }}" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botón para abrir modal CORREGIDO -->
=======
                <form name="proveedores" action="{{ url('/proveedores') }}" method="GET">
                    <div class="text-end mb-3">
                        <!-- Botón para abrir modal -->
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProveedor">
                            <i class="fa-solid fa-plus"></i> Nuevo
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
<<<<<<< HEAD
                                       placeholder="Buscar por nombre, documento o email" 
=======
                                       placeholder="Buscar por NIT, nombre, email o teléfono" 
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                                       name="search"
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="{{ url('/proveedores') }}" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
<<<<<<< HEAD
                <!-- Tabla Proveedores -->
=======
                <!-- Tabla proveedores -->
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                @if($datos->count() > 0)
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
<<<<<<< HEAD
                            <td>idProveedores</td>
                            <td>tipoDocumentoProveedor</td>
                            <td>nombreProveedor</td>
                            <td>telefonoProveedor</td>
                            <td>correoProveedor</td>
                            <td>Acciones</td>
=======
                            <th>NIT Proveedor</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Acciones</th>
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datos as $item)
                            <tr>
<<<<<<< HEAD
                                <td>{{ $item->idProveedores }}</td> 
                                <td>{{ $item->tipoDocumentoProveedor }}</td>  
                                <td>{{ $item->nombreProveedor }}</td>
                                <td>{{ $item->telefonoProveedor }}</td>  
                                <td>{{ $item->correoProveedor }}</td>
=======
                                <td>{{ $item->NITProveedores }}</td> 
                                <td>{{ $item->nombreProveedor }}</td>  
                                <td>{{ $item->telefonoProveedor }}</td>
                                <td>{{ $item->correoProveedor }}</td>  
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarProveedor"
<<<<<<< HEAD
                                            data-id="{{ $item->idProveedores }}"
                                            data-tipodoc="{{ $item->tipoDocumentoProveedor }}"
                                            data-nombre="{{ $item->nombreProveedor }}"
                                            data-email="{{ $item->correoProveedor }}"
                                            data-telefono="{{ $item->telefonoProveedor }}"
                                            data-direccion="{{ $item->direccionProveedor ?? '' }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                    <form action="{{ route('proveedores.destroy', $item->idProveedores) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este proveedor?')">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </button>
                                    </form>
=======
                                            data-id="{{ $item->NITProveedores }}"
                                            data-nombre="{{ $item->nombreProveedor }}"
                                            data-telefono="{{ $item->telefonoProveedor }}"
                                            data-correo="{{ $item->correoProveedor }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                 <button type="button" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEliminarProveedor"
                                            data-id="{{ $item->NITProveedores }}">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>

                                    
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <!-- Botón anterior -->
                        <li class="page-item {{ $datos->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $datos->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                Atrás
                            </a>
                        </li>

                        <!-- Números de página -->
                        @for ($i = 1; $i <= $datos->lastPage(); $i++)
                            <li class="page-item {{ $datos->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" 
                                   href="{{ $datos->url($i) }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                            
                        <!-- Botón Siguiente -->
                        <li class="page-item {{ !$datos->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $datos->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Información de registros -->
                <div class="text-muted mt-2">
                    Mostrando {{ $datos->firstItem() }} a {{ $datos->lastItem() }} de {{ $datos->total() }} registros
                </div>

                @else
                <div class="alert alert-info text-center mt-3">
                    <i class="fas fa-info-circle"></i> 
                    @if(request('search'))
                        No se encontraron proveedores con "{{ request('search') }}"
                    @else
                        No hay proveedores registrados.
                    @endif
                </div>
                @endif
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
                <form action="{{ route('proveedores.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
<<<<<<< HEAD
                        <!-- CAMPO PARA NÚMERO DE DOCUMENTO -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idProveedor" class="form-label">Número de Documento *</label>
                                    <input type="text" class="form-control" id="idProveedor" name="idProveedor" required 
                                           placeholder="Ej: 123456789">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipoDocumentoProveedor" class="form-label">Tipo Documento *</label>
                                    <select class="form-select" id="tipoDocumentoProveedor" name="tipoDocumentoProveedor" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Cédula">Cédula</option>
                                        <option value="Pasaporte">Pasaporte</option>
                                        <option value="RUC">RUC</option>
                                        <option value="DNI">DNI</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO DOCUMENTO -->
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombreProveedor" class="form-label">Nombre Completo *</label>
=======
                        <!-- CAMPO PARA NIT PROVEEDOR -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="NITProveedores" class="form-label">NIT Proveedor *</label>
                                    <input type="text" class="form-control" id="NITProveedores" name="NITProveedores" required 
                                           placeholder="Ej: 123456789">
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO NIT PROVEEDOR -->
                    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombreProveedor" class="form-label">Nombre del Proveedor *</label>
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                                    <input type="text" class="form-control" id="nombreProveedor" name="nombreProveedor" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
<<<<<<< HEAD
                                    <label for="emailProveedor" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="emailProveedor" name="emailProveedor" required>
=======
                                    <label for="telefonoProveedor" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="telefonoProveedor" name="telefonoProveedor" required>
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
<<<<<<< HEAD
                                    <label for="telefonoProveedor" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefonoProveedor" name="telefonoProveedor">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="direccionProveedor" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccionProveedor" name="direccionProveedor">
=======
                                    <label for="correoProveedor" class="form-label">Correo Electrónico *</label>
                                    <input type="email" class="form-control" id="correoProveedor" name="correoProveedor" required>
>>>>>>> 1992225baf11169504a8d35174321996067799e9
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
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
<<<<<<< HEAD
                        <!-- CAMPO PARA NÚMERO DE DOCUMENTO (solo lectura) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_idProveedor" class="form-label">Número de Documento *</label>
                                    <input type="text" class="form-control" id="edit_idProveedor" name="idProveedor" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tipoDocumentoProveedor" class="form-label">Tipo Documento *</label>
                                    <select class="form-select" id="edit_tipoDocumentoProveedor" name="tipoDocumentoProveedor" required>
                                        <option value="Cédula">Cédula</option>
                                        <option value="Pasaporte">Pasaporte</option>
                                        <option value="RUC">RUC</option>
                                        <option value="DNI">DNI</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO DOCUMENTO -->
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_nombreProveedor" class="form-label">Nombre Completo *</label>
=======
                        <!-- CAMPO PARA NIT PROVEEDOR (solo lectura) -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_NITProveedores" class="form-label">NIT Proveedor *</label>
                                    <input type="text" class="form-control" id="edit_NITProveedores" name="NITProveedores" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- FIN CAMPO NIT PROVEEDOR -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_nombreProveedor" class="form-label">Nombre del Proveedor *</label>
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                                    <input type="text" class="form-control" id="edit_nombreProveedor" name="nombreProveedor" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
<<<<<<< HEAD
                                    <label for="edit_emailProveedor" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="edit_emailProveedor" name="emailProveedor" required>
=======
                                    <label for="edit_telefonoProveedor" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="edit_telefonoProveedor" name="telefonoProveedor" required>
>>>>>>> 1992225baf11169504a8d35174321996067799e9
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
<<<<<<< HEAD
                                    <label for="edit_telefonoProveedor" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="edit_telefonoProveedor" name="telefonoProveedor">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_direccionProveedor" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="edit_direccionProveedor" name="direccionProveedor">
=======
                                    <label for="edit_correoProveedor" class="form-label">Correo Electrónico *</label>
                                    <input type="email" class="form-control" id="edit_correoProveedor" name="correoProveedor" required>
>>>>>>> 1992225baf11169504a8d35174321996067799e9
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
<<<<<<< HEAD

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
=======
    <!-- Modal para Eliminar Proveedor -->
    <div class="modal fade" id="modalEliminarProveedor" tabindex="-1" aria-labelledby="modalEliminarProveedorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEliminarProveedorLabel">Eliminar Proveedor</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEliminarProveedor" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este proveedor?</p>
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
>>>>>>> 1992225baf11169504a8d35174321996067799e9
        // Auto cerrar alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Configurar modal de edición
            const modalEditar = document.getElementById('modalEditarProveedor');
            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
<<<<<<< HEAD
                    const tipoDoc = button.getAttribute('data-tipodoc');
                    const nombre = button.getAttribute('data-nombre');
                    const email = button.getAttribute('data-email');
                    const telefono = button.getAttribute('data-telefono');
                    const direccion = button.getAttribute('data-direccion');

                    // Actualizar el formulario
                    document.getElementById('formEditarProveedor').action = `/proveedores/${id}`;
                    document.getElementById('edit_idProveedor').value = id; 
                    document.getElementById('edit_tipoDocumentoProveedor').value = tipoDoc;
                    document.getElementById('edit_nombreProveedor').value = nombre;
                    document.getElementById('edit_emailProveedor').value = email;
                    document.getElementById('edit_telefonoProveedor').value = telefono || '';
                    document.getElementById('edit_direccionProveedor').value = direccion || '';
                });
            }

            // Limpiar formulario de nuevo proveedor cuando se cierra el modal - CORREGIDO
            const modalNuevo = document.getElementById('modalProveedor');
            if (modalNuevo) {
                modalNuevo.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('idProveedor').value = '';
                    document.getElementById('tipoDocumentoProveedor').value = '';
                    document.getElementById('nombreProveedor').value = '';
                    document.getElementById('emailProveedor').value = '';
                    document.getElementById('telefonoProveedor').value = '';
                    document.getElementById('direccionProveedor').value = '';
                });
            }
        });
    </script>
</body>
</html>
=======
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

            // Limpiar formulario de nuevo proveedor cuando se cierra el modal
            const modalNuevo = document.getElementById('modalProveedor');
            if (modalNuevo) {
                modalNuevo.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('NITProveedores').value = '';
                    document.getElementById('nombreProveedor').value = '';
                    document.getElementById('telefonoProveedor').value = '';
                    document.getElementById('correoProveedor').value = '';
                });
            }
            // Configurar modal de eliminación
            const modalEliminar = document.getElementById('modalEliminarProveedor');
            modalEliminar.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                document.getElementById('formEliminarProveedor').action = `/proveedores/${id}`;
            });
        });
    </script>
@endsection
>>>>>>> 1992225baf11169504a8d35174321996067799e9
