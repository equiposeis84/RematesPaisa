@extends('layouts.usuario')

@section('title', 'Iniciar Sesión - Remates El Paísa')

@section('content')


<form method="POST" action="{{ route('register.post') }}">
    @csrf

    <input type="hidden" name="idRol" value="2">

    <div class="form-group">
        <label for="NombreEmpresa">Nombre Empresa</label>
        <input type="text" name="NombreEmpresa" required>
    </div>

    <div class="form-group">
        <label for="NitEmpresa">NIT Empresa</label>
        <input type="text" name="NITEmpresa" required>
    </div>

    <div class="form-group">
        <label for="nombre">Nombre de usuario (para login)</label>
        <input type="text" id="nombre" name="nombre" required>
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
        <label for="emailCliente">Correo Electrónico (cliente)</label>
        <input type="email" id="emailCliente" name="emailCliente" required>
    </div>

    <div class="form-group">
        <label for="email">Correo para login</label>
        <input type="email" id="email" name="email" required>
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
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmar Contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
    </div>

    <button type="submit" class="btn">Registrarse</button>
</form>

@endsection