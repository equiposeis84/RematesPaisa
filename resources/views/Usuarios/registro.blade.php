{{--

@extends('layouts.usuario')

@section('title', 'Registro - Remates El Paisa')

@section('content')
<div class="form-container">
    <h1>Crear Cuenta</h1>
    <form action="#" method="post">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
        </div>
        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" placeholder="Apellido" required>
        </div>
        <div class="form-group">
            <label for="tipoDocumento">Tipo de Documento</label>
            <select id="tipoDocumento" name="tipoDocumento" required>
                <option value="">Selecciona</option>
                <option value="cc">Cédula de Ciudadanía</option>
                <option value="ti">Tarjeta de Identidad</option>
                <option value="ce">Cédula de Extranjería</option>
            </select>
        </div>

        <div class="form-group">
            <label for="documento">Número de Documento</label>
            <input type="text" id="documento" name="documento" required>
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" placeholder="tu@correo.com" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" required>
        </div>

        <div class="form-group">
            <label for="empresa">¿Tiene asociación con empresa?</label>
            <select id="empresa" name="empresa" required>
                <option value="">Selecciona</option>
                <option value="no">No tengo asociación con empresa</option>
                <option value="si">Sí tengo asociación con empresa</option>
            </select>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Crea una contraseña segura" required>
        </div>
        <div class="form-group">
            <label for="confirm-password">Confirmar Contraseña</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Repite la contraseña" required>
        </div>
        <button type="submit" class="btn">Registrarse</button>
    </form>
    <div class="form-links">
        <a href="/auth/login">¿Ya tienes cuenta? Inicia Sesión</a>
    </div>
</div>
@endsection
--}}@extends('layouts.usuario')

@section('title', 'Registro - Remates El Paisa')

@section('content')
<div class="form-container">
    <h1>Crear Cuenta</h1>

    <form method="POST" action="{{ route('clientes.store') }}">
        @csrf
                <div class="form-group">
                <label for="NombreEmpresa">Nombre Empresa</label>   
               <input type="text" name="nombreEmpresa" required>

        </div>
        <div class="form-group">
            <label for="nombreUsuario">Nombre de Usuario</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" required>
        </div>
        <!-- Tipo de documento -->
        <div class="form-group">
            <label for="tipoDocumentoCliente">Tipo de Documento</label>
            <select id="tipoDocumentoCliente" name="tipoDocumentoCliente" required>
                <option value="">Selecciona</option>
                <option value="CC">Cédula de Ciudadanía</option>
                <option value="TI">Tarjeta de Identidad</option>
                <option value="CE">Cédula de Extranjería</option>
            </select>
        </div>

        <!-- Número de documento -->
        <div class="form-group">
            <label for="idCliente">Número de Documento</label>
            <input type="text" id="idCliente" name="idCliente" value="{{ old('idCliente') }}" required>
        </div>

        <!-- Nombre y apellido -->
        <div class="form-group">
            <label for="nombreCliente">Nombre</label>
            <input type="text" id="nombreCliente" name="nombreCliente" value="{{ old('nombreCliente') }}" required>
        </div>

        <div class="form-group">
            <label for="apellidoCliente">Apellido</label>
            <input type="text" id="apellidoCliente" name="apellidoCliente" value="{{ old('apellidoCliente') }}" required>
        </div>

        <!-- Contacto -->
        <div class="form-group">
            <label for="emailCliente">Correo Electrónico</label>
            <input type="email" id="emailCliente" name="emailCliente" value="{{ old('emailCliente') }}" required>
        </div>

        <div class="form-group">
            <label for="telefonoCliente">Teléfono</label>
            <input type="text" id="telefonoCliente" name="telefonoCliente" value="{{ old('telefonoCliente') }}" required>
        </div>

        <div class="form-group">
            <label for="direccionCliente">Dirección</label>
            <input type="text" id="direccionCliente" name="direccionCliente" value="{{ old('direccionCliente') }}" required>
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

    <div class="form-links">
        <a href="/auth/login">¿Ya tienes cuenta? Inicia Sesión</a>
    </div>
</div>
@endsection
