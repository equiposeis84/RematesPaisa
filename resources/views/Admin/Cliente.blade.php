
(function(){
// Estructura base para la vista de Clientes
document.write(`<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Clientes - Remates El Paísa</title>
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="dashboard-container">
		<aside class="sidebar">
			<!DOCTYPE html>
			<html lang="es">
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>Clientes - Remates El Paísa</title>
				<link rel="stylesheet" href="../css/style.css">
			</head>
			<body>
				<div class="dashboard-container">
					<aside class="sidebar">
						<div class="logo"><h1>Remates El Paísa</h1></div>
						<nav class="main-nav">
							<ul>
								<!-- Icon placement: replace the emoji with your image using <img class="icon-img" src="../img/icons/mi-icono.svg"> -->
								<li><a href="../inicioA.html"><span class="nav-icon">🏠</span>Inicio</a></li>
								<li><a href="Catalogo.html"><span class="nav-icon">📦</span>Catálogo</a></li>
								<li class="has-submenu">
									<button class="submenu-toggle" aria-expanded="false">⚙️ Gestión <span class="caret">▾</span></button>
									<ul class="submenu" hidden>
										<li><a href="inventario.html">Inventario</a></li>
										<li><a href="Pedidos.html">Pedidos</a></li>
										<li><a href="Usuarios.html">Usuarios</a></li>
									</ul>
								</li>
							</ul>
						</nav>
						<div class="user-section"><div class="user-info"><p><strong>Conectado como: Admin General</strong></p></div></div>
					</aside>

					<main class="main-content">
						<header class="content-header">
							<h2>Gestión de Clientes</h2>
							<p>Administra la información de tus clientes aquí.</p>
						</header>

						<div class="table-container">
							<table>
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Email</th>
										<th>Teléfono</th>
										<th>Estado</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Ejemplo Cliente</td>
										<td>cliente@ejemplo.com</td>
										<td>3001234567</td>
										<td>Activo</td>
										<td>
											<button class="btn btn-sm btn-edit">✏️ Editar</button>
											<button class="btn btn-sm btn-danger">🗑️ Eliminar</button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</main>
				</div>

				<script>
				(function(){
					document.querySelectorAll('.submenu-toggle').forEach(function(btn){
						btn.addEventListener('click', function(){
							var li = btn.closest('.has-submenu');
							if(!li) return;
							var submenu = li.querySelector('.submenu');
							var open = !li.classList.contains('open');
							li.classList.toggle('open', open);
							btn.setAttribute('aria-expanded', open);
							if(submenu) submenu.hidden = !open;
						});
					});
				})();
				</script>

				<script src="../js/main.js"></script>
			</body>
			</html>
					</nav>
					<div class="user-section">
						<div class="user-info">
							<p><strong>Conectado como: Admin General</strong></p>
						</div>
						<nav class="secondary-nav">
							<ul>
								<li><a href="#">Ayuda y Contacto</a></li>
								<li><a href="#">Cerrar Sesión</a></li>
							</ul>
						</nav>
					</div>
				</aside>
										<li><a href="inventario.html"><span class="nav-icon">✔️</span>Inventario</a></li>
										<li><a href="Pedidos.html"><span class="nav-icon">✉️</span>Pedidos</a></li>
										<li><a href="Usuarios.html"><span class="nav-icon">👥</span>Usuarios</a></li>
										<li><a href="Reporter.html"><span class="nav-icon">📊</span>Reportes</a></li>
										<li><a href="Cliente.html"><span class="nav-icon">🙍</span>Clientes</a></li>
										<li><a href="Repartidores.html"><span class="nav-icon">🚚</span>Repartidores</a></li>
									</ul>
								</li>
							</ul>
						</nav>
						<div class="user-section">
							<div class="user-info">
								<p><strong>Conectado como: Admin General</strong></p>
							</div>
							<nav class="secondary-nav">
								<ul>
									<li><a href="#">Ayuda y Contacto</a></li>
									<li><a href="#">Cerrar Sesión</a></li>
								</ul>
							</nav>
						</div>
					</aside>

					<main class="main-content">
						<header class="content-header">
							<h2>Gestión de Clientes</h2>
							<p>Administra la información de tus clientes aquí.</p>
						</header>
						<div class="table-container">
							<table>
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Email</th>
										<th>Teléfono</th>
										<th>Estado</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Ejemplo Cliente</td>
										<td>cliente@ejemplo.com</td>
										<td>3001234567</td>
										<td>Activo</td>
										<td>
											<button class="btn btn-sm btn-edit">✏️ Editar</button>
											<button class="btn btn-sm btn-danger">🗑️ Eliminar</button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</main>
				</div>

				<script src="../js/main.js"></script>
			</body>
			</html>
