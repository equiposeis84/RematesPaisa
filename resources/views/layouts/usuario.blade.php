@extends('layouts.usuario')


@section('content')

<style>
    .auth-wrapper {
        width: 100%;
        min-height: calc(100vh - 60px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    .auth-card {
        width: 420px;
    }

    
</style>

<div class="auth-wrapper">

    <div class="auth-card">

        {{-- PEGAR AQUÍ TU FORMULARIO COMPLETO SIN CAMBIAR NI UNA LÍNEA --}}

        <div class="register-card">
            <div class="card-header">
                <h2><i class="fas fa-user-plus"></i> Crear Cuenta</h2>
                <p>Regístrate para continuar</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="input-group">
                    <label for="nombre">
                        <i class="fas fa-user"></i> Nombre Completo
                    </label>
                    <input id="nombre" type="text" name="name" value="{{ old('name') }}" required autofocus>
                </div>

                <div class="input-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="input-group">
                    <label for="password">
                        <i class="fas fa-key"></i> Contraseña
                    </label>
                    <input id="password" type="password" name="password" required>
                </div>

                <div class="input-group">
                    <label for="password_confirmation">
                        <i class="fas fa-key"></i> Confirmar Contraseña
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                </div>

                <div class="terms">
                    <input type="checkbox" required>
                    <label>Acepto los <a href="#">Términos y Condiciones</a></label>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>

                <div class="footer-links">
                    <p>¿Ya tienes una cuenta?</p>
                    <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Inicia Sesión aquí</a>
                </div>

            </form>
        </div>

    </div>

</div>

@endsection
