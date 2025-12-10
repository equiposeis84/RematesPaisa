<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remates El Paísa - Administración</title>
    <link rel="stylesheet" href="/css/MainContent.css">
    <link rel="stylesheet" href="/css/SidebarStyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>Remates El Paísa</h1>
            </div>
            
            <div class="este">
                <nav class="main-nav">
                    <ul>
                        <li class="sidebar-fixed">
                            <a href="{{ route('welcome') }}">
                                <span class="nav-icon"><i class="fa-solid fa-house"></i></span>
                                Inicio
                            </a>
                        </li>
                        
                        <li class="has-submenu sidebar-fixed">
                            <button class="submenu-toggle" aria-expanded="false" aria-controls="gestion-submenu">
                                <span class="nav-icon"><i class="fa-solid fa-gear"></i></span>
                                Gestión
                                <span class="caret"><i class="fa-solid fa-circle-chevron-down"></i></span>
                            </button>
                            <ul id="gestion-submenu" class="submenu" show>
                                <li>
                                   <a href="{{route('clientes.index')}}" data-view="">
                                        <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                                        Usuarios
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('productos.index')}}" data-view="">
                                        <span class="nav-icon"><i class="fa-solid fa-box-archive"></i></span>
                                        Productos
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('pedidos.index')}}" data-view="">
                                        <span class="nav-icon"><i class="fa-solid fa-bag-shopping"></i></span>
                                        Pedidos
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('proveedores.index')}}" data-view="">
                                        <span class="nav-icon"><i class="fa-solid fa-truck-fast"></i></span>
                                        Proveedores
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('roles.index')}}" data-view="">
                                        <span class="nav-icon"><i class="fa-solid fa-user-shield"></i></span>
                                        Roles y Permisos
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <div class="space">
                <div class="space-item"></div>
            </div>

            <div class="user-section">
                <div class="user-info">
                    <p><strong>Conectado como: Admin General</strong></p>
                </div>

                <nav class="secondary-nav">
                    <ul>
                        <li>
                            <a href="{{route('AyudaContacto.index')}}" data-view="">
                                <span class="nav-icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                                <span class="nav-text">Ayuda y contacto</span>
                            </a>
                        </li>

                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                                @csrf
                                <button type="submit" class="logout-btn" style="background: none; border: none; color: inherit; cursor: pointer; width: 100%; text-align: left;">
                                    <span class="nav-icon">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </span>
                                    <span class="nav-text">Cerrar Sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL-->
        <main class="main-content">
            <section class="content-cards">
                @yield('content')
            </section>
        </main>
    </div>

    <script>
        // Simple submenu toggle (works without external JS)
        (function(){
            document.querySelectorAll('.submenu-toggle').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var parent = btn.closest('.has-submenu');
                    var submenu = parent.querySelector('.submenu');
                    var expanded = btn.getAttribute('aria-expanded') === 'true';
                    
                    if (expanded) {
                        btn.setAttribute('aria-expanded','false');
                        submenu.hidden = true;
                        parent.classList.remove('open');
                    } else {
                        btn.setAttribute('aria-expanded','true');
                        submenu.hidden = false;
                        parent.classList.add('open');
                    }
                });
            });
        })();
    </script>

    <script src="js/view-loader.js"></script>
    <script src="js/main.js"></script>
</body>
</html>