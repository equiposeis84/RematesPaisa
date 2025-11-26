@extends('layouts.usuario')

@section('title', 'Iniciar Sesión - Remates El Paísa')

@section('content')
<div class="form-container">
    <h1>Iniciar Sesión</h1>
    
    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="form-group">
            <label for="nombreUsuario">Nombre de Usuario</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" placeholder="usuario123" required>
        </div>

        <div class="form-group">
            <label for="passwordUsuario">Contraseña</label>
            <input type="password" id="passwordUsuario" name="passwordUsuario" placeholder="••••••••" required>
        </div>

        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <button type="submit" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="btn-icon bi bi-people" viewBox="0 0 16 16"></svg>
            Ingresar
        </button>
    </form>

    <div class="form-links">
        <a href="#">¿Olvidaste tu contraseña?</a>
        <br><br>
        <a href="{{ route('auth.register') }}">¿No tienes cuenta? Regístrate</a>
    </div>
</div>
@endsection
