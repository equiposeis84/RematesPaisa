@extends('VistasAdmin.welcome')
@section('title', 'Autenticación de Usuarios')
@section('content')
    <div class="container-sm d-flex justify-content-center mt-5">
        <div class="card">
            <div class="card-body" style="width: 1200px;">
                <h3><i class="fas fa-user-shield me-2"></i> Módulo de Autenticación de Usuarios</h3>
                
                <!-- Mensajes de alerta -->
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
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Información:</strong> Este módulo muestra las contraseñas encriptadas de los usuarios y permite verificar autenticación. 
                    <span class="text-danger"><strong>ADVERTENCIA:</strong> Solo administradores pueden acceder.</span>
                </div>
                
                <hr>

                <!-- Herramientas de verificación -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-key me-2"></i> Herramientas de Verificación
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Generar Hash de Contraseña</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="passwordParaHash" placeholder="Contraseña a encriptar">
                                                <button class="btn btn-outline-primary" onclick="generarHash()">
                                                    <i class="fas fa-hashtag"></i> Generar
                                                </button>
                                            </div>
                                            <div id="hashResultado" class="mt-2" style="display: none;">
                                                <small>Hash generado: <code id="hashGenerado"></code></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Verificar Contraseña Común</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="passwordGlobal" placeholder="Verificar en todos">
                                                <button class="btn btn-outline-warning" onclick="verificarGlobal()">
                                                    <i class="fas fa-search"></i> Verificar
                                                </button>
                                            </div>
                                            <div id="globalResultado" class="mt-2" style="display: none;">
                                                <small>Resultado: <span id="globalResultadoText"></span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <i class="fas fa-chart-pie me-2"></i> Estadísticas
                            </div>
                            <div class="card-body text-center">
                                <h4>{{ $usuarios->total() }}</h4>
                                <p class="mb-0">Usuarios Registrados</p>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <span class="badge bg-danger p-2">Admin: {{ $usuarios->where('idRol', 1)->count() }}</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="badge bg-success p-2">Clientes: {{ $usuarios->where('idRol', 2)->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de búsqueda -->
                <form action="{{ route('usuarios.auth.index') }}" method="GET" class="mb-4">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" 
                                       placeholder="Buscar por nombre o email" 
                                       name="search"
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                           <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                           <a href="{{ route('usuarios.auth.index') }}" class="btn btn-warning"><i class="fas fa-list"></i> Mostrar Todos</a>
                        </div>
                    </div>
                </form>
                
                <!-- Tabla de usuarios -->
                @if($usuarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Contraseña Encriptada</th>
                                <th>Verificar Contraseña</th>
                                <th>Estado Autenticación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                                <tr id="usuario-{{ $usuario->idUsuario }}">
                                    <td>
                                        <span class="badge bg-primary">#{{ $usuario->idUsuario }}</span>
                                    </td> 
                                    <td>
                                        <strong>{{ $usuario->nombre }}</strong>
                                    </td>  
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($usuario->idRol == 1) bg-danger
                                            @elseif($usuario->idRol == 2) bg-success
                                            @elseif($usuario->idRol == 3) bg-warning
                                            @else bg-info @endif">
                                            {{ $usuario->rol ? $usuario->rol->nombreRol : 'Sin rol' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control form-control-sm password-hash" 
                                                   value="{{ $usuario->password }}"
                                                   readonly
                                                   id="hash-{{ $usuario->idUsuario }}">
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="copiarHash('{{ $usuario->idUsuario }}')"
                                                    title="Copiar hash">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">
                                            {{ Str::limit($usuario->password, 40) }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="text" 
                                                   class="form-control password-intento" 
                                                   placeholder="Contraseña a verificar"
                                                   id="intento-{{ $usuario->idUsuario }}">
                                            <button class="btn btn-outline-primary" 
                                                    onclick="verificarPassword('{{ $usuario->idUsuario }}')"
                                                    title="Verificar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="resultado-{{ $usuario->idUsuario }}" class="text-center">
                                            <span class="badge bg-secondary">No verificado</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li class="page-item {{ $usuarios->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $usuarios->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                Atrás
                            </a>
                        </li>

                        @for ($i = 1; $i <= $usuarios->lastPage(); $i++)
                            <li class="page-item {{ $usuarios->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" 
                                   href="{{ $usuarios->url($i) }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                            
                        <li class="page-item {{ !$usuarios->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $usuarios->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="text-muted mt-2">
                    Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuarios
                </div>

                @else
                <div class="alert alert-info text-center mt-3">
                    <i class="fas fa-info-circle"></i> 
                    @if(request('search'))
                        No se encontraron usuarios con "{{ request('search') }}"
                    @else
                        No hay usuarios registrados en el sistema.
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto cerrar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Verificar contraseña individual
        function verificarPassword(idUsuario) {
            const password = document.getElementById('intento-' + idUsuario).value;
            
            if (!password) {
                alert('Por favor ingresa una contraseña para verificar');
                return;
            }
            
            // Mostrar loading
            const resultadoDiv = document.getElementById('resultado-' + idUsuario);
            resultadoDiv.innerHTML = '<span class="badge bg-warning">Verificando...</span>';
            
            // Enviar petición AJAX
            fetch(`/admin/usuarios-auth/${idUsuario}/verificar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    password_intento: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.es_correcta) {
                        resultadoDiv.innerHTML = '<span class="badge bg-success">✓ ' + data.mensaje + '</span>';
                        document.getElementById('usuario-' + idUsuario).classList.add('table-success');
                    } else {
                        resultadoDiv.innerHTML = '<span class="badge bg-danger">✗ ' + data.mensaje + '</span>';
                        document.getElementById('usuario-' + idUsuario).classList.add('table-danger');
                    }
                } else {
                    resultadoDiv.innerHTML = '<span class="badge bg-danger">Error</span>';
                    alert(data.error || 'Error al verificar');
                }
            })
            .catch(error => {
                resultadoDiv.innerHTML = '<span class="badge bg-danger">Error</span>';
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
        }
        
        // Generar hash de contraseña
        function generarHash() {
            const password = document.getElementById('passwordParaHash').value;
            
            if (!password) {
                alert('Por favor ingresa una contraseña');
                return;
            }
            
            fetch(`/admin/usuarios-auth/generar-hash`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('hashGenerado').textContent = data.hash;
                    document.getElementById('hashResultado').style.display = 'block';
                    
                    // Mostrar en un alert también
                    alert(`Hash generado para "${data.password_original}":\n\n${data.hash}`);
                } else {
                    alert('Error al generar hash');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
        }
        
        // Verificar contraseña global
        function verificarGlobal() {
            const password = document.getElementById('passwordGlobal').value;
            
            if (!password) {
                alert('Por favor ingresa una contraseña');
                return;
            }
            
            document.getElementById('globalResultadoText').textContent = 'Verificando...';
            document.getElementById('globalResultado').style.display = 'block';
            
            fetch(`/admin/usuarios-auth/verificar-global`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    password_comun: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const correctos = data.correctos;
                    const total = data.total;
                    const porcentaje = Math.round((correctos / total) * 100);
                    
                    let mensaje = `Verificados: ${correctos}/${total} usuarios (${porcentaje}%)`;
                    if (correctos > 0) {
                        mensaje += ' - Se encontraron coincidencias!';
                    }
                    
                    document.getElementById('globalResultadoText').textContent = mensaje;
                    
                    // Actualizar cada fila individualmente
                    data.resultados.forEach(resultado => {
                        const resultadoDiv = document.getElementById('resultado-' + resultado.id);
                        if (resultado.es_correcta) {
                            resultadoDiv.innerHTML = '<span class="badge bg-success">✓ Coincide</span>';
                            document.getElementById('usuario-' + resultado.id).classList.add('table-success');
                        }
                    });
                    
                    if (correctos > 0) {
                        alert(`Se encontraron ${correctos} usuario(s) con la contraseña "${password}"`);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('globalResultadoText').textContent = 'Error al verificar';
            });
        }
        
        // Copiar hash al portapapeles
        function copiarHash(idUsuario) {
            const hashInput = document.getElementById('hash-' + idUsuario);
            hashInput.select();
            hashInput.setSelectionRange(0, 99999); // Para móviles
            
            navigator.clipboard.writeText(hashInput.value).then(() => {
                // Mostrar mensaje temporal
                const originalText = hashInput.nextElementSibling.innerHTML;
                hashInput.nextElementSibling.innerHTML = '<span class="text-success">✓ Copiado!</span>';
                
                setTimeout(() => {
                    hashInput.nextElementSibling.innerHTML = originalText;
                }, 2000);
            });
        }
        
        // Permitir presionar Enter en los campos de verificación
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.password-intento');
            inputs.forEach(input => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const idUsuario = this.id.replace('intento-', '');
                        verificarPassword(idUsuario);
                    }
                });
            });
            
            document.getElementById('passwordParaHash').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    generarHash();
                }
            });
            
            document.getElementById('passwordGlobal').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    verificarGlobal();
                }
            });
        });
    </script>
@endsection