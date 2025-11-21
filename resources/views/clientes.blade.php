@extends('welcome')
@section('title', 'Clientes')
@section('content')
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3>Modulo Clientes</h3>
                
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
                <form name="clientes" action="{{ url('/clientes') }}" method="GET">
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
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-6 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="{{ url('/clientes') }}" class="btn btn-warning"><i class="fas fa-list"></i> Reset</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla clientes -->
                @if($datos->count() > 0)
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Cliente</th>
                            <th>Nombre Empresa</th>
                            <th>Tipo Documento</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datos as $item)
                            <tr>
                                <td>{{ $item->idCliente }}</td> 
                                <td>{{ $item->NombreEmpresa }}</td>
                                <td>{{ $item->tipoDocumentoCliente }}</td>  
                                <td>{{ $item->nombreCliente }}</td>
                                <td>{{ $item->apellidoCliente }}</td>  
                                <td>{{ $item->direccionCliente }}</td>
                                <td>{{ $item->telefonoCliente }}</td>
                                <td>{{ $item->emailCliente }}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarCliente"
                                            data-id="{{ $item->idCliente }}"
                                            data-empresa="{{ $item->NombreEmpresa }}"
                                            data-tipodoc="{{ $item->tipoDocumentoCliente }}"
                                            data-nombre="{{ $item->nombreCliente }}"
                                            data-apellido="{{ $item->apellidoCliente }}"
                                            data-direccion="{{ $item->direccionCliente }}"
                                            data-telefono="{{ $item->telefonoCliente }}"
                                            data-email="{{ $item->emailCliente }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDeleteCliente"
                                            data-id="{{ $item->idCliente }}">
                                        <i class="fa-solid fa-trash-can"></i> Eliminar
                                    </button>

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
                        No se encontraron clientes con "{{ request('search') }}"
                    @else
                        No hay clientes registrados.
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div> <!-- Fin del container -->

    <!-- Modal para Nuevo Cliente -->
    <div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalClienteLabel">Nuevo Cliente</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- CAMPOS PRINCIPALES -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="NombreEmpresa" class="form-label">Nombre Empresa *</label>
                                    <input type="text" class="form-control" id="NombreEmpresa" name="NombreEmpresa" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idCliente" class="form-label">ID Cliente *</label>
                                    <input type="text" class="form-control" id="idCliente" name="idCliente" required 
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
                                        <option value="Cédula">Cédula</option>
                                        <option value="Pasaporte">Pasaporte</option>
                                        <option value="RUC">RUC</option>
                                        <option value="DNI">DNI</option>
                                        <option value="NIT">NIT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefonoCliente" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control" id="telefonoCliente" name="telefonoCliente" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombreCliente" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="nombreCliente" name="nombreCliente" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellidoCliente" class="form-label">Apellido *</label>
                                    <input type="text" class="form-control" id="apellidoCliente" name="apellidoCliente" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="emailCliente" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="emailCliente" name="emailCliente" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="direccionCliente" class="form-label">Dirección *</label>
                                    <input type="text" class="form-control" id="direccionCliente" name="direccionCliente" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cliente</button>
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
                                <h1 class="modal-title fs-5" id="modalDeleteClienteLabel">Eliminar Cliente</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="formEliminarCliente" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-body">
                                    <p>¿Está seguro de que desea eliminar este cliente?</p>
                                    <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger">Eliminar Cliente</button>
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
                    <h1 class="modal-title fs-5" id="modalEditarClienteLabel">Editar Cliente</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarCliente" method="POST">
                    @csrf
                    @method('PUT')
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
                                    <label for="edit_idCliente" class="form-label">ID Cliente *</label>
                                    <input type="text" class="form-control" id="edit_idCliente" name="idCliente" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tipoDocumentoCliente" class="form-label">Tipo Documento *</label>
                                    <select class="form-select" id="edit_tipoDocumentoCliente" name="tipoDocumentoCliente" required>
                                        <option value="Cédula">Cédula</option>
                                        <option value="Pasaporte">Pasaporte</option>
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
                        <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
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
            });
        }

        // Configurar modal de eliminación
        const modalDelete = document.getElementById('modalDeleteCliente');
        if (modalDelete) {
            modalDelete.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                
                // Actualizar el formulario de eliminación
                document.getElementById('formEliminarCliente').action = `/clientes/${id}`;
            });
        }
    }); 
</script>
@endsection