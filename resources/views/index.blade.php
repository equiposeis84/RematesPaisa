<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remates El Paísa</title>
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
            <div class="logo"><h1>Remates El Paísa</h1></div>
            <div class="este">
                <nav class="main-nav">
                    <ul>
                        <li class="sidebar-fixed">
                            <a href="{{ route('catalogo') }}"> 
                                <span class="nav-icon"> <i class="fa-solid fa-store"></i> </span>Catálogo 
                            </a>
                        </li>
                        
                        <li class="sidebar-fixed">
                            <a href="{{ route('usuario.carrito') }}"> 
                                <span class="nav-icon"> <i class="fa-solid fa-cart-shopping"></i> </span>Mi Carrito 
                            </a>
                        </li>
                        
                        <li class="sidebar-fixed">
                            @if(session()->has('user_id') && session('user_type') != 1)
                                <a href="{{ route('usuario.pedidos') }}"> 
                                    <span class="nav-icon"> <i class="fa-solid fa-clipboard-list"></i> </span>Mis Pedidos 
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-warning"> 
                                    <span class="nav-icon"> <i class="fa-solid fa-clipboard-list"></i> </span>Mis Pedidos 
                                    <small class="d-block text-muted">(Inicia sesión)</small>
                                </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
            
            <div class="space">
                <div class="space-item"></div>
            </div>

            <div class="user-section">
                <div class="user-info">
                    @if(session()->has('user_id'))
                        <p><strong>{{ session('user_name') }}</strong></p>
                        <p style="color: #777; font-size: 12px;">{{ session('user_email') }}</p>
                        <small class="badge bg-info">
                            @if(session('user_type') == 1)
                                Administrador
                            @elseif(session('user_type') == 2)
                                Cliente
                            @elseif(session('user_type') == 3)
                                Repartidor
                            @endif
                        </small>
                    @else
                        <p><strong>Invitado</strong></p>
                        <p style="color: #777; font-size: 12px;">Explora nuestro catálogo</p>
                    @endif
                </div>

                <nav class="secondary-nav">
                    <ul>
                        <li>
                            <a href="{{ route('usuario.ayuda.contacto') }}">
                                <span class="nav-icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                                <span class="nav-text">Ayuda y contacto</span>
                            </a>
                        </li>

                        <li>
                            @if(session()->has('user_id'))
                                <!-- FORMULARIO DE LOGOUT -->
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                        <span class="nav-icon">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                        </span>
                                        <span class="nav-text">Cerrar Sesión</span>
                                    </a>
                                </form>
                            @else
                                <a href="{{ route('login') }}">
                                    <span class="nav-icon">
                                        <i class="fa-solid fa-right-to-bracket"></i>
                                    </span>
                                    <span class="nav-text">Iniciar Sesión</span>
                                </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content">
            <section class="content-cards">
                <!-- AQUÍ VA EL CONTENIDO DINÁMICO -->
                @yield('content')
            </section>
        </main>
    </div>

    @yield('scripts')
</body>
</html>