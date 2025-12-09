<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remates El Paísa </title>
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
                            <a href="#"> 
                                <span class="nav-icon"> <i class="fa-solid fa-store"></i> </span>Catálogo 
                            </a>
                        </li>
                        
                        <li class="sidebar-fixed">
                            <a href="#"> 
                                <span class="nav-icon"> <i class="fa-solid fa-cart-shopping"></i> </span>Mi Carrito 
                            </a>
                        </li>
                        
                        <li class="sidebar-fixed">
                            <a href="#"> 
                                <span class="nav-icon"> <i class="fa-solid fa-clipboard-list"></i> </span>Mis Pedidos 
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <div class="space">
                <div class="space-item"></div>
            </div>

            <div class="user-section">
                <div class="user-info">
                    <p><strong>Invitado</strong></p>
                </div>

                <nav class="secondary-nav">
                    <ul>
                        <li>
                            <a href="#">
                                <span class="nav-icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                                <span class="nav-text">Ayuda y contacto</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('login') }}">
                                <span class="nav-icon">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                </span>
                                <span class="nav-text">Iniciar Sesión</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content">
            <section class="content-cards">
                <!-- CONTENIDO DEL CATÁLOGO AQUÍ -->
                <div class="container mt-4">
                    <h2>Catálogo de Productos</h2>
                    <p>Contenido del catálogo irá aquí...</p>
                    
                    <!-- SECCIÓN PRODUCTOS -->
                    <div class="row" id="productos">
                        <!-- Los productos se cargarán aquí -->
                    </div>
                    
                    <!-- SECCIÓN OFERTAS -->
                    <div class="row mt-5" id="ofertas">
                        <!-- Las ofertas se cargarán aquí -->
                    </div>
                    
                    <!-- SECCIÓN CATEGORÍAS -->
                    <div class="row mt-5" id="categorias">
                        <!-- Las categorías se cargarán aquí -->
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
    // Simple submenu toggle
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
</body>
</html>