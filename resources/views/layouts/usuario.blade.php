(!-- Layout base para vistas de usuario: incluye sidebar y yield para contenido --)
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'Remates El Paísa')</title>
	<link rel="stylesheet" href="{{ asset('css/MainContent.css') }}">
	<link rel="stylesheet" href="{{ asset('css/SidebarStyle.css') }}">
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
						<li class="sidebar-fixed"><a href="/">Catálogo</a></li>
						<li class="sidebar-fixed"><a href="#">Mi Carrito</a></li>
						<li class="sidebar-fixed"><a href="#">Mis Pedidos</a></li>
					</ul>
				</nav>
			</div>

			<div class="space"><div class="space-item"></div></div>

			<div class="user-section">
				<div class="user-info">
					@if(session()->has('user_id'))
						<p><strong>{{ session('user_name') }}</strong></p>
						<p style="color: #777; font-size: 12px;">{{ session('user_email') }}</p>
					@else
						<p><strong>Invitado</strong></p>
					@endif
				</div>

				<nav class="secondary-nav">
					<ul>
						<li><a href="#">Ayuda y contacto</a></li>
						<li>
							@if(session()->has('user_id'))
								<form action="{{ route('logout') }}" method="POST" style="display:inline">@csrf
									<a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar Sesión</a>
								</form>
							@else
								<a href="{{ route('login') }}">Iniciar Sesión</a>
							@endif
						</li>
					</ul>
				</nav>
			</div>
		</aside>

		<main class="main-content">
			<section class="content-cards">
				@yield('content')
			</section>
		</main>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
	@yield('scripts')
</body>
</html>
