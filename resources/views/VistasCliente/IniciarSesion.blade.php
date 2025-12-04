@extends('layouts.usuario')

@section('title', 'Iniciar Sesión - Remates El Paísa')

@section('content')
<div class="form-container">
    <h1>Iniciar Sesión</h1>
    
    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email">
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password">
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
