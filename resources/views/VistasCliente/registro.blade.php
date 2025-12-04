<form method="POST" action="{{ route('clientes.store') }}">
    @csrf

    <input type="hidden" name="idRol" value="2">

    <div class="form-group">
        <label for="NombreEmpresa">Nombre Empresa</label>
        <input type="text" name="nombreEmpresa" required>
    </div>

    <div class="form-group">
        <label for="NitEmpresa">NIT Empresa</label>
        <input type="text" name="nitEmpresa" required>
    </div>

    <div class="form-group">
        <label for="nombreUsuario">Nombre de Usuario</label>
        <input type="text" id="nombreUsuario" name="nombreUsuario" required>
    </div>

    <div class="form-group">
        <label for="tipoDocumentoCliente">Tipo de Documento</label>
        <select id="tipoDocumentoCliente" name="tipoDocumentoCliente" required>
            <option value="">Selecciona</option>
            <option value="CC">Cédula de Ciudadanía</option>
            <option value="TI">Tarjeta de Identidad</option>
            <option value="CE">Cédula de Extranjería</option>
        </select>
    </div>

    <div class="form-group">
        <label for="idCliente">Número de Documento</label>
        <input type="text" id="idCliente" name="idCliente" required>
    </div>

    <div class="form-group">
        <label for="nombreCliente">Nombre</label>
        <input type="text" id="nombreCliente" name="nombreCliente" required>
    </div>

    <div class="form-group">
        <label for="apellidoCliente">Apellido</label>
        <input type="text" id="apellidoCliente" name="apellidoCliente" required>
    </div>

    <div class="form-group">
        <label for="emailCliente">Correo Electrónico</label>
        <input type="email" id="emailCliente" name="emailCliente" required>
    </div>

    <div class="form-group">
        <label for="telefonoCliente">Teléfono</label>
        <input type="text" id="telefonoCliente" name="telefonoCliente" required>
    </div>

    <div class="form-group">
        <label for="direccionCliente">Dirección</label>
        <input type="text" id="direccionCliente" name="direccionCliente" required>
    </div>

    <div class="form-group">
        <label for="passwordUsuario">Contraseña</label>
        <input type="password" id="passwordUsuario" name="passwordUsuario" required>
    </div>

    <div class="form-group">
        <label for="confirm-password">Confirmar Contraseña</label>
        <input type="password" id="confirm-password" name="confirm-password" required>
    </div>

    <button type="submit" class="btn">Registrarse</button>
</form>
