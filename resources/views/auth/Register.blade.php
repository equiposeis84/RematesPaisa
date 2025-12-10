@extends('index')

@section('content')

<style>
    /* He mantenido tus estilos originales, pero scopeados dentro de .auth-wrapper
       para no sobrescribir el <body> del layout (así sidebar sigue visible) */
    .auth-wrapper {
        background: linear-gradient(135deg, rgb(255, 255, 255) 0%, rgb(255, 255, 255) 100%);
        min-height: calc(100vh - 0px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        width: 100%;
    }

    .register-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
    }
    .register-header {
        background: #626262ff;
        color: white;
        padding: 30px;
        text-align: center;
    }
    .register-body {
        padding: 30px;
    }
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #e3e6f0;
    }
    .form-control:focus {
        border-color: #000000ff;
        box-shadow: 0 0 0 0.2rem rgba(245, 87, 108, 0.25);
    }
    .btn-register {
        background: #000000ff;
        border: none;
        color: white;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }
    .btn-register:hover {
        background: #1d00f9ff;
        transform: translateY(-2px);
    }
    .login-link {
        color: #1500ffff;
        text-decoration: none;
        font-weight: 500;
    }
    .login-link:hover {
        text-decoration: underline;
    }
    .password-strength {
        height: 5px;
        border-radius: 3px;
        margin-top: 5px;
        transition: all 0.3s;
    }
    .strength-weak { background: #dc3545; width: 30%; }
    .strength-medium { background: #ffc107; width: 60%; }
    .strength-strong { background: #28a745; width: 100%; }

    /* pequeños ajustes para que encaje con el layout/index */
    .register-card { margin: 10px 0; }
    .invalid-feedback { display: block; }
</style>

<div class="auth-wrapper">
    <div class="register-card">
        <div class="register-header">
            <h2><i class="fas fa-user-plus"></i> Crear Cuenta</h2>
            <p class="mb-0">Regístrate como cliente</p>
        </div>
        
        <div class="register-body">
            <!-- Mostrar mensajes -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                @csrf
                
                <div class="mb-3">
                    <label for="nombre" class="form-label">
                        <i class="fas fa-user"></i> Nombre Completo
                    </label>
                    <input type="text" 
                           class="form-control @error('nombre') is-invalid @enderror" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre') }}"
                           placeholder="Juan Pérez"
                           required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           placeholder="juan@ejemplo.com"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Usaremos este email para contactarte</small>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-key"></i> Contraseña
                    </label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="••••••••"
                           required
                           onkeyup="checkPasswordStrength(this.value)">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="passwordStrength" class="password-strength"></div>
                    <small class="form-text text-muted">Mínimo 6 caracteres</small>
                </div>
                
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-key"></i> Confirmar Contraseña
                    </label>
                    <input type="password" 
                           class="form-control" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           placeholder="••••••••"
                           required>
                    <small id="passwordMatch" class="form-text"></small>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="terms" 
                           name="terms" 
                           required>
                    <label class="form-check-label" for="terms">
                        Acepto los <a href="#" class="login-link">Términos y Condiciones</a>
                    </label>
                </div>
                
                <div class="d-grid mb-3">
                    <button type="submit" class="btn-register" id="submitBtn">
                        <i class="fas fa-user-plus"></i> Registrarse
                    </button>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <p class="mb-0">¿Ya tienes una cuenta?</p>
                    <a href="{{ route('login') }}" class="login-link">
                        <i class="fas fa-sign-in-alt"></i> Inicia Sesión aquí
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <!-- Bootstrap JS (mantener si tu layout no lo incluye, si ya lo incluye puedes quitarlo) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-cerrar alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Verificar fortaleza de contraseña
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength';
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        }
        
        // Verificar que las contraseñas coincidan
        document.getElementById('password_confirmation').addEventListener('keyup', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirm === '') {
                matchText.textContent = '';
                matchText.className = 'form-text';
            } else if (password === confirm) {
                matchText.textContent = '✓ Las contraseñas coinciden';
                matchText.className = 'form-text text-success';
            } else {
                matchText.textContent = '✗ Las contraseñas no coinciden';
                matchText.className = 'form-text text-danger';
            }
        });
        
        // Validación antes de enviar
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const terms = document.getElementById('terms').checked;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Debes aceptar los términos y condiciones');
                return false;
            }
            
            return true;
        });
        //asdasdasd
    </script>
@endsection
 
